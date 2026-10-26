<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
    /**
     * master_task
     *
     * @return HasMany
     */
    public function master_task(): HasMany
    {
        return $this->hasMany(MasterTask::class);
    }

    /**
     * task
     *
     * @return HasMany
     */
    public function task(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * closeperiode
     *
     * @return HasMany
     */
    public function closeperiode(): HasMany
    {
        return $this->hasMany(ClosePeriode::class);
    }

    /**
     * employee_status
     *
     * @return BelongsTo
     */
    public function employee_status(): BelongsTo
    {
        return $this->belongsTo(EmployeeStatus::class, 'status_employee_id');
    }

    /**
     * canteens
     *
     * @return HasMany
     */
    public function canteens(): HasMany
    {
        return $this->hasMany(Canteen::class, 'employee_id', 'employee_id');
    }

    /**
     * canteen
     *
     * @return HasMany
     */
    public function canteen_output(): HasMany
    {
        return $this->hasMany(CanteenOutput::class, 'employee_id', 'employee_id');
    }


    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class, 'coa_id');
    }

    public function costcenter()
    {
        return $this->belongsTo(CostCenter::class, 'costcenter_id');
    }

    public function userLevel()
    {
        return $this->belongsTo(UserLevel::class, 'user_level_id');
    }

    public function evaluation()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function evaluationResult()
    {
        return $this->hasMany(EvaluationResult::class);
    }


    public function discipline()
    {
        return $this->hasMany(Discipline::class);
    }

    public function warningLetter()
    {
        return $this->hasMany(WarningLetter::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function employeeSchedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'user_id');
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * getAvatarAttribute
     *
     * @param  mixed $avatar
     * @return string
     */
    public function getAvatarAttribute($avatar): string
    {
        if ($avatar != null) :
            return asset('/storage/' . $avatar);
        else :
            return 'https://ui-avatars.com/api/?name=' . str_replace(' ', '+', $this->name) . '&background=4e73df&color=ffffff&size=100';
        endif;
    }
}
