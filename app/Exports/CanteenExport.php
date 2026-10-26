<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Canteen;
use App\Models\PriceCanteen;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CanteenExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        return Canteen::with(['user', 'department'])->
        // whereDate('canteens.created_at', now()->toDateString())
        whereBetween('canteens.created_at', [$startDate, $endDate])
            ->get();
    }


    public function headings(): array
    {

        $startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        $price = DB::table('price_canteens')->where('status', 'active')->value('prices');

        return
            [
                ['E Canteen Information System'],
            [
                'Total Price : ',
                Canteen::with(['user', 'department'])->
                    // whereDate('canteens.created_at', now()->toDateString())
                    whereBetween('canteens.created_at', [$startDate, $endDate])
                    ->count() * $price,
            ],
            [
                    'ID',
                    'Name',
                    'Eat',
                    'Department',
                    'Employee ID',
                    'Date of Eat',
                ],

            ];
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '8fddf2']],
        ];

        $stylessum = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $styleshead = [
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'], // Set text color to white
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '25396f']],
        ];

        $stylescontent = [
            1 => [
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                'borders' => ['bottom' => ['style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ],
        ];


        $sheet->getStyle('A:F')->applyFromArray($stylescontent);
        $sheet->getStyle('A1:F1')->applyFromArray($styleshead);
        $sheet->getStyle('A3:F3')->applyFromArray($styles);
        $sheet->getStyle('A2:F2')->applyFromArray($stylessum);
        // $sheet->getStyle('A' . ($sheet->getHighestRow() + 1) . ':F' . ($sheet->getHighestRow() + 1))->applyFromArray($footerStyle);
        $sheet->mergeCells('A1:F1');
    }


    public function map($canteen): array
    {
        return
            [
                [
                    $canteen->id,
                    $canteen->user->name,
                    $canteen->eat,
                    $canteen->department->name,
                    $canteen->user->employee_id,
                Carbon::parse($canteen->created_at)->format('d/m/Y'),

            ],
            ];
    }
}
