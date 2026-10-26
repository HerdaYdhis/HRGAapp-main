<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_logs', function (Blueprint $table) {
            $table->id();
            $table->string('device_sn')->nullable()->index(); // Serial Number mesin
            $table->string('employee_id')->nullable()->index();
            $table->string('pin')->nullable();               // PIN dari mesin
            $table->datetime('scan_datetime')->nullable()->index();
            $table->tinyInteger('status')->nullable();       // 0=check-in, 1=check-out
            $table->tinyInteger('verify')->nullable();       // 1=fingerprint, 4=card
            $table->tinyInteger('work_code')->nullable();
            $table->boolean('processed')->default(false)->index(); // sudah diproses ke canteen?
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_logs');
    }
};
