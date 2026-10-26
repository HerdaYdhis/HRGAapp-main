<?php

namespace App\Imports;

use App\Models\Performance;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class ImportIndikator implements ToCollection, WithHeadingRow
{
    use Importable;

    /** @var int|string|null */
    protected $userId;
    /** @var array */
    protected $importedData = [];
    /** @var int */
    protected $importedCount = 0;

    public function __construct($userId = null)
    {
        $this->userId = $userId ?: session('sendUserId');
    }

    public function collection(Collection $rows)
    {
        if (!$this->userId) {
            throw new \Exception('User ID tidak ditemukan. Harap pilih user terlebih dahulu.');
        }

        if ($rows->isEmpty()) {
            throw new \Exception('File Excel kosong atau tidak memiliki data.');
        }

        // Validasi judul kolom (case-insensitive & trim)
        $firstItem = $rows->first();
        $firstRow = is_array($firstItem) ? $firstItem : (is_object($firstItem) && method_exists($firstItem, 'toArray') ? $firstItem->toArray() : (array) $firstItem);
        $actualHeadings = array_map(function ($key) {
            return strtolower(trim((string) $key));
        }, array_keys($firstRow));

        if (!in_array('indikator', $actualHeadings) || !in_array('bobot', $actualHeadings)) {
            throw new \Exception('Struktur tabel tidak sesuai. Judul kolom harus memiliki "indikator" dan "bobot".');
        }

        // 1. Validasi isi dan hitung total bobot file terlebih dahulu
        $fileTotalBobot = 0;
        $itemsToInsert = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // header baris 1

            // Normalisasi key
            $normalizedRow = [];
            foreach ($row as $k => $v) {
                $normalizedRow[strtolower(trim((string) $k))] = $v;
            }

            $indikator = trim((string) ($normalizedRow['indikator'] ?? ''));
            $bobot = $normalizedRow['bobot'] ?? null;

            if ($indikator === '' && ($bobot === null || $bobot === '')) {
                // Baris kosong diabaikan
                continue;
            }

            if ($indikator === '') {
                throw new \Exception("Baris {$rowNum}: Indikator tidak boleh kosong.");
            }

            if (!is_numeric($bobot) || $bobot < 0) {
                throw new \Exception("Baris {$rowNum}: Bobot harus berupa angka positif (terbaca: '{$bobot}').");
            }

            $bobot = (int) $bobot;
            $fileTotalBobot += $bobot;

            $itemsToInsert[] = [
                'indikator' => $indikator,
                'bobot' => $bobot,
                'user_id' => $this->userId,
            ];
        }

        if (empty($itemsToInsert)) {
            throw new \Exception('Tidak ada data valid yang ditemukan untuk diimpor.');
        }

        $existingSum = Performance::where('user_id', $this->userId)->sum('bobot');
        $totalCheck = $existingSum + $fileTotalBobot;

        if ($totalCheck > 100) {
            throw new \Exception("Total bobot melebihi 100. (Bobot saat ini: {$existingSum}, Tambahan file: {$fileTotalBobot}, Total: {$totalCheck}).");
        }

        // 2. Simpan dalam DB transaction
        DB::beginTransaction();
        try {
            foreach ($itemsToInsert as $item) {
                // Hindari duplikasi jika nama indikator sama persis untuk user ini, perbarui bobotnya
                $existing = Performance::where('user_id', $this->userId)
                    ->where('indikator', $item['indikator'])
                    ->first();

                if ($existing) {
                    $existing->update(['bobot' => $item['bobot']]);
                } else {
                    Performance::create($item);
                }

                $this->importedData[] = $item;
                $this->importedCount++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Gagal menyimpan data ke database: ' . $e->getMessage());
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
