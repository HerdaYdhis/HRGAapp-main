<div>
    <x-layouts.card>
        <x-slot:header>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Fingerprint Scan Canteen</h5>
                    <p class="text-muted mb-0">Tap fingerprint or card on the machine to scan.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    {{-- Device status badge --}}
                    <span class="badge {{ $device_online ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-2">
                        <i class="bi {{ $device_online ? 'bi-wifi' : 'bi-wifi-off' }} me-1"></i>
                        {{ $device_name }} &mdash; {{ $device_status }}
                    </span>
                    @if (auth()->user()->is_superadmin)
                        <a href="{{ route('fingerprint-device') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-gear-fill"></i> Settings
                        </a>
                    @endif
                </div>
            </div>
        </x-slot:header>

        <x-slot:body>
            <x-alert.notification />

            <div class="row g-4">
                {{-- Left: Scan status panel --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center py-5">

                            {{-- Fingerprint icon with pulse animation --}}
                            <div class="fingerprint-icon mb-4 {{ $device_online ? 'fingerprint-active' : 'fingerprint-offline' }}">
                                <i class="bi bi-fingerprint" style="font-size: 6rem; {{ $device_online ? 'color: #0d6efd;' : 'color: #6c757d;' }}"></i>
                            </div>

                            @if ($device_online)
                                <p class="text-primary fw-semibold fs-5 mb-1">Device Ready</p>
                                <p class="text-muted">Waiting for fingerprint or card tap...</p>
                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <button wire:click="refreshScan" wire:loading.attr="disabled"
                                        class="btn btn-primary px-4">
                                        <span wire:loading wire:target="refreshScan">
                                            <span class="spinner-border spinner-border-sm me-1"></span>
                                        </span>
                                        <span wire:loading.remove wire:target="refreshScan">
                                            <i class="bi bi-arrow-clockwise me-1"></i>
                                        </span>
                                        Fetch Latest Scan
                                    </button>
                                    <button wire:click="checkDevice" class="btn btn-outline-secondary">
                                        <i class="bi bi-broadcast me-1"></i> Refresh Status
                                    </button>
                                </div>
                            @else
                                <p class="text-danger fw-semibold fs-5 mb-1">Device Offline</p>
                                <p class="text-muted">Cannot connect to fingerprint machine.<br>
                                    <small>IP: {{ $device_ip ?: 'No device configured' }}</small>
                                </p>
                                <button wire:click="checkDevice" class="btn btn-outline-primary mt-2">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Retry Connection
                                </button>
                                @if (auth()->user()->is_superadmin)
                                    <a href="{{ route('fingerprint-device') }}" class="btn btn-warning mt-2 ms-2">
                                        <i class="bi bi-gear me-1"></i> Configure Device
                                    </a>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>

                {{-- Right: Scan result panel --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                            <h6 class="fw-bold text-muted mb-0 text-uppercase" style="letter-spacing:.05em;">Last Scan Result</h6>
                        </div>
                        <div class="card-body px-4">

                            @if ($status === 'Success')
                                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                                    <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                                    <strong>Scan Successful</strong>
                                </div>
                            @elseif ($status === 'Employee Not Found')
                                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                    <i class="bi bi-person-x-fill fs-4 me-2"></i>
                                    <strong>Employee Not Found</strong>
                                </div>
                            @elseif (!empty($status))
                                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
                                    <strong>{{ $status }}</strong>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-person-badge" style="font-size: 3rem; opacity:.3;"></i>
                                    <p class="mt-2 mb-0">No scan result yet.</p>
                                </div>
                            @endif

                            @if (!empty($status))
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted ps-0" style="width:40%">
                                                <i class="bi bi-person me-1"></i> Name
                                            </td>
                                            <td class="fw-semibold">
                                                {{ $name ?: '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">
                                                <i class="bi bi-building me-1"></i> Department
                                            </td>
                                            <td class="fw-semibold">{{ $department ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">
                                                <i class="bi bi-clock me-1"></i> Scan Time
                                            </td>
                                            <td class="fw-semibold">{{ $scan_time ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">
                                                <i class="bi bi-card-text me-1"></i> Employee ID
                                            </td>
                                            <td class="fw-semibold">{{ $employee ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">
                                                <i class="bi bi-toggles me-1"></i> Eat Status
                                            </td>
                                            <td>
                                                @if ($eat === 'yes')
                                                    <span class="badge bg-success">Yes</span>
                                                @elseif ($eat === 'no')
                                                    <span class="badge bg-danger">No</span>
                                                @else
                                                    <span class="badge bg-secondary">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">
                                                <i class="bi bi-layers me-1"></i> Shift
                                            </td>
                                            <td class="fw-semibold">{{ $shift ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">
                                                <i class="bi bi-fingerprint me-1"></i> Method
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">Fingerprint</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            {{-- Scan history table --}}
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Today's Fingerprint Scans</h6>
                        </div>
                        <div class="card-body p-0">
                            <livewire:canteen.datatable-scan />
                        </div>
                    </div>
                </div>
            </div>

        </x-slot:body>
    </x-layouts.card>
</div>

@push('style')
<style>
    @keyframes pulse-fp {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .6; transform: scale(1.08); }
    }
    .fingerprint-active {
        animation: pulse-fp 2.4s ease-in-out infinite;
    }
    .fingerprint-offline {
        opacity: .35;
    }
</style>
@endpush

@push('script')
<script>
    // Auto-refresh every 10 seconds when auto_refresh is on
    document.addEventListener('livewire:load', function () {
        let scanInterval = null;

        function startAutoRefresh() {
            if (scanInterval) return;
            scanInterval = setInterval(function () {
                @this.refreshScan();
            }, 10000);
        }

        function stopAutoRefresh() {
            if (scanInterval) {
                clearInterval(scanInterval);
                scanInterval = null;
            }
        }

        // Only start if device is online
        if (@json($device_online)) {
            startAutoRefresh();
        }

        // Speak result
        Livewire.on('scanResult', function (response) {
            if (response.success) {
                speakText('Scan berhasil, ' + @json($name));
            } else {
                speakText(response.message);
            }
        });

        function speakText(text) {
            if (!window.speechSynthesis) return;
            var utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            speechSynthesis.speak(utterance);
        }
    });
</script>
@endpush
