@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3 class="mb-1"><i class="bi bi-list-check text-primary me-2"></i>List Kinerja</h3>
                    <p class="text-subtitle text-secondary mb-0">Kelola master indikator KPI dan pembobotan kinerja karyawan</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first mb-md-0 mb-3">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">List Kinerja</li>
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
                            @if ($othersVisible)
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="card-title mb-0">Indikator Kinerja: <span class="text-primary fw-bold">{{ $user_name }}</span></h5>
                                    </div>
                                    <div class="card-toolbar d-flex flex-wrap gap-2">
                                        <button type="button" wire:click="openModal" class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah Indikator
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
                                </div>
                            @endif
                        </x-slot:header>
                        <x-slot:body>
                            @if (!$select_user)
                                <div class="py-2">
                                    <livewire:performances.datatable-user />
                                </div>
                            @endif
                            @if ($othersVisible)
                                <div class="mb-3">
                                    <button class="btn btn-secondary btn-sm d-inline-flex align-items-center" wire:click="back">
                                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar User
                                    </button>
                                </div>
                                <livewire:performances.datatable />
                            @endif
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Tambah' }} Master Kinerja"
        form="{{ $is_upload_excel ? 'import' : 'submit' }}">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <div class="row g-3">
                @if (!$is_upload_excel)
                    <div class="col-12">
                        <label for="code" class="form-label fw-bold">Indikator Kinerja</label>
                        <x-forms.textarea name="Indikator" model="indikator" placeholder="Tuliskan indikator capaian kinerja karyawan..." />
                    </div>
                    <div class="col-12">
                        <label for="code" class="form-label fw-bold">Bobot (%)</label>
                        <x-forms.number name="Bobot" model="bobot" placeholder="Contoh: 25" />
                        <small class="text-secondary d-block mt-1">Total seluruh bobot indikator harus berjumlah 100%.</small>
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
                            <label for="terlambat" class="form-label fw-bold d-block mb-1">Import File Excel</label>
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
