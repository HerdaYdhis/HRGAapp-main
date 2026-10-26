<div>
    <x-layouts.card>
        <x-slot:header>
            <div class="d-flex justify-content-between">
                <div class="row">
                    <h5 class="card-title">Rekap Data Canteen</h5>
                </div>
                <div class="card-toolbar">
                    <button type="button" wire:click="openModalDownload" class="btn btn-primary me-2">
                        <i class="bi bi-download"></i> Download
                    </button>
                </div>
            </div>
        </x-slot:header>
        <x-slot:body>
            {{-- <livewire:canteen.datatable-all> --}}
            <livewire:canteen.datatable-rekap />

        </x-slot:body>

    </x-layouts.card>
    <x-modal.centered _id="{{ $modal }}" title="Download Rekap Canteen" form="download">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <div class="row">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <x-forms.date name="Start Date" model="start_date" />
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <x-forms.date name="End Date" model="end_date" />
                </div>
                {{-- <div class="mb-3">
                    <label for="" class="form-label">Branch</label>
                    <select class="form-select form-select" wire:model="branch_id">
                        <option>Select one</option>
                        <option value="1">SAF</option>
                        <option value="2">GGJS</option>
                    </select>
                </div> --}}
            </div>
        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary me-2" wire:click="closeModal">Batal</button>
            <x-buttons.submit label="Simpan" target="submit" class="btn-primary ml-1" />
        </x-slot:action>
    </x-modal.centered>
</div>
@push('script')
@endpush
