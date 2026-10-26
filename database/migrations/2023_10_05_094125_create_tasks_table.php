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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->unsignedInteger('user_id');
            $table->enum('type', ['daily', 'weekly', 'monthly', 'project']);
            $table->date('duedate')->nullable();
            $table->boolean('progress')->default(0);
            $table->string('image')->nullable();
            $table->enum('status', array('open', 'done', 'done with overdue', 'cancelled'))->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
