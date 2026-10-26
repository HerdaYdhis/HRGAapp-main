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

class ExportIndikatorTemplate implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'List Kinerja';
    }

    public function headings(): array
    {
        return [
            'INDIKATOR',
            'BOBOT',
        ];
    }

    public function array(): array
    {
        return [
            ['Ketepatan waktu penyelesaian tugas', 30],
            ['Kualitas hasil pekerjaan sesuai standar', 40],
            ['Inisiatif dan kedisiplinan kerja', 30],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 45,
            'B' => 15,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(22);
        $sheet->getRowDimension(4)->setRowHeight(22);

        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
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
            4 => [
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            'B2:B4' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
            'A1:B4' => [
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
