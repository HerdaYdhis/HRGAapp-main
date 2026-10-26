<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeSchedule extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'employee_id', 'work_date', 'meal_shift_id'];

    public function meal_shifts()
    {
        return $this->belongsTo(MealShift::class, 'meal_shift_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * employeeschedule
     *
     * @return void
     */

    public function canteen(): HasMany
    {
        return $this->hasMany(EmployeeSchedule::class, 'employee_id', 'employee_id');
    }
}
