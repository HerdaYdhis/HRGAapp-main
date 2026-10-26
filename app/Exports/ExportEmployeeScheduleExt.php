<?php

namespace App\Exports;

use App\Models\Canteen;
use App\Models\EmployeeSchedule;
use App\Models\EmployeeStatus;
use App\Models\MealShift;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ExportEmployeeScheduleExt implements FromCollection, WithHeadings
{
    protected $data;
    protected $start_date;
    protected $end_date;


    public function __construct($data, $start_date, $end_date)
    {
        $this->data = $data;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        $formattedData = [];
        $uniqueDates = $this->getUniqueDatesInRange();

        $users = EmployeeSchedule::whereBetween('work_date', [$this->start_date, $this->end_date])->with('user', 'user.department', 'meal_shifts', 'user.branch')->get();

        // dd($users);
        foreach ($users as $user) {
            // dd($user['user']['name']);
            $employeeId = $user['user']['employee_id'];
            $employeeName = $user['user']['name'];
            $department = $user['user']['department']['name'] ?? null;
            $branch = $user['user']['branch']['name'] ?? null;

            $formattedData[$employeeId]['employee_id'] = $employeeId;
            $formattedData[$employeeId]['employee_name'] = $employeeName;
            $formattedData[$employeeId]['department'] = $department;
            $formattedData[$employeeId]['branch'] = $branch;

            foreach ($uniqueDates as $date) {
                $shift = EmployeeSchedule::where('work_date', $date)->where('employee_id', $employeeId)->with('meal_shifts')->first();
                if ($shift) {
                    $shiftName = $shift->meal_shifts ? $shift->meal_shifts->shift_name : null;
                } else {
                    $shiftName = 'dayoff';
                }
                $formattedData[$employeeId][$date] = $shiftName;
            }
        }

        return collect(array_values($formattedData));
    }


    public function headings(): array
    {
        $uniqueDates = $this->getUniqueDatesInRange();

        $headings = ['Employee_id', 'Employee_name', 'Department', 'Branch'];
        foreach ($uniqueDates as $date) {
            $headings[] = $date;
        }

        return $headings;
    }

    protected function getUniqueDatesInRange()
    {
        $startDate = \Carbon\Carbon::parse($this->start_date);
        $endDate = \Carbon\Carbon::parse($this->end_date);

        $uniqueDates = [];
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $uniqueDates[] = $date->toDateString();
        }

        return $uniqueDates;
    }
}
