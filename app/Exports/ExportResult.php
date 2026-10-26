<?php

namespace App\Exports;

use App\Models\EvaluationResult;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


use App\Models\Department;

class ExportResult implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $departmentId;

    public function __construct($departmentId = null)
    {
        $this->departmentId = $departmentId ?: session('filterDepartment');
    }

    public function headings(): array
    {
        $periode =  session('sendPeriode') ? \Carbon\Carbon::parse(session('sendPeriode'))->format('F Y') : ' ';
        $periode1 =  session('sendPeriode') ? \Carbon\Carbon::parse(session('sendPeriode'))->addMonth() : ' ';
        $periode1 = $periode1->format('F Y');

        $deptTitle = 'SEMUA DEPARTEMEN';
        if ($this->departmentId) {
            $dept = Department::find($this->departmentId);
            if ($dept) {
                $deptTitle = 'DEPARTEMEN ' . strtoupper($dept->name);
            }
        }

        return [
            ['PENILAIAN TUNJANGAN KINERJA - ' . $deptTitle],
            ['PERIODE ' . $periode . ' DI BAYARKAN DI PENGGAJIAN ' . $periode1],
            [''],
            [
                'No',
                'ID Karyawan',
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
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Iterate through rows to add borders
        for ($row = 4; $row <= $highestRow; $row++) {
            $range = 'A' . $row . ':' . $highestColumn . $row;
            $sheet->getStyle($range)->getBorders()->getTop()->setBorderStyle('thin');
            $sheet->getStyle($range)->getBorders()->getBottom()->setBorderStyle('thin');
        }

        // Iterate through columns to add borders
        $columns = range('A', $highestColumn);
        foreach ($columns as $column) {
            $range = $column . '4:' . $column . $highestRow;
            $sheet->getStyle($range)->getBorders()->getLeft()->setBorderStyle('thin');
            $sheet->getStyle($range)->getBorders()->getRight()->setBorderStyle('thin');
        }

        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('R4:U4')->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('W4')->getFont()->getColor()->setARGB('FFFFFFFF');

        $sheet->getStyle('F4:G4')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF00FF00', // Set the background color here
                ],
            ],
        ]);
        $sheet->getStyle('H4:O4')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF00FF', // Set the background color here
                ],
            ],
        ]);
        $sheet->getStyle('R4:U4')->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFF0000', // Set the background color here
                ],
            ],
        ]);
        $sheet->getStyle('W4')->applyFromArray([
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
        // return EvaluationResult::with(['user.department', 'user.userLevel'])
        //     ->select('evaluation_results.*', 'users.name as user_name', 'departments.name as department_name', 'user_levels.name as user_level_name')
        //     ->get();
        // $periode = '%' . session('sendPeriode') . '%';
        $query = EvaluationResult::select(
            DB::raw('ROW_NUMBER() OVER (ORDER BY departments.name) as number'),
            'users.employee_id as user_employee_id',
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
            ->leftjoin('users', 'evaluation_results.user_id', '=', 'users.id')
            ->leftjoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftjoin('user_levels', 'users.user_level_id', '=', 'user_levels.id')
            ->orderBy('departments.name');

        if (session('sendPeriode')) {
            $query->where('evaluation_results.periode', session('sendPeriode'));
        }

        if ($this->departmentId) {
            $query->where('users.department_id', $this->departmentId);
        }

        $results = $query->get();

        return $results;
    }
}
