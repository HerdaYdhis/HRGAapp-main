<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerprintLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_sn',
        'employee_id',
        'pin',
        'scan_datetime',
        'status',
        'verify',
        'work_code',
        'processed',
    ];

    protected $casts = [
        'scan_datetime' => 'datetime',
        'processed'     => 'boolean',
    ];
}
