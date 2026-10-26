@php use App\Utilities\Constant; @endphp

<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Employee Schedule</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Employee Schedule</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        @if (Session::has('error_messages'))
            <div class="alert alert-danger">
                <h4 class="alert-heading">Alert</h4>
                <ul>
                    @foreach (Session::get('error_messages') as $errorMessage)
                        <li>{{ $errorMessage }}</li>
                    @endforeach
                </ul>
            </div>
            <?php Session::forget('error_messages'); ?>
        @endif

        <section class="section mt-10">
            <div class="row">
                <div class="col-12">
                    <x-alert.notification />
                    <x-layouts.card>
                        <x-slot:header>
                            <div class="d-flex flex-md-row flex-column justify-content-between">
                                <h5 class="card-title">List Employee Schedule</h5>
                                <div class="card-toolbar text-center">
                                    <button type="button" wire:click="openModal" class="btn btn-primary mb-2">
                                        Tambah
                                    </button>
                                    <button type="button" wire:click="openModalExcel" class="btn btn-primary mb-2">
                                        <i class="bi bi-upload"></i> Upload
                                    </button>
                                    <button type="button" wire:click="openModalDownload" class="btn btn-primary mb-2">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                    <button type="button" wire:click="deleteModal" class="btn btn-danger mb-2">
                                        <i class="bi bi-x"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            <livewire:canteen.datatable-employee />
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>
    @php
        $formValue = $is_delete
            ? 'delete_range'
            : ($is_upload_excel
                ? 'import'
                : ($is_download
                    ? 'download'
                    : 'submit'));
    @endphp
    <x-modal.centered _id="{{ $modal }}"
        title="{{ $is_delete ? 'Delete' : ($is_edit ? 'Edit' : ($is_download ? 'Download' : 'Tambah')) }} Employee Schedule"
        :form="$formValue">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <div class="row">
                @if (!$is_upload_excel && !$is_download && !$is_delete)
                    <div class="form-group">
                        <label for="user_id">User</label>
                        <fieldset class="form-group">
                            <div wire:ignore>
                                <select class="form-select @error('user_id') is-invalid @enderror"
                                    id="{{ 'user_id' }}" wire:model.defer="user_id">
                                    <option value="">Select User</option>
                                    @forelse ($userdata as $user)
                                        <option value="{{ $user->id }}"
                                            @if ($user_id == $user->id) selected @endif>
                                            {{ $user->name }}</option>
                                    @empty
                                        <option value="" disabled>User Tidak Ditemukan</option>
                                    @endforelse
                                </select>
                            </div>
                            @error('user_id')
                                <x-forms.error message="{{ $message }}" />
                            @enderror
                        </fieldset>
                    </div>
                    <input type="hidden" name="employee_id" wire:model="employee_id" />
                    <div class="form-group">
                        <label for="user_id">Shift Name</label>
                        <fieldset class="form-group">
                            <div wire:ignore>
                                <select class="form-select @error('meal_shift_id') is-invalid @enderror"
                                    id="{{ 'meal_shift_id' }}" wire:model.defer="meal_shift_id">
                                    <option value=" ">Select shift</option>
                                    @forelse ($meal_shifts as $meal)
                                        <option value="{{ $meal->id }}"
                                            @if ($meal_shift_id == $meal->id) selected @endif>
                                            {{ $meal->shift_name }}</option>
                                    @empty
                                        <option value="" disabled>Shift Tidak Ditemukan</option>
                                    @endforelse
                                </select>
                            </div>
                            @error('meal_shift_id')
                                <x-forms.error message="{{ $message }}" />
                            @enderror
                        </fieldset>
                    </div>
                    <div class="form-group">
                        <label for="work_date">Work Date</label>
                        <x-forms.date name="Work Date" model="work_date" />
                    </div>
                @elseif (!$is_upload_excel && $is_download && !$is_delete)
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <x-forms.date name="Start Date" model="start_date" />
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <x-forms.date name="End Date" model="end_date" />
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Type</label>
                        <select class="form-select" wire:model="type_download">
                            <option selected>Select one</option>
                            <option value="template">Download Template</option>
                            <option value="import">Download Import</option>
                        </select>
                    </div>
                @elseif (!$is_upload_excel && !$is_download && $is_delete)
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <x-forms.date name="Start Date" model="start_date" />
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <x-forms.date name="End Date" model="end_date" />
                    </div>
                @else
                    <div class="mb-3">
                        <label for="" class="form-label">Type</label>
                        <select class="form-select form-select-lg" wire:model="selectedOption">
                            <option selected>Select one</option>
                            <option value="update">Update Data</option>
                            <option value="upload">Import Data</option>
                        </select>
                    </div>
                    @if ($selectedOption === 'update')
                        <div class="form-group">
                            <div x-data="{ isUploading: false, progress: 0, isDone: false, isFileUploaded: false }"
                                x-on:livewire-upload-start="isUploading = true; isDone = false; isFileUploaded = true"
                                x-on:livewire-upload-finish="isUploading = false; isDone = true; isFileUploaded = true"
                                x-on:livewire-upload-error="isUploading = false; isFileUploaded = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress">
                                <label for="terlambat">Update Excel</label>
                                <input type="file" class="form-control mb-2" wire:model="excelFile">
                                <div x-show="isUploading">
                                    <progress max="100" x-bind:value="progress"></progress>
                                    Uploading...
                                </div>
                                <div x-show="isDone" x-cloak>
                                    <span class="badge bg-success">Done!</span>
                                </div>
                                <div x-show="!isFileUploaded && !isUploading">
                                    No file uploaded or file is empty.
                                </div>
                            </div>
                        </div>
                    @elseif($selectedOption === 'upload')
                        <div class="form-group">
                            <div x-data="{ isUploading: false, progress: 0, isDone: false, isFileUploaded: false }"
                                x-on:livewire-upload-start="isUploading = true; isDone = false; isFileUploaded = true"
                                x-on:livewire-upload-finish="isUploading = false; isDone = true; isFileUploaded = true"
                                x-on:livewire-upload-error="isUploading = false; isFileUploaded = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress">
                                <label for="terlambat">Import Excel</label>
                                <input type="file" class="form-control mb-2" wire:model="excelFile">
                                <div x-show="isUploading">
                                    <progress max="100" x-bind:value="progress"></progress>
                                    Uploading...
                                </div>
                                <div x-show="isDone" x-cloak>
                                    <span class="badge bg-success">Done!</span>
                                </div>
                                <div x-show="!isFileUploaded && !isUploading">
                                    No file uploaded or file is empty.
                                </div>
                            </div>
                        </div>
                    @endif

                @endif
            </div>
        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary me-2" wire:click="closeModal">Batal</button>
            <x-buttons.submit label="{{ $is_delete ? 'Delete' : 'Simpan' }}" target="submit"
                class="btn-{{ $is_delete ? 'danger' : 'primary' }} ml-1" />
        </x-slot:action>
    </x-modal.centered>
</div>
@push('script')
    <script>
        $(document).ready(function() {
            $('#{{ $modal }}').on('shown.bs.modal', function(e) {
                $('#user_id').select2({
                    dropdownParent: $("#{{ $modal }}")
                });
                $('#user_id').on('change', function(e) {
                    var data = $('#user_id').select2("val");
                    @this.set('user_id', data);
                    @this.call('updateEmployeeId');
                });
            });
        });
    </script>
@endpush
