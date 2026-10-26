<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CanteenOutput extends Model
{
    use HasFactory;

    /**
     * user
     *
     * @return void
     */
    public function user(): BelongsTo
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
     * coa
     *
     * @return void
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'coa_id', 'id');
    }

    /**
     * costcenter
     *
     * @return void
     */
    public function costcenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'costcenter_id', 'id');
    }




}
