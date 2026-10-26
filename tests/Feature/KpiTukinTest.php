<?php

namespace Tests\Feature;

use App\Models\Discipline;
use App\Models\Evaluation;
use App\Models\EvaluationResult;
use App\Models\Performance;
use App\Models\User;
use App\Models\UserLevel;
use App\Models\WarningLetter;
use App\Services\EvaluationCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiTukinTest extends TestCase
{
    use RefreshDatabase;

    public function test_discipline_score_calculation()
    {
        // 1. Tanpa pelanggaran (100% disiplin)
        $score = EvaluationCalculatorService::calculateDisciplineScore(0, 0, 0, 0, 0, 0);
        $this->assertEquals(100, $score['nilai_kedisiplinan']);
        $this->assertEquals(0, $score['total_potongan']);

        // 2. alfa=1 (30), izin=1 (10), sakit=1 (10), terlambat 5-20=1 (10)
        // Total potongan: 30 + 10 + 10 + 10 = 60 => nilai: 40
        $score2 = EvaluationCalculatorService::calculateDisciplineScore(1, 1, 1, 1, 0, 0);
        $this->assertEquals(60, $score2['total_potongan']);
        $this->assertEquals(40, $score2['nilai_kedisiplinan']);

        // 3. alfa >= 3 (100) => nilai: 0
        $score3 = EvaluationCalculatorService::calculateDisciplineScore(3, 0, 0, 0, 0, 0);
        $this->assertEquals(100, $score3['total_potongan']);
        $this->assertEquals(0, $score3['nilai_kedisiplinan']);

        // 4. terlambat bertingkat: t1=1 (10), t2=1 (20), t3=1 (30) => total potongan: 60 => nilai: 40
        $score4 = EvaluationCalculatorService::calculateDisciplineScore(0, 0, 0, 1, 1, 1);
        $this->assertEquals(60, $score4['total_potongan']);
        $this->assertEquals(40, $score4['nilai_kedisiplinan']);
    }

    public function test_percentage_tier_calculation()
    {
        $this->assertEquals(100, EvaluationCalculatorService::calculatePercentageTier(100));
        $this->assertEquals(100, EvaluationCalculatorService::calculatePercentageTier(91));
        $this->assertEquals(90, EvaluationCalculatorService::calculatePercentageTier(90.9));
        $this->assertEquals(90, EvaluationCalculatorService::calculatePercentageTier(81));
        $this->assertEquals(80, EvaluationCalculatorService::calculatePercentageTier(80));
        $this->assertEquals(80, EvaluationCalculatorService::calculatePercentageTier(71));
        $this->assertEquals(70, EvaluationCalculatorService::calculatePercentageTier(70));
        $this->assertEquals(70, EvaluationCalculatorService::calculatePercentageTier(51));
        $this->assertEquals(50, EvaluationCalculatorService::calculatePercentageTier(50.9));
        $this->assertEquals(50, EvaluationCalculatorService::calculatePercentageTier(30));
        $this->assertEquals(50, EvaluationCalculatorService::calculatePercentageTier(0));
    }

    public function test_sp_score_calculation()
    {
        $this->assertEquals(20, EvaluationCalculatorService::calculateSPScore(1, 0, 0));
        $this->assertEquals(30, EvaluationCalculatorService::calculateSPScore(0, 1, 0));
        $this->assertEquals(50, EvaluationCalculatorService::calculateSPScore(0, 0, 1));
        $this->assertEquals(100, EvaluationCalculatorService::calculateSPScore(1, 1, 1));
        // Capped at 100
        $this->assertEquals(100, EvaluationCalculatorService::calculateSPScore(0, 0, 3));
    }

    public function test_kpi_and_tukin_full_calculation_cycle()
    {
        $userLevel = UserLevel::create([
            'level' => 'Staff Test',
            'tunjangan' => 1000000,
            'persentase_kinerja' => 70,
            'persentase_kedisiplinan' => 30,
        ]);

        $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP-001',
            'user_level_id' => $userLevel->id,
            'role_id' => 1,
        ]);

        // Indikator 1 (bobot 60) dan Indikator 2 (bobot 40)
        $perf1 = Performance::create([
            'user_id' => $user->id,
            'indikator' => 'Pencapaian Target Project',
            'bobot' => 60,
        ]);

        $perf2 = Performance::create([
            'user_id' => $user->id,
            'indikator' => 'Kualitas Kode & Dokumentasi',
            'bobot' => 40,
        ]);

        $periode = '2024-05';

        // Penilaian: Indikator 1 dapat 80 => nilai_akhir = 48
        Evaluation::create([
            'performance_id' => $perf1->id,
            'user_id' => $user->id,
            'nilai' => 80,
            'nilai_akhir' => 48,
            'periode' => $periode,
        ]);

        // Penilaian: Indikator 2 dapat 90 => nilai_akhir = 36
        Evaluation::create([
            'performance_id' => $perf2->id,
            'user_id' => $user->id,
            'nilai' => 90,
            'nilai_akhir' => 36,
            'periode' => $periode,
        ]);

        // Kedisiplinan: izin 1x (potongan 10) => nilai_kedisiplinan 90, nilai_akhir_kedisiplinan = 90 * 0.3 = 27
        Discipline::create([
            'user_id' => $user->id,
            'alfa' => 0,
            'izin' => 1,
            'sakit' => 0,
            'terlambat1' => 0,
            'terlambat2' => 0,
            'terlambat3' => 0,
            'nilai_kedisiplinan' => 90,
            'nilai' => 27,
            'periode' => $periode,
        ]);

        // Surat Peringatan: SP1 (potongan 20)
        WarningLetter::create([
            'user_id' => $user->id,
            'sp1' => 1,
            'sp2' => 0,
            'sp3' => 0,
            'nilai' => 20,
            'periode' => $periode,
        ]);

        // Jalankan rekalkulasi
        $result = EvaluationCalculatorService::recalculate($user->id, $periode);

        $this->assertNotNull($result);
        $this->assertEquals(84, $result->nilai_kinerja); // 48 + 36 = 84
        $this->assertEquals(58.8, $result->nilai_akhir_kinerja); // 84 * 0.7 = 58.8
        $this->assertEquals(90, $result->nilai_kedisiplinan);
        $this->assertEquals(27, $result->nilai_akhir_kedisiplinan); // 90 * 0.3 = 27
        $this->assertEquals(85.8, $result->total); // 58.8 + 27 = 85.8
        $this->assertEquals(90, $result->persentase); // 85.8 tier => 90%
        $this->assertEquals(20, $result->nilai_sp);
        $this->assertEquals(70, $result->total_akhir); // 90 - 20 = 70%
        $this->assertEquals(700000, $result->nominal); // 70% dari 1,000,000 = 700,000

        // Pastikan hanya 1 record di evaluation_results (tidak ada duplicate)
        $count = EvaluationResult::where('user_id', $user->id)->where('periode', $periode)->count();
        $this->assertEquals(1, $count);
    }

    public function test_period_isolation()
    {
        $userLevel = UserLevel::create([
            'level' => 'Staff Test',
            'tunjangan' => 2000000,
            'persentase_kinerja' => 70,
            'persentase_kedisiplinan' => 30,
        ]);

        $user = User::create([
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP-002',
            'user_level_id' => $userLevel->id,
            'role_id' => 1,
        ]);

        $perf = Performance::create([
            'user_id' => $user->id,
            'indikator' => 'KPI Utama',
            'bobot' => 100,
        ]);

        // Periode 1: 2024-01 nilai 100%
        Evaluation::create([
            'performance_id' => $perf->id,
            'user_id' => $user->id,
            'nilai' => 100,
            'nilai_akhir' => 100,
            'periode' => '2024-01',
        ]);
        EvaluationCalculatorService::recalculate($user->id, '2024-01');

        // Periode 2: 2024-11 nilai 50%
        Evaluation::create([
            'performance_id' => $perf->id,
            'user_id' => $user->id,
            'nilai' => 50,
            'nilai_akhir' => 50,
            'periode' => '2024-11',
        ]);
        EvaluationCalculatorService::recalculate($user->id, '2024-11');

        $resultJan = EvaluationResult::where('user_id', $user->id)->where('periode', '2024-01')->first();
        $resultNov = EvaluationResult::where('user_id', $user->id)->where('periode', '2024-11')->first();

        $this->assertEquals(100, $resultJan->nilai_kinerja);
        $this->assertEquals(50, $resultNov->nilai_kinerja);
        $this->assertEquals(100, $resultJan->persentase);
        $this->assertEquals(70, $resultNov->persentase); // 50 * 0.7 = 35 + 30 = 65 => 70% tier
    }

    public function test_import_indikator_rolls_back_on_excessive_bobot()
    {
        $user = User::create([
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP-003',
            'role_id' => 1,
        ]);

        Performance::create([
            'user_id' => $user->id,
            'indikator' => 'Existing Task',
            'bobot' => 50,
        ]);

        $import = new \App\Imports\ImportIndikator($user->id);

        // Simulasi rows Excel dengan bobot total 60 (50 + 60 = 110 > 100)
        $rows = collect([
            ['indikator' => 'Task A', 'bobot' => 30],
            ['indikator' => 'Task B', 'bobot' => 30],
        ]);

        try {
            $import->collection($rows);
            $this->fail('Harusnya melempar Exception karena total bobot > 100');
        } catch (\Exception $e) {
            $this->assertStringContainsString('melebihi 100', $e->getMessage());
        }

        // Pastikan TIDAK ada data Task A atau Task B yang tersimpan setengah jalan
        $this->assertEquals(0, Performance::where('user_id', $user->id)->where('indikator', 'Task A')->count());
        $this->assertEquals(0, Performance::where('user_id', $user->id)->where('indikator', 'Task B')->count());
        $this->assertEquals(50, Performance::where('user_id', $user->id)->sum('bobot'));
    }

    public function test_import_discipline_and_warning_letter_update_or_create()
    {
        $userLevel = UserLevel::create([
            'level' => 'Staff IT',
            'tunjangan' => 1000000,
            'persentase_kinerja' => 70,
            'persentase_kedisiplinan' => 30,
        ]);

        $user = User::create([
            'name' => 'Alice Wonder',
            'email' => 'alice@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP-004',
            'user_level_id' => $userLevel->id,
            'role_id' => 1,
        ]);

        $importDiscipline = new \App\Imports\ImportDiscipline();
        $discRows = collect([
            [
                'employee_id' => 'EMP-004',
                'employee_name' => 'Alice Wonder',
                'alfa' => 1,
                'sakit' => 0,
                'izin' => 0,
                'terlambat_5_20' => 0,
                'terlambat_21_60' => 0,
                'terlambat_60' => 0,
                'periode_yyyy_mm' => '2024-05',
            ]
        ]);
        $importDiscipline->collection($discRows);

        $disc = Discipline::where('user_id', $user->id)->where('periode', '2024-05')->first();
        $this->assertNotNull($disc);
        $this->assertEquals(70, $disc->nilai_kedisiplinan); // 100 - 30 (alfa 1) = 70

        // Import ulang dengan revisi (alfa = 0) -> harus update, bukan duplikat
        $discRowsUpdate = collect([
            [
                'employee_id' => 'EMP-004',
                'employee_name' => 'Alice Wonder',
                'alfa' => 0,
                'sakit' => 0,
                'izin' => 0,
                'terlambat_5_20' => 0,
                'terlambat_21_60' => 0,
                'terlambat_60' => 0,
                'periode_yyyy_mm' => '2024-05',
            ]
        ]);
        $importDiscipline->collection($discRowsUpdate);

        $this->assertEquals(1, Discipline::where('user_id', $user->id)->where('periode', '2024-05')->count());
        $discUpdated = Discipline::where('user_id', $user->id)->where('periode', '2024-05')->first();
        $this->assertEquals(100, $discUpdated->nilai_kedisiplinan);

        // Test Import Warning Letter
        $importWl = new \App\Imports\ImportWarningLetter();
        $wlRows = collect([
            [
                'employee_id' => 'EMP-004',
                'employee_name' => 'Alice Wonder',
                'sp1' => 1,
                'sp2' => 0,
                'sp3' => 0,
                'periode_yyyy_mm' => '2024-05',
            ]
        ]);
        $importWl->collection($wlRows);

        $wl = WarningLetter::where('user_id', $user->id)->where('periode', '2024-05')->first();
        $this->assertNotNull($wl);
        $this->assertEquals(20, $wl->nilai);

        // EvaluationResult harus otomatis ter-update
        $evalResult = EvaluationResult::where('user_id', $user->id)->where('periode', '2024-05')->first();
        $this->assertNotNull($evalResult);
        $this->assertEquals(20, $evalResult->nilai_sp);
        $this->assertEquals(100, $evalResult->nilai_kedisiplinan);
    }
}
