@php use App\Utilities\Constant; @endphp
<div>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Task</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Task</li>
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
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="row d-flex align-items-stretch mb-3">
                                        <!-- Tiga box di sebelah kiri -->
                                        <div class="col-md-4 h-100 order-md-1 order-2">
                                            <div class="card bg-info mb-4 text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">Total Open</h5>
                                                    <p class="card-text">
                                                    <h4 style="color: white">{{ $open }}</h4>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="card bg-info mb-4 text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">Total Done</h5>
                                                    <p class="card-text">
                                                    <h4 style="color: white">{{ $sum }}</h4>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="card bg-info text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">Total Overdue</h5>
                                                    <p class="card-text">
                                                    <h4 style="color: white">{{ $overdue }}</h4>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Satu box di sebelah kanan -->
                                        <div class="col-md-8 order-md-2 order-1">
                                            <div class="card">
                                                <div class="card-body">
                                                    <x-charts.chart name="Percentage" chart="id=chart" />
                                                    {{-- <livewire:task.real-time-chart /> --}}
                                                    <div class="row">
                                                        <div class="col text-center">
                                                            <h4>{{ $emote }}
                                                                {{ $motivasi }}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="rounded-3 bg-info mb-5 p-5">
                                <h1 class="text-white"> <span id="emote-value">{{ $emote }}</span><span
                                        id="motivasi-value">{{ $motivasi }} </span></h1>
                                <h2 class="text-white">Total Done : <span id="sum-value">{{ $sum }}</span>
                                </h2>
                                <h2 class="text-white">Overdue : <span id="overdue-value">{{ $overdue }}</span>
                                </h2>
                                <h2 class="text-white">Percentage : <span id="percent-value">{{ $persen }}</span>
                                    %</h2>
                                <h2 class="text-white">Total Project : <span
                                        id="project-value">{{ $totalproject }}</span></h2>
                            </div> --}}

                            <div class="d-flex flex-md-row flex-column justify-content-between">
                                <h5 class="card-title">List Task</h5>

                                {{-- @if (UserAccess::can('task', 'manage-task') || auth()->user()->is_superadmin) --}}
                                <div class="card-toolbar">
                                    <button type="button" wire:click="submit" class="btn btn-primary me-2">
                                        Refresh Task
                                    </button>
                                    <a href="/task#list_project" class="btn btn-primary me-2">
                                        Go to Project
                                    </a>

                                </div>

                                {{-- @endif --}}
                            </div>

                        </x-slot:header>
                        <x-slot:body>
                            {{-- @if (UserAccess::can('task', 'list-task') || auth()->user()->is_superadmin) --}}
                            <div class="row">
                                <div class="my-2 border p-4">
                                    <h4 class="header">List Daily</h4>
                                    <fieldset class="rounded border p-4">
                                        <livewire:task.datatable-day />
                                    </fieldset>
                                </div>
                                <div class="my-2 border p-4">
                                    <h4 class="header">List Weekly</h4>
                                    <fieldset class="rounded border p-4">
                                        <livewire:task.datatable-week />
                                    </fieldset>
                                </div>
                                <div class="my-2 border p-4">
                                    <h4 class="header">List Monthly</h4>
                                    <fieldset class="rounded border p-4">
                                        <livewire:task.datatable-month />
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row border p-4" id="list_project">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <div class="card bg-info mb-4 text-white">
                                            <div class="card-body">
                                                <h5 class="card-title">Project Total</h5>
                                                <p class="card-text">
                                                <h2 style="color: white">{{ $totalproject }}</h2>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info mb-4 text-white">
                                            <div class="card-body">
                                                <h5 class="card-title">Project Done</h5>
                                                <p class="card-text">
                                                <h2 style="color: white">{{ $doneproject }}</h2>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info mb-4 text-white">
                                            <div class="card-body">
                                                <h5 class="card-title">Project Open</h5>
                                                <p class="card-text">
                                                <h2 style="color: white">{{ $openproject }}</h2>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info mb-4 text-white">
                                            <div class="card-body">
                                                <h5 class="card-title">Project Overdue</h5>
                                                <p class="card-text">
                                                <h2 style="color: white">{{ $overdueproject }}</h2>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-md-row flex-column justify-content-between">
                                    <div class="card-toolbar">
                                        <button type="button" wire:click="openModalProject"
                                            class="btn btn-primary me-2">
                                            Create Project Task
                                        </button>
                                    </div>
                                </div>
                                <div class="my-2 border p-4">
                                    <h4 class="header">List Project</h4>
                                    <livewire:task.datatable-project />
                                </div>
                            </div>
                            {{-- @endif --}}
                        </x-slot:body>
                    </x-layouts.card>
                </div>
            </div>
        </section>
    </div>

    <x-modal.centered _id="{{ $modal }}" title="{{ $is_edit ? 'Edit' : 'Tambah' }} Task"
        form="{{ $create_project ? 'project_insert' : 'submit' }}">
        <x-slot:content>
            <x-alert.validation />
            <x-alert.notification />
            <div class="row">
                @if ($create_project)
                    <div class="form-group">
                        <label for="title">Title</label>
                        <x-forms.text name="Title" model="title" />
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <x-forms.text name="Description" model="description" />
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="progress"
                                wire:model="progress">
                            <label class="form-check-label" for="progress">
                                Does this task require progress?
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="duedate">Due Date</label>
                        <x-forms.date name="Due Date" model="duedate" />
                    </div>
                @else
                    <div class="form-group">
                        <label for="status">Status</label>
                        <x-forms.select :datas="Constant::STATUS" value="value" label="label" name="status"
                            model="status" />

                        @error('status')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Doc Evident</label>
                        <input class="form-control" type="file" id="image" wire:model="image">
                        <div wire:loading wire:target="image">
                            <p class="text-primary mb-2 ms-1 mt-1">Uploading ...</p>
                        </div>
                        @error('image')
                            <x-forms.error message="{{ $message }}" />
                        @enderror
                    </div>
                @endif
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
        document.addEventListener('DOMContentLoaded', function() {

            var persen = '{{ $persen }}';
            var options = {
                chart: {
                    height: 265,
                    type: "radialBar",
                },

                series: [persen],
                colors: ["#20E647"],
                plotOptions: {
                    radialBar: {
                        hollow: {
                            margin: 0,
                            size: "70%",
                            background: "#293450"
                        },
                        track: {
                            dropShadow: {
                                enabled: true,
                                top: 2,
                                left: 0,
                                blur: 4,
                                opacity: 0.15
                            }
                        },
                        dataLabels: {
                            name: {
                                offsetY: -10,
                                color: "#fff",
                                fontSize: "13px"
                            },
                            value: {
                                color: "#fff",
                                fontSize: "30px",
                                show: true
                            }
                        }
                    }
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shade: "dark",
                        type: "vertical",
                        gradientToColors: ["#87D4F9"],
                        stops: [0, 100]
                    }
                },
                stroke: {
                    lineCap: "round"
                },
                labels: ["Percentage"]
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();

            function updateChart() {
                // Lakukan AJAX untuk mengambil data baru
                $.ajax({
                    url: '/getChartData',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Perbarui data chart dengan data baru
                        chart.updateSeries([data.persen]);
                    },
                    error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }


            // Set interval untuk memperbarui chart setiap 5 detik
            setInterval(updateChart, 3000);
        });
    </script>
@endpush
