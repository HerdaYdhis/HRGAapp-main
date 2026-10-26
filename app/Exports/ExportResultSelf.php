<?php

namespace App\Exports;

use App\Models\EvaluationResult;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportResultSelf implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{

    public function headings(): array
    {
        $user = User::where('id', auth()->user()->id)->first();

        return [
            ['PENILAIAN TUNJANGAN KINERJA ' . $user->name],
            [''],
            [
                'No',
                'ID Karyawan',
                'Periode',
                'Nama',
                'Departemen',
                'Level Job',
                'Nilai Kinerja',
                'Nilai Akhir Kinerja',
                'Alpa',
                'Izin',
                'Sakit',
                'Terlambat (≥5≤20 menit)',
                'Terlambat (>20 ≤60 menit)',
                'Terlambat (>60 menit)',
                'Nilai Kedisiplinan',
                'Nilai Akhir Kedisiplinan',
                'Total Nilai Kinerja + Kedisiplinan',
                '% Nilai Kinerja + Kedisiplinan',
                'SP 1',
                'SP 2',
                'SP 3',
                'Total SP',
                'Total Nilai Akhir',
                'Nominal Tunjangan Yang Didapatkan',
            ],

        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells from A1 to A3
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('S3:V3')->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('X3')->getFont()->getColor()->setARGB('FFFFFFFF');

        $sheet->getStyle('G3:H3')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF00FF00', // Set the background color here
                ],
            ],
        ]);
        $sheet->getStyle('I3:P3')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF00FF', // Set the background color here
                ],
            ],
        ]);
        $sheet->getStyle('S3:V3')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF0000', // Set the background color here
                ],
            ],
        ]);
        $sheet->getStyle('X3')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF191970', // Set the background color here
                ],
            ],
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $results = EvaluationResult::select(
            DB::raw('ROW_NUMBER() OVER (ORDER BY departments.name) as number'),
            'users.employee_id as user_employee_id',
            'periode',
            'users.name as user_name',
            'departments.name',
            'user_levels.level',
            'nilai_kinerja',
            'nilai_akhir_kinerja',
            'alfa',
            'izin',
            'sakit',
            'terlambat1',
            'terlambat2',
            'terlambat3',
            'nilai_kedisiplinan',
            'nilai_akhir_kedisiplinan',
            'total',
            'persentase',
            'sp1',
            'sp2',
            'sp3',
            'nilai_sp',
            'total_akhir',
            'nominal'
        )
            ->where('evaluation_results.user_id', auth()->user()->id)
            ->leftjoin('users', 'evaluation_results.user_id', '=', 'users.id')
            ->leftjoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftjoin('user_levels', 'users.user_level_id', '=', 'user_levels.id')
            ->orderBy('departments.name')
            ->get();

        return $results;
    }
}
