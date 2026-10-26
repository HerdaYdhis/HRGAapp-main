<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\CanteenOutput;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CanteenOutputExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return CanteenOutput::with('user:id,employee_id,name,coa_id,costcenter_id,status_employee')->with('department:id,name')->with('coa:id,name')->select('canteen_outputs.*')->get();
    }


    public function headings(): array
    {
        $firstDayOfMonth = now()->firstOfMonth();
        $fourteenthDayOfMonth = now()->firstOfMonth()->addDays(15);

        $headings = [
            ['E Canteen Information System'],
            [
                'NO',
                'Employee ID',
                'Name',
                'Department',
            ],
        ];

        if (now() >= $firstDayOfMonth && now() <= $fourteenthDayOfMonth) {
            for ($i = 1; $i <= 15; $i++) {
                $headings[1][] = $i;
            }
        } else {
            $endOfMonthDay = now()->endOfMonth()->format('d');

            for ($i = 16; $i <= $endOfMonthDay; $i++) {
                $headings[1][] = $i;
            }
        }

        $headings[1] = array_merge($headings[1], [
            'Total',
            'Total Payout',
            'COA',
            'Cost Center',
            'Status',
        ]);

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            'font' => ['bold' => true, 'size' => 12, 'name' => 'Arial Narrow'], // Set font to Arial Narrow
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '8fddf2']],
        ];

        $styleshead = [
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'], // Set text color to white
                'name' => 'Arial Narrow', // Set font to Arial Narrow
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '25396f']],
        ];

        $stylescontent = [
            'font' => [
                'name' => 'Arial Narrow', // Set font to Arial Narrow
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $currencyStyle = [
            'numberFormat' => [
                'formatCode' => '[$Rp-421] #,##0.00',
            ],
        ];

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray($stylescontent); // Apply styles to the entire range
        $sheet->getStyle('U2:U' . $highestRow)->applyFromArray($currencyStyle);
        $sheet->getStyle('A1:X1')->applyFromArray($styleshead);
        $sheet->getStyle('A2:X2')->applyFromArray($styles);
        $sheet->mergeCells('A1:X1');
    }






    public function map($canteen): array
    {
        $firstDayOfMonth = now()->firstOfMonth();
        $fourteenthDayOfMonth = now()->firstOfMonth()->addDays(15);

        $no = 1;
        $data = [
            $no++,
            $canteen->user->employee_id,
            $canteen->user->name,
            $canteen->department->name,
        ];

        if (now() >= $firstDayOfMonth && now() <= $fourteenthDayOfMonth) {
            for ($i = 1; $i <= 15; $i++) {
                $data[] = $canteen->{$i} == 0 ? '0' : $canteen->{$i};
            }

            $data[] = $canteen->total1 ?? 0;
            $data[] = $canteen->totalpayout1 ?? 0;
        } else {
            $endOfMonthDay = now()->endOfMonth()->format('d');

            for ($i = 16; $i <= $endOfMonthDay; $i++) {
                $data[] = $canteen->{$i} == 0 ? '0' : $canteen->{$i};
            }

            $data[] = $canteen->total2 ?? 0;
            $data[] = $canteen->totalpayout2 ?? 0;
        }
        $data[] = $canteen->coa->name;
        $data[] = $canteen->costcenter->name;
        $data[] = $canteen->user->status_employee;
        return [$data];
    }
}
