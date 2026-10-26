<?php

namespace App\Imports;

use App\Models\EmployeeSchedule;
use App\Models\User;
use App\Models\MealShift;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

HeadingRowFormatter::default('none');

class ImportUpdateEmployeeSchedule implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $employeeId = $row['Employee_id'];
            $employeeName = $row['Employee_name'];

            foreach ($row as $key => $value) {
                if ($key != 'Employee_id' && $key != 'Employee_name'  && $key != 'Department' && $key != 'Branch') {
                    $date = $this->convertToDate($key);
                    $shift = $value;
                    // dd($date);

                    // Check if the schedule already exists
                    $existingSchedule = EmployeeSchedule::where([
                        'employee_id' => $employeeId,
                        'work_date' => $date,
                    ])->first();

                    if ($existingSchedule) {
                        // Update the existing schedule
                        $existingSchedule->update([
                            'employee_id' => $employeeId,
                            'meal_shift_id' => $this->getMealShiftId($shift),
                            'work_date' => $date,
                        ]);
                    }
                }
            }
        }
    }

    private function getMealShiftId($shift)
    {
        // Add logic to get the meal shift ID based on the shift name
        $meal = MealShift::where('shift_name', $shift)->first();
        return $meal ? $meal->id : null;
    }

    private function convertToDate($value)
    {
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp(($value - 25569) * 86400)->format('Y-m-d');
        }

        return $value;
    }
}
