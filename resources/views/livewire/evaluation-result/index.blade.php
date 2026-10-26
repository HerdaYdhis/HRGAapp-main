@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3 class="mb-1"><i class="bi bi-wallet2 text-primary me-2"></i>Rekap Tunjangan Kerja Pegawai</h3>
                    <p class="text-subtitle text-secondary mb-0">Kalkulasi dan rekapitulasi nominal tunjangan kinerja (TUKIN) seluruh pegawai</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first mb-md-0 mb-3">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Rekap Tunjangan Pegawai</li>
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
                                    <h5 class="card-title mb-0">
                                        @if ($othersVisible)
                                            Rekap Periode: <span class="text-primary fw-bold">{{ \Carbon\Carbon::parse($othersVisible)->format('F Y') }}</span>
                                        @else
                                            Pilih Periode Rekapitulasi Tunjangan
                                        @endif
                                    </h5>
                                </div>
                                @if ($othersVisible)
                                    <div class="card-toolbar d-flex flex-wrap gap-2">
                                        <button class="btn btn-secondary btn-sm d-inline-flex align-items-center" wire:click="back">
                                            <i class="bi bi-arrow-left me-1"></i> Ganti Periode
                                        </button>
                                        <button type="button" wire:click="download" title="Download Excel"
                                            class="btn btn-success btn-sm d-inline-flex align-items-center">
                                            <i class="bi bi-file-earmark-excel me-1"></i> Download Excel
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            @if (!$select_user)
                                <x-alert.validation />
                                <x-alert.notification />
                                <form wire:submit.prevent="user_submit" class="py-2">
                                    <div class="row">
                                        <div class="col-xl-5 col-lg-6 col-md-8 col-12" style="max-width: 480px;">
                                            <div class="form-group mb-4">
                                                <label for="periode" class="form-label fw-bold">
                                                    <i class="bi bi-calendar-event text-primary me-2"></i>Periode Penilaian
                                                </label>
                                                <x-forms.month name="Perode" model="periode" />
                                                <small class="text-secondary d-block mt-1">Pilih bulan dan tahun untuk menghitung rekapitulasi tunjangan.</small>
                                            </div>

                                            <div class="pt-1">
                                                <button type="submit" class="btn btn-primary px-4 py-2 d-inline-flex align-items-center shadow-sm">
                                                    <i class="bi bi-calculator me-2"></i> Tampilkan Rekap Tunjangan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endif

                            @if ($othersVisible)
                                <div class="py-2">
                                    <livewire:evaluation-result.datatable />
                                </div>
                            @endif
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="Input Nilai Kinerja" form="submit">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <input type="hidden" name="user_id" value="{{ $othersVisible }}">
            <div class="row g-3">
                <div class="col-12">
                    <label for="title" class="form-label fw-bold">Nilai</label>
                    <x-forms.number name="Nilai" model="nilai" />
                </div>
            </div>
        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary px-3" wire:click="closeModal">Batal</button>
            <x-buttons.submit label="Simpan" target="submit" class="btn-primary px-4" />
        </x-slot:action>
    </x-modal.centered>
</div>
