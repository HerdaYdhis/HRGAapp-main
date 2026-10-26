<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportDisciplineTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Kedisiplinan';
    }

    public function headings(): array
    {
        return [
            'employee_id',
            'alfa',
            'sakit',
            'izin',
            'terlambat_5_20',
            'terlambat_21_60',
            'terlambat_60',
            'periode_yyyy_mm',
        ];
    }

    public function array(): array
    {
        return [
            ['Herda (ACAD123)', 0, 0, 0, 0, 0, 0, '2024-10'],
            ['Admin (ADM001)', 1, 0, 0, 1, 0, 0, '2024-10'],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 10,
            'C' => 10,
            'D' => 10,
            'E' => 18,
            'F' => 18,
            'G' => 18,
            'H' => 20,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(22);

        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
            2 => [
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            3 => [
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            'B2:H3' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
            'A1:H3' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFCBD5E1'],
                    ],
                ],
            ],
        ];
    }
}
