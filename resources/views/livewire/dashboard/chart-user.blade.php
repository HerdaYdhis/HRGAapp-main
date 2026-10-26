<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
    <x-layouts.card>
        <x-slot:header>
            <h7 class="card-title bg-gradient-ltr rounded-3 p-2 text-white">Your monthly statistics</h7>
        </x-slot:header>

        <x-slot:body>
            <x-charts.chart name="Employee statistics alone" chart="id=chart-user" />
            {{-- {{$use}} --}}
        </x-slot:body>
    </x-layouts.card>

</div>

@push('script')
    <script>
        var options = {
            chart: {
                height: 280,
                type: "line",
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 2,
            },
            stroke: {
                curve: 'straight'
                // curve: 'smooth',
            },
            series: [{
                name: "Total",
                data: @json($usersStatistics)
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
                categories: @json($month),
            }
        };
        new ApexCharts(document.getElementById('chart-user'), options).render();
    </script>
@endpush
