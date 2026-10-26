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
        Schema::create('meal_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('shift_name');
            $table->time('monday_meal_time')->nullable();
            $table->time('tuesday_meal_time')->nullable();
            $table->time('wednesday_meal_time')->nullable();
            $table->time('thursday_meal_time')->nullable();
            $table->time('friday_meal_time')->nullable();
            $table->time('saturday_meal_time')->nullable();
            $table->time('sunday_meal_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_shifts');
    }
};
