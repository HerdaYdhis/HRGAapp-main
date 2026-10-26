<?php

namespace App\Exports;

use App\Models\WarningLetter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExportWarningLetter implements FromCollection, WithHeadings, WithColumnFormatting
{

    public function headings(): array
    {
        return [
            'Employee_id',
            'Employee_name',
            'SP1',
            'SP2',
            'SP3',
            'Periode_yyyy_mm',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '@',
            'C' => '0',
            'D' => '0',
            'E' => '0',
            'A' => '@',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return WarningLetter::leftJoin('users', 'warning_letters.user_id', '=', 'users.id')
            ->select('users.employee_id as employee_id', 'users.name as employee_name', 'sp1', 'sp2', 'sp3', 'periode')
            ->get();
    }
}
