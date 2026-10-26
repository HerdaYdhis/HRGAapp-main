<div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ auth()->user()->avatar }}" alt="avatar" class="rounded-circle img-fluid"
                        style="width: 150px;">
                    <div class="rounded-circle img-fluid">

                    </div>
                    <h5 class="my-3">{{ auth()->user()->name }}</h5>
                    <div class="d-flex justify-content-center mb-2">
                        <button type="button" wire:click="openModal" class="btn btn-primary me-2">Update</button>
                    </div>

                    <div class="rounded p-4">
                        {{ QrCode::size(200)->margin(1)->generate(encrypt(auth()->user()->employee_id)) }}
                    </div>
                </div>
            </div>
            <div class="card mb-lg-0 mb-4">
                {{-- // --}}
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">Full Name</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{ auth()->user()->name }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">Email</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">Whatsapp</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{ auth()->user()->no_wa }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">Address</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{ auth()->user()->address }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-3">
                            <p class="mb-0">Employess Id</p>
                        </div>
                        <div class="col-sm-9">
                            <p class="text-muted mb-0">{{ auth()->user()->employee_id }}</p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">

                <x-alert.notification />
                {{-- <div class="col-md-6 mb-md-0 mb-4">
                    <x-layouts.card>
                        <x-slot:header>

                        </x-slot:header>
                        <x-slot:body>

                        </x-slot:body>
                    </x-layouts.card>
                </div>
                <div class="col-md-6 mb-md-0 mb-4">
                    <x-layouts.card>
                        <x-slot:header>
                        </x-slot:header>
                        <x-slot:body>

                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </div> --}}
            </div>

            <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Edit' }} Profil" form="submit">
                <x-slot:content>
                    <x-alert.validation />
                    <x-alert.notification />
                    <div class="row">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <x-forms.text name="Nama" model="name" />
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <x-forms.text name="email" model="email" />
                        </div>
                        <div class="form-group">
                            <label for="no_wa">No Whatsapp</label>
                            <x-forms.text name="no_wa" model="no_wa" />
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" wire:model.defer="password"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                placeholder="Masukkan password" />
                            @error('password')
                                <x-forms.error message="{{ $message }}" />
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <x-forms.text name="address" model="address" />
                        </div>

                        <div class="form-group">
                            <label for="phone">Avatar</label>
                            <input class="form-control" type="file" id="avatar" wire:model="avatar">
                            <div wire:loading wire:target="avatar">
                                <p class="text-primary mb-2 ms-1 mt-1">Uploading ...</p>
                            </div>
                            @error('avatar')
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
