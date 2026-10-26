<div>
    <x-layouts.card>
        <x-slot:header>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Fingerprint Device Settings</h5>
                    <p class="text-muted mb-0">Manage fingerprint machines connected via TCP/IP.</p>
                </div>
                <div>
                    <button type="button" wire:click="openModal" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Add Device
                    </button>
                </div>
            </div>
        </x-slot:header>

        <x-slot:body>
            <x-alert.notification />

            @if (count($devices) === 0)
                <div class="text-center py-5">
                    <i class="bi bi-fingerprint text-muted" style="font-size:4rem;opacity:.3;"></i>
                    <p class="mt-3 text-muted">No fingerprint device configured yet.</p>
                    <button wire:click="openModal" class="btn btn-primary mt-1">
                        <i class="bi bi-plus-lg me-1"></i> Add First Device
                    </button>
                </div>
            @else
                <div class="row g-4">
                    @foreach ($devices as $device)
                        @php
                            $statusLabel = $device_status[$device->id] ?? 'Unknown';
                            $statusClass = match($statusLabel) {
                                'Online'  => 'success',
                                'Offline' => 'danger',
                                default   => 'secondary',
                            };
                        @endphp
                        <div class="col-md-6 col-xl-4">
                            <div class="card border shadow-sm h-100">
                                <div class="card-body">
                                    {{-- Header row --}}
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="bi bi-fingerprint me-1 text-primary"></i>
                                                {{ $device->name }}
                                            </h6>
                                            <span class="badge bg-{{ $statusClass }}">
                                                <i class="bi bi-circle-fill me-1" style="font-size:.5rem;vertical-align:middle;"></i>
                                                {{ $statusLabel }}
                                            </span>
                                            @if (!$device->is_active)
                                                <span class="badge bg-warning text-dark ms-1">Inactive</span>
                                            @endif
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item" wire:click="edit({{ $device->id }})">
                                                        <i class="bi bi-pencil me-2"></i>Edit
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" wire:click="toggleActive({{ $device->id }})">
                                                        <i class="bi bi-toggle-{{ $device->is_active ? 'off' : 'on' }} me-2"></i>
                                                        {{ $device->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item text-danger"
                                                        wire:click="delete({{ $device->id }})"
                                                        wire:confirm="Are you sure you want to delete this device?">
                                                        <i class="bi bi-trash me-2"></i>Delete
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    {{-- Device info --}}
                                    <table class="table table-sm table-borderless mb-3">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted ps-0" style="width:40%">
                                                    <i class="bi bi-hdd-network me-1"></i> IP Address
                                                </td>
                                                <td class="fw-semibold font-monospace">{{ $device->ip_address }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">
                                                    <i class="bi bi-diagram-3 me-1"></i> Port
                                                </td>
                                                <td class="fw-semibold font-monospace">{{ $device->port }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">
                                                    <i class="bi bi-cpu me-1"></i> Device ID
                                                </td>
                                                <td class="fw-semibold">{{ $device->device_id }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted ps-0">
                                                    <i class="bi bi-hourglass me-1"></i> Timeout
                                                </td>
                                                <td class="fw-semibold">{{ $device->timeout }}s</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    {{-- Action buttons --}}
                                    <div class="d-flex gap-2">
                                        <button wire:click="syncNow({{ $device->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="syncNow({{ $device->id }})"
                                            class="btn btn-sm btn-outline-primary flex-fill">
                                            <span wire:loading wire:target="syncNow({{ $device->id }})">
                                                <span class="spinner-border spinner-border-sm"></span>
                                            </span>
                                            <span wire:loading.remove wire:target="syncNow({{ $device->id }})">
                                                <i class="bi bi-arrow-repeat me-1"></i>
                                            </span>
                                            Sync Now
                                        </button>
                                        <button wire:click="edit({{ $device->id }})"
                                            class="btn btn-sm btn-outline-secondary flex-fill">
                                            <i class="bi bi-gear me-1"></i> Configure
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </x-slot:body>
    </x-layouts.card>


    {{-- Add / Edit Modal --}}
    <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit Device' : 'Add Fingerprint Device' }}" form="submit">
        <x-slot:content>
            <x-alert.validation />

            {{-- Name --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Device Name <span class="text-danger">*</span></label>
                <input type="text" wire:model.defer="name"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="e.g. Main Entrance A200">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- IP + Port --}}
            <div class="row">
                <div class="col-8 mb-3">
                    <label class="form-label fw-semibold">IP Address <span class="text-danger">*</span></label>
                    <input type="text" wire:model.defer="ip_address"
                        class="form-control font-monospace @error('ip_address') is-invalid @enderror"
                        placeholder="192.168.1.100">
                    @error('ip_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-4 mb-3">
                    <label class="form-label fw-semibold">Port <span class="text-danger">*</span></label>
                    <input type="number" wire:model.defer="port"
                        class="form-control font-monospace @error('port') is-invalid @enderror"
                        placeholder="80">
                    @error('port') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Comm Key --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Comm Key</label>
                <input type="text" wire:model.defer="comm_key"
                    class="form-control font-monospace @error('comm_key') is-invalid @enderror"
                    placeholder="0">
                @error('comm_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">
                    Cek di mesin: <strong>Menu → Comm → PC Connection → Comm Key</strong>. Default: <code>0</code>
                </small>
            </div>

            {{-- Device ID + Timeout --}}
            <div class="row">
                <div class="col-6 mb-3">
                    <label class="form-label fw-semibold">Device ID <span class="text-danger">*</span></label>
                    <input type="number" wire:model.defer="device_id"
                        class="form-control @error('device_id') is-invalid @enderror"
                        placeholder="1">
                    @error('device_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Usually 1 for single-device setup</small>
                </div>
                <div class="col-6 mb-3">
                    <label class="form-label fw-semibold">Timeout (seconds) <span class="text-danger">*</span></label>
                    <input type="number" wire:model.defer="timeout"
                        class="form-control @error('timeout') is-invalid @enderror"
                        placeholder="30" min="5" max="120">
                    @error('timeout') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Active toggle --}}
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_active"
                        wire:model.defer="is_active" role="switch">
                    <label class="form-check-label fw-semibold" for="is_active">
                        Active (use this device for scanning)
                    </label>
                </div>
            </div>

            {{-- Test Connection --}}
            <div class="border rounded p-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold text-muted">
                        <i class="bi bi-broadcast me-1"></i> Test Connection
                    </span>
                    <button type="button" wire:click="testConnection"
                        wire:loading.attr="disabled"
                        wire:target="testConnection"
                        class="btn btn-sm btn-outline-primary">
                        <span wire:loading wire:target="testConnection">
                            <span class="spinner-border spinner-border-sm me-1"></span> Testing...
                        </span>
                        <span wire:loading.remove wire:target="testConnection">
                            <i class="bi bi-plug me-1"></i> Test Now
                        </span>
                    </button>
                </div>
                @if (!empty($test_result))
                    <div class="alert {{ $test_status === 'success' ? 'alert-success' : 'alert-danger' }} mb-0 py-2">
                        {{ $test_result }}
                    </div>
                @else
                    <small class="text-muted">Click "Test Now" to verify connection before saving.</small>
                @endif
            </div>

        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary me-2" wire:click="closeModal">Cancel</button>
            <x-buttons.submit label="Save Device" target="submit" class="btn-primary" />
        </x-slot:action>
    </x-modal.centered>

</div>
@push('script')
<script>
    // Keep modal open on test connection result
    Livewire.on('testDone', () => {
        // just re-render
    });
</script>
@endpush
