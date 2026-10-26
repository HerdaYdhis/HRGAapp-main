<?php

namespace App\Http\Livewire\Canteen;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Canteen;
use App\Models\FingerprintLog;
use App\Models\FingerprintDevice;
use App\Services\FingerprintService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScanFingerprint extends Component
{
    // Hasil scan terakhir
    public $name        = '';
    public $employee    = '';
    public $eat         = '';
    public $department  = '';
    public $shift       = '';
    public $status      = '';
    public $scan_time   = '';
    public $last_log_id = null; // ID log terakhir yang diproses

    // Status mesin
    public $device_status = 'Checking...';
    public $device_online = false;
    public $device_ip     = '';
    public $device_name   = '';

    // Mode: 'push' (ADMS) atau 'pull' (legacy TCP)
    public $mode = 'push';

    protected $listeners = ['checkDevice', 'refreshScan'];

    public function mount()
    {
        $this->checkDevice();
        $this->loadLastScan();
    }

    /**
     * Cek status mesin via HTTP test (port 80/4370).
     */
    public function checkDevice()
    {
        $device = FingerprintDevice::where('is_active', true)->first();

        if (!$device) {
            $this->device_status = 'No Device Configured';
            $this->device_online = false;
            $this->device_name   = 'No Device';
            $this->device_ip     = '-';
            return;
        }

        $this->device_name = $device->name;
        $this->device_ip   = $device->ip_address . ':' . $device->port;

        try {
            $service = new FingerprintService(
                $device->ip_address,
                $device->port,
                min($device->timeout, 5), // max 5 detik untuk check
                $device->comm_key ?? '0'
            );

            $result = $service->testConnection();

            $this->device_online = $result['success'];
            $this->device_status = $result['success'] ? 'Online' : 'Offline';

        } catch (\Exception $e) {
            $this->device_online = false;
            $this->device_status = 'Offline';
        }

        // Cek juga apakah ada push log masuk hari ini
        // Jika ada, mesin dalam mode push dan bisa dikatakan aktif
        $hasPushLog = FingerprintLog::whereDate('scan_datetime', Carbon::today())->exists();
        if ($hasPushLog && !$this->device_online) {
            $this->device_online = true;
            $this->device_status = 'Online (Push Mode)';
        }
    }

    /**
     * Load tampilan hasil scan terakhir hari ini dari fingerprint_logs.
     */
    public function loadLastScan()
    {
        $latestLog = FingerprintLog::whereDate('scan_datetime', Carbon::today())
            ->orderByDesc('scan_datetime')
            ->first();

        if ($latestLog) {
            $this->last_log_id = $latestLog->id;
            $this->displayScanResult($latestLog);
        }
    }

    /**
     * Refresh: cek apakah ada log baru dari mesin sejak last_log_id.
     * Dipanggil oleh tombol "Fetch Latest Scan" atau auto-refresh JS.
     */
    public function refreshScan()
    {
        $query = FingerprintLog::whereDate('scan_datetime', Carbon::today())
            ->orderByDesc('scan_datetime');

        if ($this->last_log_id) {
            $query->where('id', '>', $this->last_log_id);
        }

        $newLog = $query->first();

        if (!$newLog) {
            // Tidak ada log baru
            session()->flash('info', 'No new scan detected. Waiting for fingerprint...');
            return;
        }

        $this->last_log_id = $newLog->id;
        $this->displayScanResult($newLog);

        // Jika belum diproses ke canteen, proses sekarang
        if (!$newLog->processed) {
            $this->processToCanteen($newLog);
        }

        $this->emit('re_render_table');
    }

    /**
     * Tampilkan hasil scan ke UI.
     */
    protected function displayScanResult(FingerprintLog $log): void
    {
        $pin  = $log->pin;
        $user = User::where('employee_id', $pin)->first();

        if (!$user) {
            $this->name       = '';
            $this->employee   = $pin;
            $this->department = '';
            $this->shift      = '';
            $this->eat        = 'no';
            $this->status     = 'Employee Not Found';
            $this->scan_time  = Carbon::parse($log->scan_datetime)->format('H:i:s');
            session()->flash('error', "Employee Not Found: PIN={$pin}");
            $this->emit('scanResult', ['success' => false, 'message' => 'Employee Not Found']);
            return;
        }

        $canteen = Canteen::where('employee_id', $pin)
            ->whereDate('created_at', Carbon::today())
            ->with('meal_shifts')
            ->first();

        $this->name       = $user->name;
        $this->employee   = $pin;
        $this->department = $user->department->name ?? '-';
        $this->shift      = $canteen?->meal_shifts?->shift_name ?? '-';
        $this->scan_time  = Carbon::parse($log->scan_datetime)->format('H:i:s');

        if (!$canteen) {
            $this->eat    = 'no';
            $this->status = 'Not Registered Today';
            session()->flash('error', "Not registered in canteen today: {$user->name}");
            $this->emit('scanResult', ['success' => false, 'message' => 'Not Registered']);
            return;
        }

        if ($canteen->meal_shift_id == 2) {
            $this->eat    = 'no';
            $this->status = 'Day Off';
            session()->flash('error', "{$user->name} is on day off");
            $this->emit('scanResult', ['success' => false, 'message' => 'Day Off']);
            return;
        }

        if ($canteen->eat) {
            $this->eat    = 'yes';
            $this->status = 'Already Scanned';
            session()->flash('info', "{$user->name} already scanned today");
            $this->emit('scanResult', ['success' => true, 'message' => 'Already Scanned']);
            return;
        }

        $this->eat    = 'yes';
        $this->status = 'Success';
        session()->flash('success', "Scan successful: {$user->name}");
        $this->emit('scanResult', ['success' => true, 'message' => 'Scan Successful']);
    }

    /**
     * Proses log ke tabel canteens.
     */
    protected function processToCanteen(FingerprintLog $log): void
    {
        $pin  = $log->pin;
        $time = Carbon::parse($log->scan_datetime);

        $user = User::where('employee_id', $pin)->first();
        if (!$user) {
            $log->update(['processed' => true]);
            return;
        }

        $canteen = Canteen::where('employee_id', $pin)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if (!$canteen || $canteen->meal_shift_id == 2 || $canteen->eat) {
            $log->update(['processed' => true]);
            return;
        }

        DB::table('canteens')
            ->where('employee_id', $pin)
            ->whereDate('created_at', Carbon::today())
            ->update([
                'eat'        => true,
                'scan_time'  => $time->format('H:i:s'),
                'method'     => 'fingerprint',
                'updated_at' => now(),
            ]);

        $log->update(['processed' => true, 'employee_id' => $pin]);
        Log::info("[ScanFP] Canteen updated: {$user->name} jam {$time->format('H:i:s')}");
    }

    public function render()
    {
        return view('livewire.canteen.scan-fingerprint')->layoutData([
            'title' => 'Scan Fingerprint'
        ]);
    }
}
