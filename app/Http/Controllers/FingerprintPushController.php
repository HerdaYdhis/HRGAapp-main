<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Canteen;
use App\Models\FingerprintLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ADMS Push Server untuk mesin ZKTeco / Solution A200
 *
 * Mesin diarahkan ke server ini:
 *   Server Address: http://<IP_SERVER>/iclock
 *
 * Endpoint yang digunakan mesin:
 *   GET  /iclock/cdata          — handshake
 *   POST /iclock/cdata          — kirim attendance records
 *   GET  /iclock/getrequest     — mesin tanya ada command baru tidak
 *   POST /iclock/devicecmd      — mesin kirim hasil command
 */
class FingerprintPushController extends Controller
{
    /**
     * GET /iclock/cdata — Handshake awal dari mesin.
     * Mesin kirim SN (serial number) dan minta opsi konfigurasi.
     */
    public function handshake(Request $request)
    {
        $sn = $request->input('SN', 'UNKNOWN');

        Log::info("[ADMS] Handshake dari mesin SN=$sn | IP=" . $request->ip());

        // Update atau catat device online
        DB::table('fingerprint_devices')
            ->where('name', $sn)
            ->orWhere('ip_address', $request->ip())
            ->update(['is_active' => true]);

        // Response ke mesin: konfigurasi push
        $response = implode("\r\n", [
            "GET OPTION FROM: {$sn}",
            "Stamp=9999",
            "OpStamp=" . time(),
            "ErrorDelay=60",
            "Delay=10",
            "ResLogDay=18250",
            "ResLogDelCount=10000",
            "ResLogCount=100000",
            "TransTimes=00:00;23:59",
            "TransInterval=1",
            "TransFlag=1111000000",
            "Realtime=1",
            "Encrypt=0",
        ]);

        return response($response, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * POST /iclock/cdata — Mesin kirim attendance records.
     *
     * Format data per baris (tab-separated):
     *   PIN  DateTime  Status  Verify  WorkCode  Reserved
     *   Contoh: 00001\t2026-08-05 08:30:45\t0\t1\t0\t\n
     */
    public function receiveRecords(Request $request)
    {
        $sn      = $request->input('SN', 'UNKNOWN');
        $table   = $request->input('table', '');
        $content = $request->getContent();

        Log::info("[ADMS] Records dari mesin SN=$sn table=$table | " . strlen($content) . " bytes");
        Log::debug("[ADMS] Raw content: " . substr($content, 0, 500));

        // Hanya proses tabel ATTLOG (attendance log)
        if (strtoupper($table) !== 'ATTLOG') {
            Log::info("[ADMS] Skipping table=$table");
            return response("OK: 0", 200)->header('Content-Type', 'text/plain');
        }

        $lines     = preg_split('/\r\n|\r|\n/', trim($content));
        $processed = 0;
        $errors    = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Tab-separated: PIN  DateTime  Status  Verify  WorkCode ...
            $parts = preg_split('/\t/', $line);

            if (count($parts) < 2) {
                Log::debug("[ADMS] Skip invalid line: $line");
                continue;
            }

            $pin      = trim($parts[0]);
            $datetime = trim($parts[1]);
            $status   = isset($parts[2]) ? (int) $parts[2] : 0;
            $verify   = isset($parts[3]) ? (int) $parts[3] : 0;
            $workCode = isset($parts[4]) ? (int) $parts[4] : 0;

            if (empty($pin) || empty($datetime)) continue;

            try {
                $scanDatetime = Carbon::parse($datetime);
            } catch (\Exception $e) {
                Log::warning("[ADMS] Invalid datetime: $datetime");
                continue;
            }

            // Simpan ke fingerprint_logs (hindari duplikat)
            $existing = FingerprintLog::where('pin', $pin)
                ->where('scan_datetime', $scanDatetime)
                ->first();

            if ($existing) {
                $processed++;
                continue;
            }

            $log = FingerprintLog::create([
                'device_sn'     => $sn,
                'pin'           => $pin,
                'scan_datetime' => $scanDatetime,
                'status'        => $status,
                'verify'        => $verify,
                'work_code'     => $workCode,
                'processed'     => false,
            ]);

            // Langsung proses ke canteen jika scan hari ini
            if ($scanDatetime->isToday()) {
                $this->processCanteenScan($log);
            }

            $processed++;
        }

        Log::info("[ADMS] Processed $processed records, errors=$errors");

        return response("OK: {$processed}", 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * GET /iclock/getrequest — Mesin tanya ada command baru tidak.
     * Kita jawab "OK" saja (tidak ada command yang antri).
     */
    public function getRequest(Request $request)
    {
        $sn = $request->input('SN', '');
        Log::debug("[ADMS] getrequest dari SN=$sn");
        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * POST /iclock/devicecmd — Mesin kirim hasil eksekusi command.
     */
    public function deviceCmd(Request $request)
    {
        Log::debug("[ADMS] devicecmd: " . $request->getContent());
        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Proses log fingerprint ke tabel canteens.
     */
    protected function processCanteenScan(FingerprintLog $log): void
    {
        try {
            $pin  = $log->pin;
            $time = $log->scan_datetime;

            // Cari user berdasarkan employee_id
            $user = User::where('employee_id', $pin)->first();

            if (!$user) {
                Log::info("[ADMS] Employee not found for PIN=$pin");
                $log->update(['processed' => true]);
                return;
            }

            // Cek apakah terdaftar di canteen hari ini
            $canteen = Canteen::where('employee_id', $pin)
                ->whereDate('created_at', Carbon::today())
                ->first();

            if (!$canteen) {
                Log::info("[ADMS] PIN=$pin tidak terdaftar di canteen hari ini");
                $log->update(['processed' => true]);
                return;
            }

            // Jika dayoff, skip
            if ($canteen->meal_shift_id == 2) {
                Log::info("[ADMS] PIN=$pin dayoff");
                $log->update(['processed' => true]);
                return;
            }

            // Jika sudah scan, skip
            if ($canteen->eat) {
                Log::info("[ADMS] PIN=$pin sudah scan hari ini");
                $log->update(['processed' => true]);
                return;
            }

            // Update canteen
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

            Log::info("[ADMS] Canteen updated untuk {$user->name} (PIN=$pin) jam {$time->format('H:i:s')}");

        } catch (\Exception $e) {
            Log::error("[ADMS] processCanteenScan error: " . $e->getMessage());
        }
    }
}
