@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3 class="mb-1"><i class="bi bi-person-check text-primary me-2"></i>Rekap Tunjangan Kerja Anda</h3>
                    <p class="text-subtitle text-secondary mb-0">Histori evaluasi kinerja, kedisiplinan, dan nominal tunjangan (TUKIN) pribadi Anda</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first mb-md-0 mb-3">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Rekap Tunjangan Anda</li>
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
                                        Catatan Tunjangan: <span class="text-primary fw-bold">{{ auth()->user()->name }}</span>
                                    </h5>
                                </div>
                                <div class="card-toolbar">
                                    <button type="button" wire:click="download" title="Download Excel"
                                        class="btn btn-success btn-sm d-inline-flex align-items-center">
                                        <i class="bi bi-file-earmark-excel me-1"></i> Download Excel
                                    </button>
                                </div>
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            <div class="py-2">
                                <livewire:evaluation-result.datatable-self />
                            </div>
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>
</div>
