<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('no_wa')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('address')->nullable();
            $table->string('avatar')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('job_position_id')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->unsignedInteger('user_level_id')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('coa_id')->nullable();
            $table->unsignedInteger('costcenter_id')->nullable();
            $table->date('join_date')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
