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
        Schema::create('evaluation_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('periode');
            $table->decimal('nilai_kinerja')->nullable();
            $table->decimal('nilai_akhir_kinerja')->nullable();
            $table->integer('alfa')->nullable();
            $table->integer('izin')->nullable();
            $table->integer('sakit')->nullable();
            $table->integer('terlambat1')->nullable();
            $table->integer('terlambat2')->nullable();
            $table->integer('terlambat3')->nullable();
            $table->integer('nilai_kedisiplinan')->nullable();
            $table->decimal('nilai_akhir_kedisiplinan')->nullable();
            $table->decimal('total')->nullable();
            $table->integer('persentase')->nullable();
            $table->integer('sp1')->nullable();
            $table->integer('sp2')->nullable();
            $table->integer('sp3')->nullable();
            $table->integer('nilai_sp')->nullable();
            $table->decimal('total_akhir')->nullable();
            $table->integer('nominal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_results');
    }
};
