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
        Schema::create('close_periodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->date('month');
            $table->unsignedInteger('overdue');
            $table->unsignedInteger('done');
            $table->unsignedInteger('percentage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('close_periodes');
    }
};
