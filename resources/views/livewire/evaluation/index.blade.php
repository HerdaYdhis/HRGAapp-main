@php
    use App\Utilities\Constant;
    use App\Utilities\UserAccess;
@endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3 class="mb-1"><i class="bi bi-award text-primary me-2"></i>Penilaian Kinerja</h3>
                    <p class="text-subtitle text-secondary mb-0">Input dan evaluasi capaian indikator KPI karyawan per
                        periode</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first mb-md-0 mb-3">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Penilaian Kinerja</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="row">
                <div class="col-12">
                    <x-alert.notification />
                    <x-layouts.card>
                        <x-slot:header>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                @if (!$select_user)
                                    <div>
                                        <h5 class="card-title mb-0">Pilih Karyawan & Periode Penilaian</h5>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-3">
                                        <h5 class="card-title mb-0">
                                            Penilaian: <span class="text-primary fw-bold">{{ $username }}</span>
                                            <span class="badge bg-primary text-white ms-2">Periode
                                                {{ $periode ? \Carbon\Carbon::parse($periode)->format('F Y') : '-' }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-toolbar">
                                        <button class="btn btn-secondary btn-sm d-inline-flex align-items-center"
                                            wire:click="back">
                                            <i class="bi bi-arrow-left me-1"></i> Back
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </x-slot:header>
                        <x-slot:body>
                            @if (!$select_user)
                                <x-alert.validation />
                                <x-alert.notification />
                                <form wire:submit.prevent="user_submit" class="py-2">
                                    <div class="row">
                                        <div class="col-xl-5 col-lg-6 col-md-8 col-12" style="max-width: 480px;">
                                            @if (UserAccess::can('evaluation', 'manage-evaluation') || auth()->user()->is_superadmin)
                                                <div class="form-group mb-3">
                                                    <label for="user_id" class="form-label fw-bold">
                                                        <i class="bi bi-person-fill text-primary me-2"></i>Nama Karyawan
                                                    </label>
                                                    <fieldset class="form-group mb-0">
                                                        <div wire:ignore>
                                                            <select class="form-select @error('user_id') is-invalid @enderror"
                                                                id="{{ 'user_id' }}" wire:model.defer="user_id" style="width: 100%;">
                                                                <option value="">-- Pilih Karyawan --</option>
                                                                @forelse ($userdata as $user)
                                                                    <option value="{{ $user->id }}">
                                                                        {{ $user->name }}
                                                                        ({{ $user->employee_id ?? 'ID: ' . $user->id }})
                                                                    </option>
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
                                            @endif

                                            <div class="form-group mb-4">
                                                <label for="periode" class="form-label fw-bold">
                                                    <i class="bi bi-calendar-event text-primary me-2"></i>Periode Penilaian
                                                </label>
                                                <x-forms.month name="Perode" model="periode" />
                                                <small class="text-secondary d-block mt-1">Pilih bulan dan tahun evaluasi kinerja.</small>
                                            </div>

                                            <div class="pt-1">
                                                <button type="submit" class="btn btn-primary px-4 py-2 d-inline-flex align-items-center shadow-sm">
                                                    <i class="bi bi-play-circle me-2"></i> Mulai Penilaian
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endif

                            @if ($othersVisible)
                                <div class="table-responsive mt-2">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-3 text-center" style="width: 50px;">No</th>
                                                <th class="py-3">Indikator Kinerja</th>
                                                <th class="py-3 text-center" style="width: 100px;">Bobot</th>
                                                <th class="py-3 text-center" style="width: 130px;">Nilai Capaian</th>
                                                <th class="py-3 text-center" style="width: 130px;">Nilai Akhir</th>
                                                @if (UserAccess::can('evaluation', 'manage-evaluation') || auth()->user()->is_superadmin)
                                                    <th class="py-3 text-center" style="width: 130px;">Aksi</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($performances as $performance)
                                                <tr>
                                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                                    <td class="fw-semibold">{{ $performance->indikator }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary text-white fw-bold px-3 py-2">
                                                            {{ $performance->bobot }}%
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @forelse ($performance->evaluation as $data)
                                                            <span class="fw-bold fs-6">{{ $data->nilai }}</span>
                                                        @empty
                                                            <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                                                                <i class="bi bi-clock me-1"></i> Belum terisi
                                                            </span>
                                                        @endforelse
                                                    </td>
                                                    <td class="text-center">
                                                        @forelse ($performance->evaluation as $data)
                                                            <span class="fw-bold text-success fs-6">{{ $data->nilai_akhir }}</span>
                                                        @empty
                                                            <span class="text-secondary">-</span>
                                                        @endforelse
                                                    </td>
                                                    @if (UserAccess::can('evaluation', 'manage-evaluation') || auth()->user()->is_superadmin)
                                                        <td class="text-center">
                                                            @if ($performance->evaluation && $performance->evaluation->isNotEmpty())
                                                                @foreach ($performance->evaluation as $data)
                                                                    <button
                                                                        class="btn btn-warning btn-sm d-inline-flex align-items-center px-3"
                                                                        wire:click="$emit('edit', {{ $data->id }})">
                                                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                                                    </button>
                                                                @endforeach
                                                            @else
                                                                <button
                                                                    class="btn btn-primary btn-sm d-inline-flex align-items-center px-3"
                                                                    wire:click="$emit('input_nilai', {{ $performance->id }})">
                                                                    <i class="bi bi-plus-lg me-1"></i> Input
                                                                </button>
                                                            @endif
                                                        </td>
                                                    @endif
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4 text-secondary">
                                                        Belum ada indikator kinerja yang dibuat untuk karyawan ini. Silakan
                                                        tambahkan di menu <strong>List Kinerja</strong> terlebih dahulu.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="Input Nilai Kinerja" form="submit">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <input type="hidden" name="user_id" value="{{ $othersVisible }}">
            <div class="row g-3">
                <div class="col-12">
                    <label for="title" class="form-label fw-bold">Nilai Capaian (Skala 0 - 100)</label>
                    <x-forms.number name="Nilai" model="nilai" placeholder="Masukkan nilai capaian kinerja..." />
                    <small class="text-secondary d-block mt-1">Nilai akhir akan otomatis dihitung berdasarkan bobot
                        persentase indikator.</small>
                </div>
            </div>
        </x-slot:content>
        <x-slot:action>
            <button type="button" class="btn btn-secondary px-3" wire:click="closeModal">Batal</button>
            <x-buttons.submit label="Simpan" target="submit" class="btn-primary px-4" />
        </x-slot:action>
    </x-modal.centered>
</div>

@push('script')
    <script>
        $(document).ready(function () {
            $('#user_id').select2();
            $('#user_id').on('change', function (e) {
                var data = $('#user_id').select2("val");
                @this.set('user_id', data);
            });

            Livewire.on('select2-reinitialize', function () {
                $('#user_id').select2();
                $('#user_id').on('change', function (e) {
                    var data = $('#user_id').select2("val");
                    @this.set('user_id', data);
                });
            });
        });
    </script>
@endpush