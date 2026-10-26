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
        Schema::create('borrowing_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('objective');
            $table->date('start_date');
            $table->time('start_time');
            $table->date('done_date');
            $table->time('done_time');
            $table->unsignedInteger('meetroom_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowing_rooms');
    }
};
