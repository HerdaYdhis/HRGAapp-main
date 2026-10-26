<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealShift extends Model
{
    use HasFactory;


    public function employeeSchedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'meal_shift_id');
    }

    public function canteen()
    {
        return $this->hasMany(EmployeeSchedule::class, 'meal_shift_id');
    }
}
