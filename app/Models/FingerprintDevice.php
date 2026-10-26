<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FingerprintDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'device_id',
        'comm_key',
        'timeout',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'port'      => 'integer',
        'device_id' => 'integer',
        'timeout'   => 'integer',
    ];
}
