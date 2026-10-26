<?php

namespace App\Services;

use App\Models\Discipline;
use App\Models\Evaluation;
use App\Models\EvaluationResult;
use App\Models\User;
use App\Models\WarningLetter;
use Illuminate\Support\Facades\Log;

class EvaluationCalculatorService
{
    /**
     * Hitung nilai kedisiplinan (0 - 100) berdasarkan alfa, izin, sakit, dan terlambat.
     */
    public static function calculateDisciplineScore(
        int|float|null $alfa = 0,
        int|float|null $izin = 0,
        int|float|null $sakit = 0,
        int|float|null $terlambat1 = 0,
        int|float|null $terlambat2 = 0,
        int|float|null $terlambat3 = 0
    ): array
    {
        $alfa = (int) ($alfa ?? 0);
        $izin = (int) ($izin ?? 0);
        $sakit = (int) ($sakit ?? 0);
        $terlambat1 = (int) ($terlambat1 ?? 0);
        $terlambat2 = (int) ($terlambat2 ?? 0);
        $terlambat3 = (int) ($terlambat3 ?? 0);

        // Aturan potongan alfa
        if ($alfa <= 0) {
            $potonganAlfa = 0;
        } elseif ($alfa === 1) {
            $potonganAlfa = 30;
        } elseif ($alfa === 2) {
            $potonganAlfa = 50;
        } else {
            $potonganAlfa = 100;
        }

        // Aturan potongan izin
        if ($izin <= 0) {
            $potonganIzin = 0;
        } elseif ($izin >= 1 && $izin <= 2) {
            $potonganIzin = 10;
        } else {
            $potonganIzin = 20;
        }

        // Aturan potongan sakit
        if ($sakit <= 0) {
            $potonganSakit = 0;
        } elseif ($sakit >= 1 && $sakit <= 2) {
            $potonganSakit = 10;
        } else {
            $potonganSakit = 20;
        }

        // Aturan potongan keterlambatan: 10 per t1, 20 per t2, 30 per t3
        $potonganTerlambat = ($terlambat1 * 10) + ($terlambat2 * 20) + ($terlambat3 * 30);

        $totalPotongan = $potonganAlfa + $potonganIzin + $potonganSakit + $potonganTerlambat;
        if ($totalPotongan > 100) {
            $totalPotongan = 100;
        }

        $nilaiKedisiplinan = 100 - $totalPotongan;

        return [
            'alfa' => $alfa,
            'izin' => $izin,
            'sakit' => $sakit,
            'terlambat1' => $terlambat1,
            'terlambat2' => $terlambat2,
            'terlambat3' => $terlambat3,
            'total_potongan' => $totalPotongan,
            'nilai_kedisiplinan' => $nilaiKedisiplinan,
        ];
    }

    /**
     * Hitung tier persentase berdasarkan total nilai (kinerja + kedisiplinan)
     */
    public static function calculatePercentageTier(int|float $total): int
    {
        $total = (float) $total;

        if ($total >= 91 && $total <= 100) {
            return 100;
        } elseif ($total >= 81 && $total < 91) {
            return 90;
        } elseif ($total >= 71 && $total < 81) {
            return 80;
        } elseif ($total >= 51 && $total < 71) {
            return 70;
        } else {
            return 50;
        }
    }

    /**
     * Hitung nilai surat peringatan (SP)
     */
    public static function calculateSPScore(int|null $sp1 = 0, int|null $sp2 = 0, int|null $sp3 = 0): int
    {
        $sp1 = (int) ($sp1 ?? 0);
        $sp2 = (int) ($sp2 ?? 0);
        $sp3 = (int) ($sp3 ?? 0);

        $score = ($sp1 * 20) + ($sp2 * 30) + ($sp3 * 50);
        return min(100, $score);
    }

