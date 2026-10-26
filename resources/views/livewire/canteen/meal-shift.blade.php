@php use App\Utilities\Constant; @endphp

<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Meal Shift</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Meal Shift</li>
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
                                <h5 class="card-title">List Meal Shift</h5>
                                <div class="card-toolbar">
                                    <button type="button" wire:click="openModal" class="btn btn-primary me-2">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            <livewire:canteen.datatable-meal />
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Tambah' }} Meal Shift" form="submit">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <div class="row">
                <div class="form-group">
                    <label for="code">Shift name</label>
                    <x-forms.text name="Nama" model="shift_name" />
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="senin_meal_time">Monday time</label>
                            <x-forms.time name="Senin" model="monday_meal_time" />
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="selasa_meal_time">Tuesday time</label>
                            <x-forms.time name="Selasa" model="tuesday_meal_time" />
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="rabu_meal_time">Wednesday time</label>
                            <x-forms.time name="Rabu" model="wednesday_meal_time" />
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="kamis_meal_time">Thursday time</label>
                            <x-forms.time name="Kamis" model="thursday_meal_time" />
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="jumat_meal_time">Friday time</label>
                            <x-forms.time name="Jumat" model="friday_meal_time" />
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="sabtu_meal_time">Saturday time</label>
                            <x-forms.time name="Sabtu" model="saturday_meal_time" />
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="minggu_meal_time">Sunday time</label>
                            <x-forms.time name="Minggu" model="sunday_meal_time" />
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary me-2" wire:click="closeModal">Batal</button>
            <x-buttons.submit label="Simpan" target="submit" class="btn-primary ml-1" />
        </x-slot:action>
    </x-modal.centered>
</div>
