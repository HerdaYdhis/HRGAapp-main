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
use Illuminate\Support\Facades\Session;


HeadingRowFormatter::default('none');

class ImportEmployeeSchedule implements ToCollection, WithHeadingRow
{
    use Importable;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $shifts = [];

        foreach ($rows as $row) {
            $employeeId = $row['Employee_id'];
            $employeeName = $row['Employee_name'];

            foreach ($row as $key => $value) {
                if ($key != 'Employee_id' && $key != 'Employee_name') {
                    $date = $this->convertToDate($key);
                    $shift = $value;
                    $shifts[] = [
                        'employee_id' => $employeeId,
                        'employee_name' => $employeeName,
                        'shift_name' => $shift,
                        'work_date' => $date,
                    ];
                }
            }
        }
        // dd($shifts);
        $invalidEmployeeIds = [];
        foreach ($shifts as $shift) {
            $meal = MealShift::where('shift_name', $shift['shift_name'])->first();
            if ($meal) {
                $existingSchedule = EmployeeSchedule::where([
                    'employee_id' => $shift['employee_id'],
                    'work_date' => $shift['work_date'],
                ])->first();

                if (!$existingSchedule) {
                    $user = User::where('employee_id', $shift['employee_id'])->first();
                    if ($user) {
                        $input = new EmployeeSchedule([
                            'user_id' => $user->id,
                            'employee_id' => $shift['employee_id'],
                            'meal_shift_id' => $meal->id ?? null,
                            'work_date' => $shift['work_date'],
                        ]);

                        if (!$input->save()) {
                            $invalidEmployees[] = [
                                'employee_id' => $shift['employee_id'],
                                'employee_name' => $shift['employee_name'],
                            ];
                        }
                    } else {
                        $invalidEmployees[] = [
                            'employee_id' => $shift['employee_id'],
                            'employee_name' => $shift['employee_name'],
                        ];
                        if (!empty($invalidEmployees)) {
                            $errorMessages = [];
                            foreach ($invalidEmployees as $invalidEmployee) {
                                $errorMessage = "Karyawan dengan ID {$invalidEmployee['employee_id']} (Nama: {$invalidEmployee['employee_name']}) tidak ditemukan. Tidak dapat membuat jadwal.";
                                $errorMessages[] = $errorMessage;
                            }

                            // Simpan pesan kesalahan ke dalam session
                            Session::put('error_messages', $errorMessages);
                        }
                    }
                }
            }
        }
    }
    private function convertToDate($value)
    {
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp(($value - 25569) * 86400)->format('Y-m-d');
        }

        return $value;
    }
}
