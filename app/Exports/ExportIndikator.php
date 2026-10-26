<?php

namespace App\Exports;

use App\Models\Performance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithStyles;


class ExportIndikator implements FromCollection, WithHeadings
{

    public function headings(): array
    {
        return [
            'indikator',
            'bobot'
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Performance::where('user_id', session('sendUserId'))->leftJoin('users', 'performances.user_id', '=', 'users.id')
            ->select('indikator', 'bobot')
            ->get();
    }
}
