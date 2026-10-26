@php
    use App\Utilities\Constant;
    use App\Utilities\UserAccess;
@endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Close Periode</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Close Periode</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section mt-10">
            <div class="row">
                <div class="col-12">
                    <x-alert.notification />
                    <x-layouts.card>
                        <x-slot:header>
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">List Data</h5>

                                @if (UserAccess::can('closeperiode', 'manage-closeperiode') || auth()->user()->is_superadmin)
                                    <div class="card-toolbar">
                                        <button type="button" wire:click="submit" class="btn btn-primary me-2">
                                            Create Close Periode
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            @if (UserAccess::can('closeperiode', 'list-closeperiode') || auth()->user()->is_superadmin)
                                <div class="row">
                                        <h4 class="header">List Data</h4>
                                        <fieldset class="rounded border p-4">
                                            <livewire:close-periode.datatable />
                                        </fieldset>
                                    </div>
                                </div>
                            @endif
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

</div>


