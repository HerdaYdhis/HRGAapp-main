@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3 class="mb-1"><i class="bi bi-shield-exclamation text-primary me-2"></i>Surat Peringatan</h3>
                    <p class="text-subtitle text-secondary mb-0">Pencatatan sanksi disiplin SP1, SP2, SP3 dan pemotongan tunjangan kinerja</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first mb-md-0 mb-3">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Surat Peringatan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="row">
                <div class="col-12">
                    <x-alert.notification />
                    <x-layouts.card>
                        <x-slot:header>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Daftar Surat Peringatan</h5>
                                </div>
                                @if (UserAccess::can('warning-letter', 'manage-warning-letter') || auth()->user()->is_superadmin)
                                    <div class="card-toolbar d-flex flex-wrap gap-2">
                                        <button type="button" wire:click="openModal" class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah Penilaian
                                        </button>
                                        <button type="button" wire:click="openModalExcel" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center">
                                            <i class="bi bi-upload me-1"></i> Upload Excel
                                        </button>
                                        <button type="button" wire:click="download" class="btn btn-success btn-sm d-inline-flex align-items-center">
                                            <i class="bi bi-download me-1"></i> Download Excel
                                        </button>
                                        <button type="button" wire:click="template" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                                            <i class="bi bi-file-earmark-excel me-1"></i> Download Template
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            @if (UserAccess::can('warning-letter', 'list-warning-letter') || auth()->user()->is_superadmin)
                                <livewire:warning-letter.datatable />
                            @endif
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Tambah' }} Surat Peringatan"
        form="{{ $is_upload_excel ? 'import' : 'submit' }}">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <div class="row g-3">
                @if (!$is_upload_excel)
                    <div class="col-md-6 col-12">
                        <label for="user_id" class="form-label fw-bold">Nama Karyawan</label>
                        <fieldset class="form-group mb-0">
                            <div wire:ignore>
                                <select class="form-select @error('user_id') is-invalid @enderror"
                                    id="{{ 'user_id' }}" wire:model.defer="user_id">
                                    <option value="">-- Pilih Karyawan --</option>
                                    @forelse ($userdata as $user)
                                        <option value="{{ $user->id }}"
                                            @if ($user_id == $user->id) selected @endif>
                                            {{ $user->name }} ({{ $user->employee_id ?? 'ID: '.$user->id }})
                                        </option>
                                    @empty
                                        <option value="" disabled>User Tidak Ditemukan</option>
                                    @endforelse
                                </select>
                            </div>
                            @error('user_id')
                                <x-forms.error message="{{ $message }}" />
                            @enderror
                        </fieldset>
                    </div>
                    <div class="col-md-6 col-12">
                        <label for="periode" class="form-label fw-bold">Periode Penilaian</label>
                        <x-forms.month name="Periode" model="periode" />
                    </div>

                    <div class="col-12 mt-2">
                        <label class="form-label fw-bold border-bottom pb-2 mb-2 d-block">
                            Tingkat Surat Peringatan (SP)
                        </label>
                        <div class="row g-2">
                            <div class="col-md-6 col-12">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input sp" type="radio" value="0" id="sp0"
                                            wire:model="sp" @if ($sp == 0) checked @endif>
                                        <label class="form-check-label fw-bold ms-1" for="sp0">
                                            Tidak Ada SP (0%)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input sp" type="radio" value="20" id="sp1"
                                            wire:model="sp" @if ($sp == 20) checked @endif>
                                        <label class="form-check-label fw-bold ms-1" for="sp1">
                                            Surat Peringatan 1 / SP1 (-20%)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input sp" type="radio" value="30" id="sp2"
                                            wire:model="sp" @if ($sp == 30) checked @endif>
                                        <label class="form-check-label fw-bold ms-1" for="sp2">
                                            Surat Peringatan 2 / SP2 (-30%)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input sp" type="radio" value="50" id="sp3"
                                            wire:model="sp" @if ($sp == 50) checked @endif>
                                        <label class="form-check-label fw-bold ms-1" for="sp3">
                                            Surat Peringatan 3 / SP3 (-50%)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-12">
                        <div x-data="{ isUploading: false, progress: 0, isDone: false, isFileUploaded: false }"
                            x-on:livewire-upload-start="isUploading = true; isDone = false; isFileUploaded = true"
                            x-on:livewire-upload-finish="isUploading = false; isDone = true; isFileUploaded = true"
                            x-on:livewire-upload-error="isUploading = false; isFileUploaded = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                            class="p-4 border rounded text-center">
                            <i class="bi bi-file-earmark-excel text-success fs-1 mb-2 d-block"></i>
                            <label for="terlambat" class="form-label fw-bold d-block mb-1">Import File Surat Peringatan</label>
                            <p class="text-secondary small mb-3">Pilih berkas dengan format .xls atau .xlsx</p>
                            <input type="file" class="form-control mb-2" wire:model="excelFile">
                            <div x-show="isUploading" class="mt-2">
                                <progress max="100" x-bind:value="progress" class="w-100"></progress>
                                <small class="text-secondary">Mengunggah berkas...</small>
                            </div>
                            <div x-show="isDone" x-cloak class="mt-2">
                                <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i> Berkas siap diimpor!</span>
                            </div>
                            <div x-show="!isFileUploaded && !isUploading" class="text-secondary small mt-1">
                                Belum ada berkas yang dipilih.
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary px-3" wire:click="closeModal">Batal</button>
            <x-buttons.submit label="Simpan" target="submit" class="btn-primary px-4" />
        </x-slot:action>
    </x-modal.centered>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            $('#{{ $modal }}').on('shown.bs.modal', function(e) {
                $('#user_id').select2({
                    dropdownParent: $("#{{ $modal }}")
                });
                $('#user_id').on('change', function(e) {
                    var data = $('#user_id').select2("val");
                    @this.set('user_id', data);
                });
            });
        });
    </script>
@endpush
