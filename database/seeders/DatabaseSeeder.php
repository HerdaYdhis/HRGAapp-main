<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\EmployeeStatus;
use App\Models\MealShift;
use App\Models\Role;
use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Seed Roles
        $role = Role::firstOrCreate(
            ['name' => 'Superadmin'],
            ['menu_access' => Role::ROLE_ACCESS]
        );

        // 2. Seed Department
        $dept = Department::firstOrCreate(
            ['name' => 'HRGA']
        );

        // 3. Seed Branch
        $branch = Branch::firstOrCreate(
            ['name' => 'Kantor Pusat']
        );

        // 4. Seed Employee Status
        $status = EmployeeStatus::firstOrCreate(
            ['name' => 'Tetap']
        );

        // 5. Seed MealShift (dayoff is required by User creation/status change)
        $dayoff = MealShift::firstOrCreate(
            ['shift_name' => 'dayoff'],
            [
                'monday_meal_time' => null,
                'tuesday_meal_time' => null,
                'wednesday_meal_time' => null,
                'thursday_meal_time' => null,
                'friday_meal_time' => null,
                'saturday_meal_time' => null,
                'sunday_meal_time' => null,
            ]
        );

        // 6. Seed UserLevels
        $levels = [
            ['level' => 'A.1', 'tunjangan' => 300000, 'persentase_kinerja' => 70, 'persentase_kedisiplinan' => 30],
            ['level' => 'A.2', 'tunjangan' => 325000, 'persentase_kinerja' => 70, 'persentase_kedisiplinan' => 30],
            ['level' => 'B.1', 'tunjangan' => 375000, 'persentase_kinerja' => 70, 'persentase_kedisiplinan' => 30],
            ['level' => 'B.2', 'tunjangan' => 400000, 'persentase_kinerja' => 70, 'persentase_kedisiplinan' => 30],
            ['level' => 'C.1', 'tunjangan' => 300000, 'persentase_kinerja' => 70, 'persentase_kedisiplinan' => 30],
            ['level' => 'C.2', 'tunjangan' => 325000, 'persentase_kinerja' => 70, 'persentase_kedisiplinan' => 30],
            ['level' => 'D.1', 'tunjangan' => 400000, 'persentase_kinerja' => 70, 'persentase_kedisiplinan' => 30],
            ['level' => 'D.2', 'tunjangan' => 450000, 'persentase_kinerja' => 70, 'persentase_kedisiplinan' => 30],
            ['level' => 'E.2', 'tunjangan' => 450000, 'persentase_kinerja' => 70, 'persentase_kedisiplinan' => 30],
        ];

        foreach ($levels as $level) {
            UserLevel::firstOrCreate(['level' => $level['level']], $level);
        }

        $userLevel = UserLevel::first();

        // 7. Seed Admin User
        User::firstOrCreate(
            ['email' => 'hr@saf-indo.com'],
            [
                'name' => 'Admin',
                'employee_id' => 'ADM001',
                'is_superadmin' => true,
                'is_active' => 1,
                'password' => Hash::make('admin12345'),
                'department_id' => $dept->id,
                'user_level_id' => $userLevel->id,
                'role_id' => $role->id,
                'branch_id' => $branch->id,
                'status_employee_id' => $status->id,
            ]
        );

        // Optional secondary admin user
        User::firstOrCreate(
            ['email' => 'admin@saf-indo.com'],
            [
                'name' => 'admin',
                'employee_id' => 'ADM002',
                'is_superadmin' => true,
                'is_active' => 1,
                'password' => Hash::make('admin12345'),
                'department_id' => $dept->id,
                'user_level_id' => $userLevel->id,
                'role_id' => $role->id,
                'branch_id' => $branch->id,
                'status_employee_id' => $status->id,
            ]
        );
    }
}
