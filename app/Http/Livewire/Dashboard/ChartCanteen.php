<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeSchedule;
use App\Models\Canteen;

class ChartCanteen extends Component
{
    public $shiftCounts;
    public $totalWithoutDayoff;
    public $startDate;
    public $endDate;
    public $WeekStartDate;
    public $WeekEndDate;
    public $totalWithoutDayoffWeek;
    public $shiftCountsWeek;
    public $shiftCountsTomorrow;
    public $todayStartDate;
    public $todayEndDate;
    public $totalWithoutDayoffDay;
    public $totalWithoutDayoffTomorrow;
    public $shiftCountsDay;
    public $totalScanToday;
    public $totalNotScanToday;
    public $totalWithoutDayoffLusa;
    public $shiftCountsLusa;


    public function mount()
    {
        $this->calculateShiftCounts();
    }

    private function calculateShiftCounts()
    {
        //Untuk minggu depan
        $today = \Carbon\Carbon::now();
        $daysUntilSunday = 7 - $today->dayOfWeek;
        $startDate = $today->addDays($daysUntilSunday + 1)->startOfDay();
        $endDate = $startDate->copy()->addDays(6)->endOfDay();
        $this->startDate = $startDate->toDateString();
        $this->endDate = $endDate->toDateString();

        $this->totalWithoutDayoff = EmployeeSchedule::join('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
            ->whereBetween(DB::raw('DATE(employee_schedules.work_date)'), [$this->startDate, $this->endDate])
            ->where('meal_shifts.shift_name', '<>', 'dayoff')
            ->count();

        $this->shiftCounts = EmployeeSchedule::join('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
            ->whereBetween(DB::raw('DATE(employee_schedules.work_date)'),  [$this->startDate, $this->endDate])
            ->where('meal_shifts.shift_name', '<>', 'dayoff')
            ->select('meal_shifts.shift_name', DB::raw('COUNT(*) as shift_count'))
            ->groupBy('meal_shifts.shift_name')
            ->get();



        // Untuk minggu ini
        $today = \Carbon\Carbon::now();
        $thisWeekStartDate = $today->startOfWeek()->startOfDay();
        $thisWeekEndDate = $thisWeekStartDate->copy()->addDays(6)->endOfDay();

        $this->WeekStartDate = $thisWeekStartDate;
        $this->WeekEndDate = $thisWeekEndDate;

        $this->totalWithoutDayoffWeek = EmployeeSchedule::join('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
            ->whereBetween(DB::raw('DATE(employee_schedules.work_date)'), [$this->WeekStartDate, $this->WeekEndDate])
            ->where('meal_shifts.shift_name', '<>', 'dayoff')
            ->count();

        $this->shiftCountsWeek = EmployeeSchedule::join('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
            ->whereBetween(DB::raw('DATE(employee_schedules.work_date)'),  [$this->WeekStartDate, $this->WeekEndDate])
            ->where('meal_shifts.shift_name', '<>', 'dayoff')
            ->select('meal_shifts.shift_name', DB::raw('COUNT(*) as shift_count'))
            ->groupBy('meal_shifts.shift_name')
            ->get();


        //untuk hari ini
        $today = \Carbon\Carbon::now()->toDateString();

        $this->totalWithoutDayoffDay = EmployeeSchedule::join('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
            ->where('employee_schedules.work_date', $today)
            ->where('meal_shifts.shift_name', '<>', 'dayoff')
            ->count();

        $this->shiftCountsDay = EmployeeSchedule::join('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
            ->where('employee_schedules.work_date', $today)
            ->where('meal_shifts.shift_name', '<>', 'dayoff')
            ->select('meal_shifts.shift_name', DB::raw('COUNT(*) as shift_count'))
            ->groupBy('meal_shifts.shift_name')
            ->get();

        $this->totalScanToday = Canteen::where('eat', 1)->whereDate('created_at', $today)->count();
        $this->totalNotScanToday = Canteen::where('eat', 0)->whereDate('created_at', $today)->count();
        // dd($this->totalScanToday);

        //untuk besok
        $tomorrow = \Carbon\Carbon::tomorrow()->toDateString();
        $this->totalWithoutDayoffTomorrow = EmployeeSchedule::join('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
            ->where('employee_schedules.work_date', $tomorrow)
            ->where('meal_shifts.shift_name', '<>', 'dayoff')
            ->count();

        $this->shiftCountsTomorrow = EmployeeSchedule::join('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
            ->where('employee_schedules.work_date', $tomorrow)
            ->where('meal_shifts.shift_name', '<>', 'dayoff')
            ->select('meal_shifts.shift_name', DB::raw('COUNT(*) as shift_count'))
            ->groupBy('meal_shifts.shift_name')
            ->get();

        //untuk lusa
        $lusa = \Carbon\Carbon::now()->addDays(2)->toDateString();
        $this->totalWithoutDayoffLusa = EmployeeSchedule::join('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
            ->where('employee_schedules.work_date', $lusa)
            ->where('meal_shifts.shift_name', '<>', 'dayoff')
            ->count();

        $this->shiftCountsLusa = EmployeeSchedule::join('meal_shifts', 'employee_schedules.meal_shift_id', '=', 'meal_shifts.id')
            ->where('employee_schedules.work_date', $lusa)
            ->where('meal_shifts.shift_name', '<>', 'dayoff')
            ->select('meal_shifts.shift_name', DB::raw('COUNT(*) as shift_count'))
            ->groupBy('meal_shifts.shift_name')
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.chart-canteen');
    }
}
