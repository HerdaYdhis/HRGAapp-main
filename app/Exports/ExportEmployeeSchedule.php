<?php

namespace App\Exports;

use App\Models\EmployeeSchedule;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ExportEmployeeSchedule implements FromCollection, WithHeadings
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

        $users = User::where('is_active', 1)->get();


        foreach ($users as $user) {
            $employeeId = $user->employee_id;
            $employeeName = $user->name;
            $department = $user->department->name ?? null;
            $branch = $user->branch->name ?? null;

            $formattedData[$employeeId]['employee_id'] = $employeeId;
            $formattedData[$employeeId]['employee_name'] = $employeeName;
            $formattedData[$employeeId]['department'] = $department;
            $formattedData[$employeeId]['branch'] = $branch;

            foreach ($uniqueDates as $date) {
                if (date('N', strtotime($date)) >= 6) {
                    $formattedData[$employeeId][$date] = 'dayoff';
                } else {
                    $formattedData[$employeeId][$date] = 'NS';
                }
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
