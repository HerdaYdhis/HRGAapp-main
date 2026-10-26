@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Pengguna</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pengguna</li>
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
                                <h5 class="card-title">List Pengguna</h5>
                                <div class="card-toolbar">
                                    <button type="button" wire:click="openModal" class="btn btn-primary me-2">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            <livewire:user.datatable />
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Tambah' }} Pengguna" form="submit">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <div class="row">
                <div class="form-group">
                    <label for="code">Employee ID</label>
                    <x-forms.text name="Employee ID" model="employee_id" />
                </div>
                <div class="form-group">
                    <label for="code">Nama</label>
                    <x-forms.text name="Nama" model="name" />
                </div>
                <div class="form-group">
                    <label for="code">Email</label>
                    <x-forms.text name="Email" model="email" />
                </div>
                <div class="form-group">
                    <label for="code">Password</label>
                    <x-forms.password name="Password" model="password" />
                </div>
                <div class="form-group">
                    <label for="user_id">Department</label>
                    <fieldset class="form-group">
                        <select class="form-select @error('department_id') is-invalid @enderror"
                            id="{{ 'department_id' }}" wire:model.defer="department_id" data-live-search="true">
                            <option value="">Select Department</option>
                            @forelse ($userdepartment as $department)
                                <option value="{{ $department->id }}">
                                    {{ $department->name }}</option>
                            @empty
                                <option value="" disabled>Department Tidak Ditemukan
                                </option>
                            @endforelse
                        </select>
                        @error('department_id')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="user_id">Level User</label>
                    <fieldset class="form-group">
                        <div wire:ignore>
                            <select class="form-select @error('user_level_id') is-invalid @enderror"
                                id="{{ 'user_level_id' }}" wire:model.defer="user_level_id">
                                <option value="">Select Level</option>
                                @forelse ($userlevel as $user_level)
                                    <option value="{{ $user_level->id }}">
                                        {{ $user_level->level }}</option>
                                @empty
                                    <option value="" disabled>Level user Tidak Ditemukan
                                    </option>
                                @endforelse
                            </select>
                        </div>
                        @error('user_level_id')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="user_id">Employee Status</label>
                    <fieldset class="form-group">
                        <div wire:ignore>
                            <select class="form-select @error('status_employee_id') is-invalid @enderror"
                                id="{{ 'status_employee_id' }}" wire:model.defer="status_employee_id">
                                <option value="">Select Status</option>
                                @forelse ($employee_status as $status)
                                    <option value="{{ $status->id }}">
                                        {{ $status->name }}</option>
                                @empty
                                    <option value="" disabled>Status user Tidak Ditemukan
                                    </option>
                                @endforelse
                            </select>
                        </div>
                        @error('status_employee_id')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="user_id">Branch</label>
                    <fieldset class="form-group">
                        <div wire:ignore>
                            <select class="form-select @error('branch_id') is-invalid @enderror"
                                id="{{ 'branch_id' }}" wire:model.defer="branch_id">
                                <option value="">Select Branch</option>
                                @forelse ($branch as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }}</option>
                                @empty
                                    <option value="" disabled>Branch user Tidak Ditemukan
                                    </option>
                                @endforelse
                            </select>
                        </div>
                        @error('branch')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="parent_id">Atasan</label>
                    <fieldset class="form-group">
                        <div wire:ignore>
                            <select class="form-select @error('parent_id') is-invalid @enderror"
                                id="{{ 'parent_id' }}" wire:model.defer="parent_id">
                                <option value="">Select User</option>
                                @forelse ($userdata as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }}</option>
                                @empty
                                    <option value="" disabled>User Tidak Ditemukan
                                    </option>
                                @endforelse
                            </select>
                        </div>
                        @error('parent_id')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="coa_id">COA</label>
                    <fieldset class="form-group">
                        <div wire:ignore>
                            <select class="form-select @error('coa_id') is-invalid @enderror" id="{{ 'coa_id' }}"
                                wire:model.defer="coa_id">
                                <option value="">Select COA</option>
                                @forelse ($coa as $list_coa)
                                    <option value="{{ $list_coa->id }}">
                                        {{ $list_coa->name }}</option>
                                @empty
                                    <option value="" disabled>COA Tidak Ditemukan
                                    </option>
                                @endforelse
                            </select>
                        </div>
                        @error('coa_id')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="cost_center_id">Cost Center</label>
                    <fieldset class="form-group">
                        <div wire:ignore>
                            <select class="form-select @error('cost_center_id') is-invalid @enderror"
                                id="{{ 'cost_center_id' }}" wire:model.defer="cost_center_id">
                                <option value="">Select Cost Center</option>
                                @forelse ($cost_center as $cost)
                                    <option value="{{ $cost->id }}">
                                        {{ $cost->name }}</option>
                                @empty
                                    <option value="" disabled>Cost Center Tidak Ditemukan
                                    </option>
                                @endforelse
                            </select>
                        </div>
                        @error('cost_center_id')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="is_active">Employee Status</label>
                    <fieldset class="form-group">
                        <div wire:ignore>
                            <select class="form-select @error('is_active') is-invalid @enderror"
                                id="{{ 'is_active' }}" wire:model.defer="is_active">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Non-Active</option>
                            </select>
                        </div>
                        @error('is_active')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="role_id">Hak Akses</label>
                    <fieldset class="form-group">
                        <select class="form-select @error('role_id') is-invalid @enderror"
                            id="role_id" wire:model.defer="role_id">
                            <option value="">Pilih Hak Akses</option>
                            @forelse ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @empty
                                <option value="" disabled>Hak Akses Tidak Ditemukan</option>
                            @endforelse
                        </select>
                        @error('role_id')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
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
        $(document).ready(function() {
            // Sync all wire:ignore selects when modal opens
            window.addEventListener('modal-show', function(event) {
                if (event.detail.modal === '{{ $modal }}') {
                    setTimeout(function() {
                        // Set values for all ignored selects from Livewire state
                        var component = window.Livewire.find($('#{{ $modal }}').closest('[wire\\:id]').attr('wire:id'));
                        if (!component) {
                            // Try finding via the Livewire component on the page
                            var components = window.Livewire.components ? window.Livewire.components.components : [];
                        }

                        // parent_id with select2
                        $('#parent_id').select2({
                            dropdownParent: $('#{{ $modal }}')
                        });

                        // Sync all select values from Livewire
                        @this.get('role_id').then(function(val) {
                            $('#role_id').val(val || '');
                        });
                        @this.get('department_id').then(function(val) {
                            $('#department_id').val(val || '');
                        });
                        @this.get('user_level_id').then(function(val) {
                            $('#user_level_id').val(val || '');
                        });
                        @this.get('status_employee_id').then(function(val) {
                            $('#status_employee_id').val(val || '');
                        });
                        @this.get('branch_id').then(function(val) {
                            $('#branch_id').val(val || '');
                        });
                        @this.get('parent_id').then(function(val) {
                            $('#parent_id').val(val || '').trigger('change');
                        });
                        @this.get('coa_id').then(function(val) {
                            $('#coa_id').val(val || '');
                        });
                        @this.get('cost_center_id').then(function(val) {
                            $('#cost_center_id').val(val || '');
                        });
                        @this.get('is_active').then(function(val) {
                            $('#is_active').val(val !== null && val !== undefined ? val : '1');
                        });
                    }, 100);
                }
            });

            // Listen for changes on each select and update Livewire
            $('#role_id').on('change', function() {
                var val = $(this).val();
                @this.set('role_id', val === '' ? null : val);
            });

            $('#department_id').on('change', function() {
                var val = $(this).val();
                @this.set('department_id', val === '' ? null : val);
            });

            $('#user_level_id').on('change', function() {
                var val = $(this).val();
                @this.set('user_level_id', val === '' ? null : val);
            });

            $('#status_employee_id').on('change', function() {
                var val = $(this).val();
                @this.set('status_employee_id', val === '' ? null : val);
            });

            $('#branch_id').on('change', function() {
                var val = $(this).val();
                @this.set('branch_id', val === '' ? null : val);
            });

            $('#coa_id').on('change', function() {
                var val = $(this).val();
                @this.set('coa_id', val === '' ? null : val);
            });

            $('#cost_center_id').on('change', function() {
                var val = $(this).val();
                @this.set('cost_center_id', val === '' ? null : val);
            });

            $('#is_active').on('change', function() {
                var val = $(this).val();
                @this.set('is_active', val === '' ? null : val);
            });

            // parent_id via select2
            $('#parent_id').on('change', function(e) {
                var data = $('#parent_id').select2("val");
                @this.set('parent_id', data === "" ? null : data);
            });
        });
    </script>
@endpush
