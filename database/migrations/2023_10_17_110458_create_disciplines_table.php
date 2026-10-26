<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disciplines', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->integer('alfa');
            $table->integer('sakit');
            $table->integer('izin');
            $table->integer('terlambat1');
            $table->integer('terlambat2');
            $table->integer('terlambat3');
            $table->integer('nilai_kedisiplinan');
            $table->integer('nilai');
            $table->string('periode');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplines');
    }
};
