@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Price</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Price</li>
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
                                <h5 class="card-title">List Price</h5>
                                <div class="card-toolbar">
                                    <button type="button" wire:click="openModal" class="btn btn-primary me-2">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            <livewire:price.datatable />
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Tambah' }} Price" form="submit">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <div class="row">
                <div class="form-group">
                    <label for="code">Name Price</label>
                    <x-forms.text name="Nama" model="name" />
                </div>
                 <div class="form-group">
                    <label for="prices">Price</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp.</span>
                        <input type="text" wire:model.defer="prices"
                            class="form-control @error('prices') is-invalid @enderror" id="prices"
                            placeholder="11.500"
                            onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" />
                    </div>
                    @error('prices')
                        <x-forms.error message="{{ $message }}" />
                    @enderror
                </div>
                <div class="form-group">
                        <label for="status">Status</label>
                        <x-forms.select :datas="Constant::PRICE" value="value" label="label" name="status"
                            model="status" />
                        @error('status')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </div>
            </div>
        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary me-2" wire:click="closeModal">Batal</button>
            <x-buttons.submit label="Simpan" target="submit" class="btn-primary ml-1" />
        </x-slot:action>
    </x-modal.centered>
</div>