    /**
     * Rekalkulasi nilai evaluasi dan TUKIN secara menyeluruh dan update/create EvaluationResult.
     */
    public static function recalculate(int|string $userId, string $periode)
    {
        $user = User::with('userLevel')->find($userId);
        if (!$user) {
            Log::warning("EvaluationCalculatorService: User ID {$userId} tidak ditemukan.");
            return null;
        }

        $persentaseKinerja = (float) ($user->userLevel->persentase_kinerja ?? 0);
        $persentaseKedisiplinan = (float) ($user->userLevel->persentase_kedisiplinan ?? 0);
        $tunjanganNominal = (float) ($user->userLevel->tunjangan ?? 0);

        // 1. Ambil seluruh evaluasi kinerja user pada periode yang sama
        $evaluations = Evaluation::where('user_id', $userId)
            ->where('periode', $periode)
            ->get();

        $nilaiKinerja = (float) $evaluations->sum('nilai_akhir');
        $nilaiAkhirKinerja = round($nilaiKinerja * ($persentaseKinerja / 100), 2);

        // 2. Ambil data kedisiplinan user pada periode yang sama
        $discipline = Discipline::where('user_id', $userId)
            ->where('periode', $periode)
            ->first();

        if ($discipline) {
            $inputAlfa = $discipline->alfa;
            $inputIzin = $discipline->izin;
            $inputSakit = $discipline->sakit;
            $inputT1 = $discipline->terlambat1;
            $inputT2 = $discipline->terlambat2;
            $inputT3 = $discipline->terlambat3;
            $nilaiKedisiplinan = (float) $discipline->nilai_kedisiplinan;
            $nilaiAkhirKedisiplinan = round($nilaiKedisiplinan * ($persentaseKedisiplinan / 100), 2);
        } else {
            // Default baseline jika belum ada catatan kedisiplinan (100% disiplin)
            $inputAlfa = 0;
            $inputIzin = 0;
            $inputSakit = 0;
            $inputT1 = 0;
            $inputT2 = 0;
            $inputT3 = 0;
            $nilaiKedisiplinan = 100;
            $nilaiAkhirKedisiplinan = round(100 * ($persentaseKedisiplinan / 100), 2);
        }

        // 3. Total Kinerja + Kedisiplinan
        $total = round($nilaiAkhirKinerja + $nilaiAkhirKedisiplinan, 2);
        $persentase = self::calculatePercentageTier($total);

        // 4. Ambil data Surat Peringatan (SP)
        $warningLetter = WarningLetter::where('user_id', $userId)
            ->where('periode', $periode)
            ->first();

        if ($warningLetter) {
            $sp1 = (int) $warningLetter->sp1;
            $sp2 = (int) $warningLetter->sp2;
            $sp3 = (int) $warningLetter->sp3;
            $nilaiSp = (int) $warningLetter->nilai;
        } else {
            $sp1 = 0;
            $sp2 = 0;
            $sp3 = 0;
            $nilaiSp = 0;
        }

        // 5. Total Akhir & TUKIN
        $totalAkhir = max(0, $persentase - $nilaiSp);
        $nominal = round(($totalAkhir / 100) * $tunjanganNominal);

        return EvaluationResult::updateOrCreate(
            [
                'user_id' => $userId,
                'periode' => $periode,
            ],
            [
                'nilai_kinerja' => $nilaiKinerja,
                'nilai_akhir_kinerja' => $nilaiAkhirKinerja,
                'alfa' => $inputAlfa,
                'izin' => $inputIzin,
                'sakit' => $inputSakit,
                'terlambat1' => $inputT1,
                'terlambat2' => $inputT2,
                'terlambat3' => $inputT3,
                'nilai_kedisiplinan' => $nilaiKedisiplinan,
                'nilai_akhir_kedisiplinan' => $nilaiAkhirKedisiplinan,
                'total' => $total,
                'persentase' => $persentase,
                'sp1' => $sp1,
                'sp2' => $sp2,
                'sp3' => $sp3,
                'nilai_sp' => $nilaiSp,
                'total_akhir' => $totalAkhir,
                'nominal' => $nominal,
            ]
        );
    }
}
