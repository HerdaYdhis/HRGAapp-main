@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Master Task</h3>
                    {{-- <p>Digunakan sebagai Master </p> --}}
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Master Task</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section mt-10">

            <div class="card card-body">
                <span>
                    <h4 class="text-warning">
                        Warning!
                    </h4>
                </span>
                <p>The activity is used as a task master, to create tasks, so you don't need to input it multiple times.
                </p>
                <span class="text-danger">Pay attention to the Due Date if it is monthly and project type before adding
                    it to the task</span>
            </div>
            <div class="row">
                <div class="col-12">
                    <x-alert.notification />
                    <x-layouts.card>
                        <x-slot:header>
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">List Master Task</h5>
                                @if (UserAccess::can('mastertask', 'manage-mastertask') || auth()->user()->is_superadmin)
                                    <div class="card-toolbar">
                                        <button type="button" wire:click="openModal" class="btn btn-primary me-2">
                                            Add New
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            @if (UserAccess::can('mastertask', 'list-mastertask') || auth()->user()->is_superadmin)
                                <livewire:master-task.datatable />
                            @endif
                        </x-slot:body>
                    </x-layouts.card>
                </div>
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
                            {{-- <option value="project" @if ($type == 'project') selected @endif>Project</option> --}}
                        </select>
                        @error('type')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </fieldset>
                </div>

                <div class="form-group" id="duedateField">
                    <label for="duedate">Due Date</label>
                    <x-forms.date name="Due Date" model="duedate" />
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
    <script defer>
        const typeSelect = document.getElementById('typeSelect');
        const duedateField = document.getElementById('duedateField');

        console.log('Initial selectedType:', typeSelect.value);

        const selectedType = typeSelect.value === 'daily';
        console.log('Initial selectedType after comparison:', selectedType);

        duedateField.style.display = selectedType ? 'none' : 'block';

        typeSelect.addEventListener('change', function() {
            const selectedType = typeSelect.value;

            console.log('Changed selectedType:', selectedType);

            if (selectedType === 'monthly' || selectedType === 'project') {
                duedateField.style.display = 'block';
            } else {
                duedateField.style.display = 'none';
            }
        });
    </script>
@endpush
