<?php

namespace App\Imports;

use App\Models\User;
use App\Models\WarningLetter;
use App\Services\EvaluationCalculatorService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class ImportWarningLetter implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $importedData = [];
    protected $importedCount = 0;

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception('File Excel kosong atau tidak memiliki data.');
        }

        // Validasi judul kolom (case-insensitive & trim)
        $firstItem = $rows->first();
        $firstRow = is_array($firstItem) ? $firstItem : (is_object($firstItem) && method_exists($firstItem, 'toArray') ? $firstItem->toArray() : (array) $firstItem);
        $actualHeadings = array_map(function ($key) {
            return strtolower(trim((string) $key));
        }, array_keys($firstRow));

        $requiredHeadings = ['employee_id', 'sp1', 'sp2', 'sp3', 'periode_yyyy_mm'];
        foreach ($requiredHeadings as $required) {
            if (!in_array($required, $actualHeadings)) {
                throw new \Exception("Struktur tabel tidak sesuai. Kolom '{$required}' tidak ditemukan pada file Excel.");
            }
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                // Normalisasi key
                $normalizedRow = [];
                foreach ($row as $k => $v) {
                    $normalizedRow[strtolower(trim((string) $k))] = $v;
                }

                $rawEmployeeId = trim((string) ($normalizedRow['employee_id'] ?? ''));
                if ($rawEmployeeId === '') {
                    continue;
                }

                // Ekstrak ID jika formatnya berupa "Nama (employee_id)", misal "Herda (ACAD123)" -> "ACAD123"
                if (preg_match('/\(([^)]+)\)$/', $rawEmployeeId, $matches)) {
                    $employeeId = trim($matches[1]);
                } else {
                    $employeeId = $rawEmployeeId;
                }

                $user = User::where('employee_id', $employeeId)->first();
                if (!$user) {
                    $user = User::where('employee_id', $rawEmployeeId)->first();
                }
                if (!$user) {
                    throw new \Exception("Baris {$rowNum}: Karyawan dengan Employee ID '{$employeeId}' tidak ditemukan di database.");
                }

                // Parse & format periode (YYYY-MM)
                $rawPeriode = $normalizedRow['periode_yyyy_mm'] ?? null;
                if (!$rawPeriode) {
                    throw new \Exception("Baris {$rowNum}: Periode (periode_yyyy_mm) wajib diisi untuk karyawan {$employeeId}.");
                }

                try {
                    if (is_numeric($rawPeriode)) {
                        $periode = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawPeriode)->format('Y-m');
                    } else {
                        $periode = Carbon::parse($rawPeriode)->format('Y-m');
                    }
                } catch (\Exception $e) {
                    throw new \Exception("Baris {$rowNum}: Format periode '{$rawPeriode}' tidak valid. Gunakan format YYYY-MM.");
                }

                $inputSp1 = (int) ($normalizedRow['sp1'] ?? 0);
                $inputSp2 = (int) ($normalizedRow['sp2'] ?? 0);
                $inputSp3 = (int) ($normalizedRow['sp3'] ?? 0);

                $countNilai = EvaluationCalculatorService::calculateSPScore($inputSp1, $inputSp2, $inputSp3);

                WarningLetter::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'periode' => $periode,
                    ],
                    [
                        'sp1' => $inputSp1,
                        'sp2' => $inputSp2,
                        'sp3' => $inputSp3,
                        'nilai' => $countNilai,
                    ]
                );

                // Sinkronkan ke rekap TUKIN (EvaluationResult)
                EvaluationCalculatorService::recalculate($user->id, $periode);

                $this->importedData[] = [
                    'employee_id' => $employeeId,
                    'user_id' => $user->id,
                    'periode' => $periode,
                ];
                $this->importedCount++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Gagal mengimpor Surat Peringatan: ' . $e->getMessage());
        }
    }

    public function getImportedData(): array
    {
        return $this->importedData;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
