<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Canteen extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'eat',
        'meal_shift_id',
        'scan_time',
        'department_id',
        'method',
    ];
    /**
     * user
     *
     * @return void
     */
    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'employee_id');
    // }
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }


    /**
     * department
     *
     * @return void
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    /**
     * employeeschedule
     *
     * @return void
     */
    public function employeeschedule(): BelongsTo
    {
        return $this->belongsTo(EmployeeSchedule::class, 'employee_id', 'employee_id');
    }

    public function meal_shifts()
    {
        return $this->belongsTo(MealShift::class, 'meal_shift_id');
    }
}
