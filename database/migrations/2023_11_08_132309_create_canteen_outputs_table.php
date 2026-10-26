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
        Schema::create('canteen_outputs', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->unsignedBigInteger('department_id');
            $table->boolean('1')->default(0);
            $table->boolean('2')->default(0);
            $table->boolean('3')->default(0);
            $table->boolean('4')->default(0);
            $table->boolean('5')->default(0);
            $table->boolean('6')->default(0);
            $table->boolean('7')->default(0);
            $table->boolean('8')->default(0);
            $table->boolean('9')->default(0);
            $table->boolean('10')->default(0);
            $table->boolean('11')->default(0);
            $table->boolean('12')->default(0);
            $table->boolean('13')->default(0);
            $table->boolean('14')->default(0);
            $table->boolean('15')->default(0);
            $table->boolean('16')->default(0);
            $table->boolean('17')->default(0);
            $table->boolean('18')->default(0);
            $table->boolean('19')->default(0);
            $table->boolean('20')->default(0);
            $table->boolean('21')->default(0);
            $table->boolean('22')->default(0);
            $table->boolean('23')->default(0);
            $table->boolean('24')->default(0);
            $table->boolean('25')->default(0);
            $table->boolean('26')->default(0);
            $table->boolean('27')->default(0);
            $table->boolean('28')->default(0);
            $table->boolean('29')->default(0);
            $table->boolean('30')->default(0);
            $table->boolean('31')->default(0);
            $table->unsignedInteger('total1')->nullable();
            $table->unsignedInteger('total2')->nullable();
            $table->bigInteger('totalpayout1')->nullable();
            $table->bigInteger('totalpayout2')->nullable();
            $table->unsignedInteger('coa_id')->nullable();
            $table->unsignedInteger('costcenter_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canteen_outputs');
    }
};
