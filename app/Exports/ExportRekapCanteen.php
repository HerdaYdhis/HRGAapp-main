<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Branch;
use App\Models\Canteen;
use App\Models\EmployeeStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ExportRekapCanteen implements FromCollection, WithHeadings, WithEvents
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
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $formattedData = [];
        $allDates = $this->getUniqueDatesInRange();

        // dd($allDates);

        $price = DB::table('price_canteens')->where('status', 'active')->value('prices');

        // dd($this->data[1]);
        foreach ($this->data as $canteen) {
            if (isset($canteen['user']) && $canteen['user'] !== null) {
                $employeeId = $canteen['user']['employee_id'];
                $formattedData[$employeeId] = [
                    'employee_id' => $employeeId,
                    'employee_name' => $canteen['user']['name'],
                    'department' => $canteen['department']['name']
                ];
            }


            foreach ($allDates as $date) {
                $formattedData[$employeeId][$date] = '0';
            }
        }

        foreach ($this->data as $canteen) {
            if (isset($canteen['user']) && $canteen['user'] !== null) {
                $employeeId = $canteen['user']['employee_id'];
                $workDate = $canteen['created_at']->format('Y-m-d');
                $status = $canteen['eat'];

                if ($workDate >= $this->start_date && $workDate <= $this->end_date) {
                    $formattedData[$employeeId][$workDate] = empty($status) ? '0' : '1';
                }
            }
        }

        foreach ($formattedData as &$employeeData) {
            foreach ($allDates as $date) {
                if (!isset($employeeData[$date])) {
                    $employeeData[$date] = '0';
                }
            }

            $employeeData['total_hari'] = array_sum(array_slice($employeeData, 3));
            $employeeData['total_hari'] = $employeeData['total_hari'] > 0 ? $employeeData['total_hari'] : '0';

            $employeeData['total_bayar'] = $employeeData['total_hari'] * $price;

            $coa = $this->getCOAForEmployee($employeeData['employee_id']);
            $employeeData['coa'] = $coa;
            $costcenter = $this->getCostCenterForEmployee($employeeData['employee_id']);
            $employeeData['costcenter'] = $costcenter;
            $status = $this->getStatusEmployee($employeeData['employee_id']);
            $employeeData['status'] = $status;
            $branch = $this->getBranch($employeeData['employee_id']);
            $employeeData['branch'] = $branch;
        }
        $footerData = $this->getFooterData($allDates);
        $formattedData[] = $footerData;


        return collect(array_values($formattedData));
    }

    public function headings(): array
    {
        $uniqueDates = $this->getUniqueDatesInRange();
        $headings = ['Employee_id', 'NAMA', 'DEPT'];

        foreach ($uniqueDates as $date) {
            $formattedDate = \Carbon\Carbon::parse($date)->format('d');
            $headings[] = $formattedDate;
        }

        $headings[] = 'TOTAL HARI';
        $headings[] = 'Bayar Kantin';
        $headings[] = 'COA';
        $headings[] = 'Cost Center';
        $headings[] = 'STATUS';
        $headings[] = 'BRANCH';

        return $headings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Set the sheet here
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(40);

                // Set the text alignment to center and middle align
                $event->sheet->getStyle('A1:' . $event->sheet->getDelegate()->getHighestColumn() . '1')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $footerRow = $event->sheet->getDelegate()->getHighestRow();

                // Determining the cell range for the footer row
                $footerRange = 'A' . $footerRow . ':' . $event->sheet->getDelegate()->getHighestColumn() . $footerRow;

                // Getting the StyleFill object to set the background color
                $fill = $event->sheet->getStyle($footerRange)->getFill();
                $fill->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00'); // Replace 'FFFF00' with your desired color code
            },
        ];
    }

    protected function getFooterData($allDates)
    {
        $footerData = ['Total', '', ''];
        $price = DB::table('price_canteens')->where('status', 'active')->value('prices');

        foreach ($allDates as $date) {
            $totalHariColumn = 0;

            foreach ($this->data as $canteen) {
                $workDate = $canteen['created_at']->format('Y-m-d');
                $status = $canteen['eat'];

                if ($workDate == $date) {
                    $totalHariColumn += empty($status) ? 0 : 1;
                }
            }

            $footerData[] = $totalHariColumn;
        }

        $footerData[] = array_sum(array_slice($footerData, 3));
        $footerData[] = end($footerData) * $price;
        $footerData[] = '';
        $footerData[] = '';
        $footerData[] = '';
        return $footerData;
    }


    protected function getUniqueDatesInRange()
    {
        $startDate = \Carbon\Carbon::parse($this->start_date)->startOfDay();
        $endDate = \Carbon\Carbon::parse($this->end_date)->endOfDay();

        $filteredDates = $this->data->whereBetween('created_at', [$startDate, $endDate]);

        // return $filteredDates->pluck('created_at')->map(function ($date) {
        //     return $date->format('Y-m-d');
        // })->unique()->toArray();
        return $filteredDates->pluck('created_at')->map(function ($date) {
            return $date->format('Y-m-d');
        })->unique()->sort()->values()->toArray();
    }

    protected function getCOAForEmployee($employeeId)
    {
        $user = User::where('employee_id', $employeeId)->with('coa')->first();
        // dd($user->coa->name);
        if ($user && $user->coa) {
            return $user->coa->name;
        } else {
            return 0;
        }
    }

    protected function getCostCenterForEmployee($employeeId)
    {
        $user = User::where('employee_id', $employeeId)->with('costcenter')->first();
        // dd($user->coa->name);
        if ($user && $user->costcenter) {
            return $user->costcenter->name;
        } else {
            return "-";
        }
    }
    protected function getStatusEmployee($employeeId)
    {
        $user = User::where('employee_id', $employeeId)->first();
        $status_employee = EmployeeStatus::where('id', $user->status_employee_id)->first();
        if ($status_employee) {
            return $status_employee->name;
        } else {
            return "-";
        }
    }
    protected function getBranch($employeeId)
    {
        $user = User::where('employee_id', $employeeId)->first();
        $branch = Branch::where('id', $user->branch_id)->first();
        if ($branch) {
            return $branch->name;
        } else {
            return "-";
        }
    }
}
