<div>
    <x-layouts.card>

        <x-slot:header>

            <div class="d-flex justify-content-between">
                <h5 class="card-title">Employee Statistics</h5>
                <h7 class="card-title bg-gradient-ltr rounded-3 p-2 text-white">Real-time statistics for this month, up
                    to today</h7>
            </div>
        </x-slot:header>
        <x-slot:body>
            <div class="row mt-4">

                <div class="col-lg-4">

                    <div class="form-group rounded-3 bg-info my-2 p-4 shadow mb-3 rounded">
                        <fieldset class="form-group">
                            <select class="form-select @error('selectedUserId') is-invalid @enderror" id="selectedUserId"
                                wire:model.lazy="selectedUserId" wire:change="loadData">
                                <option value="">Select or Reset User</option>
                                @forelse ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @empty
                                    <option value="" disabled>User Tidak Ditemukan</option>
                                @endforelse
                                {{-- {{dd($users)}} --}}
                            </select>
                            @error('selectedUserId')
                                <x-forms.error message="{{ $message }}" />
                            @enderror
                        </fieldset>
                    </div>

                    <div class="rounded-3 bg-info my-2 p-4 shadow mb-3 rounded">
                        <h4 class="py-10 text-white">Total Open : <span id="overdue-value">{{ $open }}</span>
                        </h4>
                        <h4 class="py-10 text-white">Total Done : <span id="sum-value">{{ $sum }}</span> </h4>
                        <h4 class="py-10 text-white">Done Overdue : <span id="overdue-value">{{ $overdue }}</span>
                        </h4>
                    </div>

                    <div class="rounded-3 bg-info my-2 p-4 shadow mb-3 rounded">
                        <h4 class="py-10 text-white">Project Open : <span
                                id="projectopen-value">{{ $openproject }}</span></h4>
                        <h4 class="py-10 text-white">Project Done : <span
                                id="projectdone-value">{{ $doneproject }}</span> </h4>
                        <h4 class="py-10 text-white">Project Overdue : <span
                                id="projectoverdue-value">{{ $overdueproject }}</span></h4>
                    </div>

                </div>
                <div class="col-lg-8">
                    <div class="row py-10 ">
                        <div class="col-lg-1 text-4xl" id="emote-value">{{ $emote }}</div>
                        <div class="text-bold col-lg-8 text-4xl" id="motivasi-value">{{ $motivasi }}</div>
                    </div>
                    <div class="progress rounded-4" style="height: 60px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info shadow-lg" role="progressbar"
                            aria-valuenow="75" aria-valuemin="100" aria-valuemax="50"
                            style="width: {{ $persen }}%; height: 100%;"></div>
                    </div>

                    <div class="text-bold text-xl font-semibold">Percen : <span
                            id="percent-value">{{ $persen }}</span> %</div>
                </div>

                <div class="pb-2 pt-5">
                    <h7 class="card-title bg-gradient-ltr rounded-3 p-2 text-white">Selected monthly statistics</h7>
                </div>

                <div class="py-3">
                    <x-charts.chart name="Statistics" chart="id=chart-select" />

                </div>

                <div class="py-3"></div>
            </div>
        </x-slot:body>
    </x-layouts.card>
</div>

@push('script')
    <script>
        document.addEventListener('livewire:load', () => {

            @this.on('refreshNilai', (data) => {
                const open = data.open;
                const sum = data.sum;
                const percent = data.percent;
                const overdue = data.overdue;
                const emote = data.emote;
                const motivasi = data.motivasi;
                const doneproject = data.doneproject;
                const openproject = data.openproject;
                const overdueproject = data.overdueproject;



                document.getElementById('motivasi-value').textContent = motivasi;
                document.getElementById('emote-value').textContent = emote;
                document.getElementById('open-value').textContent = open;
                document.getElementById('sum-value').textContent = sum;
                document.getElementById('overdue-value').textContent = overdue;
                document.getElementById('percent-value').textContent = percent;
                document.getElementById('projectdone-value').textContent = doneproject;
                document.getElementById('projectopen-value').textContent = openproject;
                document.getElementById('projectoverdue-value').textContent = overdueproject;

            });

            @this.on('refreshChartData', (chartData) => {
                var options = {
                    chart: {
                        height: 280,
                        type: "line",
                        zoom: {
                            enabled: false
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetX: -6,
                        style: {
                            fontSize: '12px',
                            colors: ['#435ebe']
                        },
                    },
                    markers: {
                        size: 2,
                    },
                    stroke: {
                        curve: 'straight'
                    },
                    series: [{
                        name: chartData.seriesName,
                        data: chartData.seriesData
                    }],
                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.9,
                            stops: [0, 90, 100]
                        }
                    },
                    xaxis: {
                        categories: chartData.categories
                    },
                    tooltip: {}
                };

                var chart = new ApexCharts(document.getElementById('chart-select'), options);
                chart.render();
            });
        });
    </script>
@endpush
