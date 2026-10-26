<div>
    <style>
        /* CSS */
        #preview {
            transform: scaleX(1);
        }
    </style>
    <x-layouts.card>
        <x-slot:header>
            <div class="d-flex flex-md-row flex-column justify-content-between">
                <h5 class="card-title">Canteen Result</h5>
                <div class="card-toolbar text-center">
                    <button type="button" wire:click="openModal" class="btn btn-primary mb-2">Tambah</button>
                    <button type="button" wire:click="openModalExcel" class="btn btn-primary mb-2">
                        <i class="bi bi-upload"></i> Upload Update
                    </button>
                </div>
            </div>
        </x-slot:header>
        <x-slot:body>
            <x-alert.notification />
            <div class="row rounded-2 border-3 py-4">
                <div class="rounded-2 py-4">
                    <livewire:canteen.datatable-scan />
                </div>
            </div>
        </x-slot:body>
    </x-layouts.card>


    <x-modal.centered _id="{{ $modal }}" title="{{ $is_upload ? 'Upload' : ($is_edit ? 'Edit' : 'Tambah') }}"
        form="{{ $is_upload ? 'import' : 'submit' }}">
        <x-slot:content>
            @if ($is_upload && !$is_edit)
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
            @elseif (!$is_upload && $is_edit)
                <div class="form-group">
                    <label for="eat">Eat Status</label>
                    <fieldset class="form-group">
                        {{-- <div wire:ignore> --}}
                        <select class="form-select @error('eat') is-invalid @enderror" id="{{ 'eat' }}"
                            wire:model.defer="eat">
                            <option value disabled="">Select Status</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        {{-- </div> --}}
                        @error('eat')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="user_id">Shift Name</label>
                    <fieldset class="form-group">
                        <div wire:ignore>
                            <select class="form-select @error('meal_shift_id') is-invalid @enderror"
                                id="{{ 'meal_shift_id' }}" wire:model.defer="meal_shift_id">
                                <option value=" ">Select shift</option>
                                @forelse ($meal_shifts as $meal)
                                    <option value="{{ $meal->id }}">
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
            @else
                <div class="form-group">
                    <label for="user_id">User</label>
                    <fieldset class="form-group">
                        <div wire:ignore>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="{{ 'user_id' }}"
                                wire:model.defer="user_id">
                                <option value="">Select User</option>
                                @forelse ($userdata as $user)
                                    <option value="{{ $user->employee_id }}">
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
                <div class="form-group">
                    <label for="user_id">Shift Name</label>
                    <fieldset class="form-group">
                        <div wire:ignore>
                            <select class="form-select @error('meal_shift_id') is-invalid @enderror"
                                id="{{ 'meal_shift_id' }}" wire:model.defer="meal_shift_id">
                                <option value=" ">Select shift</option>
                                @forelse ($meal_shifts as $meal)
                                    <option value="{{ $meal->id }}">
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
                    <label for="">Work Date</label>
                    <x-forms.date name="Work Date" model="work_date" />
                </div>
            @endif
            {{-- @endif --}}
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
            $('#{{ $modal }}').on('shown.bs.modal', function(e) {
                $('#user_id').select2({
                    dropdownParent: $("#{{ $modal }}")
                });
                $('#user_id').on('change', function(e) {
                    var data = $('#user_id').select2("val");
                    @this.set('user_id', data);
                });
            });
        });
    </script>
@endpush
