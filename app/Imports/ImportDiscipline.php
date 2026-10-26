<?php

namespace App\Imports;

use App\Models\Discipline;
use App\Models\User;
use App\Services\EvaluationCalculatorService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class ImportDiscipline implements ToCollection, WithHeadingRow
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

        $requiredHeadings = ['employee_id', 'alfa', 'sakit', 'izin', 'periode_yyyy_mm'];
        foreach ($requiredHeadings as $required) {
            if (!in_array($required, $actualHeadings)) {
                throw new \Exception("Struktur tabel tidak sesuai. Kolom '{$required}' tidak ditemukan pada file Excel.");
            }
        }

        // Jalankan import dalam database transaction
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
                    // Abaikan baris kosong
                    continue;
                }

                // Ekstrak ID jika formatnya berupa "Nama (employee_id)", misal "Herda (ACAD123)" -> "ACAD123"
                if (preg_match('/\(([^)]+)\)$/', $rawEmployeeId, $matches)) {
                    $employeeId = trim($matches[1]);
                } else {
                    $employeeId = $rawEmployeeId;
                }

                $user = User::where('employee_id', $employeeId)->with('userLevel')->first();
                if (!$user) {
                    // Coba fallback dengan raw value jika match persis
                    $user = User::where('employee_id', $rawEmployeeId)->with('userLevel')->first();
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

                $alfa = $normalizedRow['alfa'] ?? 0;
                $izin = $normalizedRow['izin'] ?? 0;
                $sakit = $normalizedRow['sakit'] ?? 0;
                $t1 = $normalizedRow['terlambat_5_20'] ?? 0;
                $t2 = $normalizedRow['terlambat_21_60'] ?? 0;
                $t3 = $normalizedRow['terlambat_60'] ?? 0;

                $scores = EvaluationCalculatorService::calculateDisciplineScore($alfa, $izin, $sakit, $t1, $t2, $t3);

                $persentaseKedisiplinan = (float) ($user->userLevel->persentase_kedisiplinan ?? 0);
                $nilaiAkhir = round(($persentaseKedisiplinan / 100) * $scores['nilai_kedisiplinan'], 2);

                // Update or Create data kedisiplinan (mencegah duplikasi data per user & periode)
                Discipline::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'periode' => $periode,
                    ],
                    [
                        'alfa' => $scores['alfa'],
                        'sakit' => $scores['sakit'],
                        'izin' => $scores['izin'],
                        'terlambat1' => $scores['terlambat1'],
                        'terlambat2' => $scores['terlambat2'],
                        'terlambat3' => $scores['terlambat3'],
                        'nilai_kedisiplinan' => $scores['nilai_kedisiplinan'],
                        'nilai' => $nilaiAkhir,
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
            throw new \Exception('Gagal mengimpor data kedisiplinan: ' . $e->getMessage());
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
