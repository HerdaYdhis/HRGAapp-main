@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Dashboard</h3>
                    {{-- <div class="mb-3 mt-4">
                        <h5>Hello, {{ auth()->user()->name }}</h5>
                        <p class="text-subtitle text-muted"> Login with email : {{ auth()->user()->email }}</p>
                        <p class="text-subtitle text-muted">Anda masuk dengan Role/User : {{ auth()->user()->role }}</p>
                    </div> --}}
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="section">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body py-4 px-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xl">
                                    <img src="{{ auth()->user()->avatar }}" alt="Face 1">
                                </div>
                                <div class="ms-3 name">
                                    <h5 class="font-bold">{{ auth()->user()->name }}</h5>
                                    <h6 class="font-bold">Login with email : </h6>
                                    <h6 class="text-muted mb-0">{{ auth()->user()->email }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="rounded">
                                    {{ QrCode::size(80)->margin(1)->generate(encrypt(auth()->user()->employee_id)) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- @php
                    dd(auth()->user()->email);
                @endphp --}}
                @if (auth()->user()->email != 'kantin01@saf-indo.com' || !auth()->user()->email != 'kantin02@saf-indo.com')
                    <x-layouts.card>
                        <x-slot:header>
                            <h7 class="card-title bg-gradient-ltr rounded-3 p-2 text-white">Peminjaman Ruangan Hari ini
                            </h7>
                        </x-slot:header>

                        <x-slot:body>
                            <div class="m-2">
                                <livewire:dashboard.datatable-bro />
                            </div>
                        </x-slot:body>
                    </x-layouts.card>
                @endauth


                @auth
                    @if (auth()->user()->role_id == null && auth()->user()->is_superadmin == false)
                        <div class="text-bg-danger rounded-4 container mb-5 p-2 py-3 text-xl text-white">
                            <p>Anda belum memiliki akses apapun dalam aplikasi ini, Silahkan Hubungi Penyedia atau
                                Administrator!</p>
                            <p>You do not yet have any access in this application, Please Contact the Provider or
                                Administrator!</p>
                            <p>~ HRGA Dept</p>
                        </div>
                    @endif

                @endauth


                @if (UserAccess::parent('scanqr') || auth()->user()->is_superadmin)
                    <div class="m-2">
                        <livewire:dashboard.chart-canteen />
                    </div>
                @endif
                @if (UserAccess::parent('closeperiode') || auth()->user()->is_superadmin)
                    <livewire:dashboard.chart-all-user-dept />
                    <div class="m-2">
                        <livewire:dashboard.chart-monthly />
                    </div>

                    <x-alert.notification />
                    <x-layouts.card>
                        <x-slot:header>
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">Task Activity</h5>
                                {{-- @if (UserAccess::can('mastertask', 'manage-mastertask') || auth()->user()->is_superadmin) --}}
                                <div class="card-toolbar">
                                    <button type="button" wire:click="openModal" class="btn btn-primary me-2">
                                        Add New Task
                                    </button>
                                </div>
                                {{-- @endif --}}
                            </div>
                        </x-slot:header>
                        <x-slot:body>

                            <div class="m-2">
                                <livewire:dashboard.datatable />

                            </div>

                        </x-slot:body>
                    </x-layouts.card>
                @endif

                @if (UserAccess::parent('task') || auth()->user()->is_superadmin)
                    <div class="m-2">
                        <livewire:dashboard.chart-user />
                    </div>
                @endif

        </div>
    </section>
</div>
<x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Tambah' }} Master Task" form="submit">
    <x-slot:content>
        <x-alert.validation />
        <x-alert.notification />
        <div class="row">
            <div class="form-group">
                <label for="title">Title</label>
                <x-forms.text name="Title" model="title" />
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <x-forms.text name="Description" model="description" />
            </div>

            <div class="form-group">
                <label for="type">Type</label>
                <fieldset class="form-group">
                    <select class="form-select @error('type') is-invalid @enderror" id="typeSelect"
                        wire:model.defer="type">
                        <option value="">Select Type</option>
                        <option value="daily" @if ($type == 'daily') selected @endif>Daily</option>
                        <option value="weekly" @if ($type == 'weekly') selected @endif>Weekly</option>
                        <option value="monthly" @if ($type == 'monthly') selected @endif>Monthly</option>
                        <option value="project" @if ($type == 'project') selected @endif>Project</option>
                    </select>
                    @error('type')
                        <x-forms.error message="{{ $message }}" />
                    @enderror
                </fieldset>
            </div>


            <div class="form-group">
                <label for="user_id">User/Karyawan</label>
                <fieldset class="form-group">
                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id"
                        wire:model.defer="user_id">
                        <option value="">Select User</option>
                        @forelse ($userdata as $user)
                            <option value="{{ $user->id }}" @if ($user_id == $user->id) selected @endif>
                                {{ $user->name }}</option>
                        @empty
                            <option value="" disabled>User Not Found</option>
                        @endforelse
                    </select>
                    @error('user_id')
                        <x-forms.error message="{{ $message }}" />
                    @enderror
                </fieldset>
            </div>

            <div class="form-group" style="display: none" id="duedateField">
                <label for="duedate">Due Date</label>
                <x-forms.date name="Due Date" model="duedate" />
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <x-forms.select :datas="Constant::STATUS" value="value" label="label" name="status" model="status" />

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

@push('script')
<script>
    const typeSelect = document.getElementById('typeSelect');
    const duedateField = document.getElementById('duedateField');

    typeSelect.addEventListener('change', function() {
        const selectedType = typeSelect.value;

        if (selectedType === 'monthly' || selectedType === 'project') {
            duedateField.style.display = 'block';
        } else {
            duedateField.style.display = 'none';
        }
    });
</script>
@endpush
