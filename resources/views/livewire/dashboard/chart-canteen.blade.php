<div>
    <div class="card mx-auto">
        <div class="card-header text-center">
            <h5> Pemesanan Hari Ini </h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-center flex-sm-row flex-column">
                @foreach ($shiftCountsDay as $shift)
                    <div class="card m-3 shadow justify-content-center align-items-center">
                        <div class="card-header">
                            <h4>Shift : {{ $shift->shift_name }} </h4>
                        </div>
                        <div class="card-body">
                            Shift Count: <h5>{{ $shift->shift_count }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card shadow text-center">
                <div class="card-body">
                    <h5>Total : {{ $totalWithoutDayoffDay }}</h5>
                    <h5>Total Scan : {{ $totalScanToday }}</h5>
                    {{-- <h5>Total tidak Scan : {{ $totalNotScanToday }}</h5> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="card mx-auto">
        <div class="card-header text-center">
            <h5> Pemesanan Besok </h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-center flex-sm-row flex-column">
                @foreach ($shiftCountsTomorrow as $shift)
                    <div class="card m-3 shadow justify-content-center align-items-center">
                        <div class="card-header">
                            <h4>Shift : {{ $shift->shift_name }} </h4>
                        </div>
                        <div class="card-body">
                            Shift Count: <h5>{{ $shift->shift_count }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card shadow text-center">
                <div class="card-body">
                    <h5>Total : {{ $totalWithoutDayoffTomorrow }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="card mx-auto">
        <div class="card-header text-center">
            <h5> Pemesanan Lusa </h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-center flex-sm-row flex-column">
                @foreach ($shiftCountsLusa as $shift)
                    <div class="card m-3 shadow justify-content-center align-items-center">
                        <div class="card-header">
                            <h4>Shift : {{ $shift->shift_name }} </h4>
                        </div>
                        <div class="card-body">
                            Shift Count: <h5>{{ $shift->shift_count }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card shadow text-center">
                <div class="card-body">
                    <h5>Total : {{ $totalWithoutDayoffLusa }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="card mx-auto">
        {{-- kotak atas --}}
        <div class="card-header text-center">
            @php
                $WeekStartDate = \Carbon\Carbon::parse($WeekStartDate)->format('d F Y');
                $WeekEndDate = \Carbon\Carbon::parse($WeekEndDate)->format('d F Y');
            @endphp
            <h5> Pemesanan pada tanggal {{ $WeekStartDate }} - {{ $WeekEndDate }} </h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-center flex-sm-row flex-column">
                @foreach ($shiftCountsWeek as $shift)
                    <div class="card m-3 shadow justify-content-center align-items-center">
                        <div class="card-header">
                            <h4>Shift : {{ $shift->shift_name }} </h4>
                        </div>
                        <div class="card-body">
                            Shift Count: <h5>{{ $shift->shift_count }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card shadow text-center">
                <div class="card-body">
                    <h5>Total : {{ $totalWithoutDayoffWeek }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="card mx-auto">
        {{-- kotak atas --}}
        <div class="card-header text-center">
            @php
                $startDate = \Carbon\Carbon::parse($startDate)->format('d F Y');
                $endDate = \Carbon\Carbon::parse($endDate)->format('d F Y');
            @endphp
            <h5>Rencana pemesanan pada tanggal {{ $startDate }} - {{ $endDate }} (minggu depan)</h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-center flex-sm-row flex-column">
                @foreach ($shiftCounts as $shift)
                    <div class="card m-3 shadow justify-content-center align-items-center">
                        <div class="card-header">
                            <h4>Shift : {{ $shift->shift_name }} </h4>
                        </div>
                        <div class="card-body">
                            Shift Count: <h5>{{ $shift->shift_count }}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card shadow text-center">
                <div class="card-body">
                    <h5>Total : {{ $totalWithoutDayoff }}</h5>
                </div>
            </div>
        </div>
    </div>

</div>
