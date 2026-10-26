<?php

namespace App\Http\Livewire\Canteen;

use Livewire\Component;
use App\Models\FingerprintDevice as DeviceModel;
use App\Services\FingerprintService;
use Illuminate\Support\Facades\Log;

class FingerprintDevice extends Component
{
    public $devices = [];
    public $modal = 'device_modal';
    public $is_edit = false;
    public $_id;

    // Form fields
    public $name;
    public $ip_address;
    public $port = 80;
    public $comm_key = '0';
    public $device_id = 1;
    public $timeout = 10;
    public $is_active = true;

    // Status
    public $test_result = '';
    public $test_status = '';
    public $device_status = [];

    protected $rules = [
        'name'       => 'required|string|max:255',
        'ip_address' => 'required|ip',
        'port'       => 'required|integer|min:1|max:65535',
        'comm_key'   => 'nullable|string|max:20',
        'device_id'  => 'required|integer|min:1',
        'timeout'    => 'required|integer|min:3|max:60',
        'is_active'  => 'boolean',
    ];

    protected $messages = [
        'name.required'       => 'Device name is required',
        'ip_address.required' => 'IP Address is required',
        'ip_address.ip'       => 'Invalid IP Address format',
    ];

    public function mount()
    {
        $this->loadDevices();
        $this->checkAllDevicesStatus();
    }

    public function loadDevices()
    {
        $this->devices = DeviceModel::orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function checkAllDevicesStatus()
    {
        $this->device_status = [];
        foreach ($this->devices as $device) {
            $this->device_status[$device->id] = $this->getDeviceStatus($device);
        }
    }

    protected function getDeviceStatus($device): string
    {
        try {
            $service = new FingerprintService(
                $device->ip_address,
                $device->port,
                $device->timeout,
                $device->comm_key ?? '0'
            );
            $s = $service->getDeviceStatus();
            return $s['online'] ? 'Online' : 'Offline';
        } catch (\Exception $e) {
            return 'Error';
        }
    }

    public function openModal()
    {
        $this->is_edit = false;
        $this->resetForm();
        $this->resetErrorBag();
        $this->test_result = '';
        $this->test_status = '';
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function closeModal()
    {
        $this->resetErrorBag();
        $this->test_result = '';
        $this->test_status = '';
        $this->dispatchBrowserEvent('modal-hide', ['modal' => $this->modal]);
    }

    public function resetForm()
    {
        $this->name       = '';
        $this->ip_address = '';
        $this->port       = 80;
        $this->comm_key   = '0';
        $this->device_id  = 1;
        $this->timeout    = 10;
        $this->is_active  = true;
    }

    public function edit($id)
    {
        $device = DeviceModel::findOrFail($id);

        $this->_id        = $device->id;
        $this->name       = $device->name;
        $this->ip_address = $device->ip_address;
        $this->port       = $device->port;
        $this->comm_key   = $device->comm_key ?? '0';
        $this->device_id  = $device->device_id;
        $this->timeout    = $device->timeout;
        $this->is_active  = $device->is_active;

        $this->is_edit     = true;
        $this->test_result = '';
        $this->test_status = '';
        $this->resetErrorBag();
        $this->dispatchBrowserEvent('modal-show', ['modal' => $this->modal]);
    }

    public function submit()
    {
        $this->validate();

        try {
            $data = [
                'name'       => $this->name,
                'ip_address' => $this->ip_address,
                'port'       => $this->port,
                'comm_key'   => $this->comm_key ?? '0',
                'device_id'  => $this->device_id,
                'timeout'    => $this->timeout,
                'is_active'  => $this->is_active,
            ];

            if ($this->is_edit) {
                DeviceModel::findOrFail($this->_id)->update($data);
                session()->flash('success', 'Device updated successfully');
            } else {
                DeviceModel::create($data);
                session()->flash('success', 'Device added successfully');
            }

            $this->loadDevices();
            $this->checkAllDevicesStatus();
            $this->closeModal();

        } catch (\Exception $e) {
            Log::error('Save device error: ' . $e->getMessage());
            session()->flash('error', 'Failed to save device: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            DeviceModel::findOrFail($id)->delete();
            session()->flash('success', 'Device deleted successfully');
            $this->loadDevices();
            $this->checkAllDevicesStatus();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete device');
        }
    }

    public function testConnection()
    {
        $this->validate([
            'ip_address' => 'required|ip',
            'port'       => 'required|integer|min:1|max:65535',
        ]);

        try {
            $service = new FingerprintService(
                $this->ip_address,
                $this->port,
                $this->timeout,
                $this->comm_key ?? '0'
            );

            $result = $service->testConnection();

            if ($result['success']) {
                $this->test_status = 'success';
                $this->test_result = '✓ Connection Successful! Device is reachable.';
            } else {
                $this->test_status = 'error';
                $this->test_result = '✗ ' . $result['message'];
            }
        } catch (\Exception $e) {
            $this->test_status = 'error';
            $this->test_result = '✗ Error: ' . $e->getMessage();
        }
    }

    public function syncNow($id)
    {
        try {
            $device  = DeviceModel::findOrFail($id);
            $service = new FingerprintService(
                $device->ip_address,
                $device->port,
                $device->timeout,
                $device->comm_key ?? '0'
            );

            $result = $service->syncNow();

            session()->flash(
                $result['total_logs'] > 0 ? 'success' : 'info',
                'Sync completed: ' . $result['total_logs'] . ' logs today'
            );

            $this->checkAllDevicesStatus();
        } catch (\Exception $e) {
            session()->flash('error', 'Sync error: ' . $e->getMessage());
        }
    }

    public function toggleActive($id)
    {
        $device            = DeviceModel::findOrFail($id);
        $device->is_active = !$device->is_active;
        $device->save();
        session()->flash('success', 'Device status updated');
        $this->loadDevices();
    }

    public function render()
    {
        return view('livewire.canteen.fingerprint-device')->layoutData([
            'title' => 'Fingerprint Device Settings'
        ]);
    }
}
