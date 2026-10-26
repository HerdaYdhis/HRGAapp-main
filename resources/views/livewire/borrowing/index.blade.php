@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Peminjaman Ruangan Meeting</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pinjam</li>
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
                                <h5 class="card-title">List Peminjaman</h5>
                                <div class="card-toolbar">
                                    <button type="button" wire:click="openModal" class="btn btn-primary me-2">
                                        Tambah Antrian
                                    </button>
                                </div>
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            <livewire:borrowing.datatable />
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Tambah' }} Peminjaman Ruang Meeting"
        form="submit">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <div class="row">
                <div class="form-group">
                    <label for="code">Tujuan Peminjaman</label>
                    <x-forms.text name="Tujuan Peminjaman" model="objective" />
                </div>
                <div class="form-group">
                    <label for="meetroom_id">Ruang Meeting</label>
                    <select class="form-select @error('meetroom_id') is-invalid @enderror" id="meetroom_id"
                        wire:model="meetroom_id">
                        <option value="">Pilih Ruangan</option>
                        @forelse ($meetingroom as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @empty
                            <option value="" disabled>Ruangan Meeting Tidak Ditemukan</option>
                        @endforelse
                    </select>
                    @error('meetroom_id')
                        <x-forms.error message="{{ $message }}" />
                    @enderror
                </div>

                <div class="form-group">
                    <label for="code">Tanggal Pinjam</label>
                    <x-forms.date name="Tanggal Pinjam" model="start_date" />
                </div>

                <div class="col-6">
                    <div class="form-group">
                        <label for="code">Waktu Mulai</label>
                        <x-forms.time name="Waktu Mulai" model="start_time" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="code">Waktu Selesai</label>
                        <x-forms.time name="Waktu Selesai" model="done_time" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="code">Note</label>
                    <x-forms.textarea name="Note" model="note" />
                </div>
            </div>
        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary me-2" wire:click="closeModal">Batal</button>
            <x-buttons.submit label="Simpan" target="submit" class="btn-primary ml-1" />
        </x-slot:action>
    </x-modal.centered>
</div>
