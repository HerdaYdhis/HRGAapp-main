@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Hak Akses</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Hak Akses</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section mt-10">
            <div class="row">
                <div class="col-12">
                    <x-alert.notification/>
                    <x-layouts.card>
                        <x-slot:header>
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">List Hak Akses</h5>
                                <div class="card-toolbar">
                                    <button type="button" wire:click="openModal" class="btn btn-primary me-2">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                        <livewire:role.datatable />
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Tambah' }} Hak Akses" form="submit">
        <x-slot:content>
            <x-alert.validation/>
            <x-alert.notification/>
            <div class="row">
                <div class="form-group">
                    <label for="code">Nama Hak Akses</label>
                    <x-forms.text name="Nama" model="name"/>
                </div>
                <div class="form-group">
                    <label for="code">Hak Akses</label>
                    <div class="p-1"></div>
                    @error('menu_access')
                    <x-forms.error message="{{ $message }}"/>
                    @enderror
                    @error('menu_access.*')
                    <x-forms.error message="{{ $message }}"/>
                    @enderror
                    @foreach ($roles as $key => $access)
                        <label for="code">{{ $access['label'] }}</label>
                        <div class="d-flex flex-wrap">
                            @foreach ($access['menu'] as $value => $label)
                                <x-forms.checkbox :value="false" :label="$label" wire:model.defer="role.{{ $key }}.{{ $value }}" class="me-5"/>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary me-2" wire:click="closeModal">Batal</button>
            <x-buttons.submit label="Simpan" target="submit" class="btn-primary ml-1"/>
        </x-slot:action>
    </x-modal.centered>
</div>
