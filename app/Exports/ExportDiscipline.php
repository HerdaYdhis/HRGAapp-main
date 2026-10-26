<?php

namespace App\Exports;

use App\Models\Discipline;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithStyles;

class ExportDiscipline implements FromCollection, WithHeadings, WithColumnFormatting
{
    public function headings(): array
    {
        return [
            'Employee_id',
            'Employee_name',
            'Alfa',
            'Sakit',
            'Izin',
            'Terlambat_5_20',
            'Terlambat_21_60',
            'Terlambat_60',
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
        return Discipline::leftJoin('users', 'disciplines.user_id', '=', 'users.id')
            ->select('users.employee_id as employee_id', 'users.name as user', 'alfa', 'sakit', 'izin', 'terlambat1', 'terlambat2', 'terlambat3', 'periode')
            ->get();
    }
}
