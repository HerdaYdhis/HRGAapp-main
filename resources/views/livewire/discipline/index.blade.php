@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3 class="mb-1"><i class="bi bi-clock-history text-primary me-2"></i>Penilaian Kedisiplinan</h3>
                    <p class="text-subtitle text-secondary mb-0">Pencatatan data alfa, izin, sakit, dan keterlambatan pegawai per periode</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first mb-md-0 mb-3">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Penilaian Kedisiplinan</li>
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
                                    <h5 class="card-title mb-0">Data Kedisiplinan Pegawai</h5>
                                </div>
                                @if (UserAccess::can('discipline', 'manage-discipline') || auth()->user()->is_superadmin)
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
                                        <button type="button" wire:click="downloadTemplate" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                                            <i class="bi bi-file-earmark-excel me-1"></i> Download Template
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            @if (UserAccess::can('discipline', 'list-discipline') || auth()->user()->is_superadmin)
                                <livewire:discipline.datatable />
                            @endif
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Tambah' }} Kedisiplinan"
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
                        <h6 class="fw-bold border-bottom pb-2 mb-2">Rekap Absensi & Izin (Jumlah Hari)</h6>
                    </div>

                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label for="alfa" class="form-label fw-bold text-danger">Alfa (Hari)</label>
                            <x-forms.number name="Alfa" model="alfa" placeholder="0" />
                            <small class="text-secondary d-block mt-1">1x: -30, 2x: -50, ≥3x: -100</small>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label for="izin" class="form-label fw-bold text-primary">Izin (Hari)</label>
                            <x-forms.number name="Izin" model="izin" placeholder="0" />
                            <small class="text-secondary d-block mt-1">1-2 hari: -10, >2 hari: -20</small>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label for="sakit" class="form-label fw-bold text-warning">Sakit (Hari)</label>
                            <x-forms.number name="Sakit" model="sakit" placeholder="0" />
                            <small class="text-secondary d-block mt-1">1-2 hari: -10, >2 hari: -20</small>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <h6 class="fw-bold border-bottom pb-2 mb-2">Tingkat Keterlambatan</h6>
                    </div>

                    <div class="col-12">
                        <div class="row g-2">
                            <div class="col-md-6 col-12">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="0" id="terlambat0"
                                            wire:model="terlambat" @if ($terlambat == 0) checked @endif>
                                        <label class="form-check-label fw-bold ms-1" for="terlambat0">
                                            Tidak Ada Terlambat (0)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="10" id="terlambat1"
                                            wire:model="terlambat" @if ($terlambat == 10) checked @endif>
                                        <label class="form-check-label fw-bold ms-1" for="terlambat1">
                                            Terlambat ≥5 ≤20 menit (-10)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model="terlambat" value="20"
                                            id="terlambat2" @if ($terlambat == 20) checked @endif>
                                        <label class="form-check-label fw-bold ms-1" for="terlambat2">
                                            Terlambat >20 ≤60 menit (-20)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="border rounded p-3">
                                    <div class="form-check">
                                        <input class="form-check-input telat" type="radio" wire:model="terlambat"
                                            value="30" id="terlambat3"
                                            @if ($terlambat == 30) checked @endif>
                                        <label class="form-check-label fw-bold ms-1" for="terlambat3">
                                            Terlambat >60 menit (-30)
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
                            <label for="terlambat" class="form-label fw-bold d-block mb-1">Import File Kedisiplinan</label>
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
