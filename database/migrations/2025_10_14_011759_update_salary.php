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
        Schema::dropIfExists('regular_salaries');

        Schema::create('regular_salaries', function (Blueprint $table) {
            $table->id();
            $table->integer('salary_grade')->unique();

            $table->integer('step_1');
            $table->integer('step_2');
            $table->integer('step_3');
            $table->integer('step_4');
            $table->integer('step_5');
            $table->integer('step_6');
            $table->integer('step_7');
            $table->integer('step_8');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regular_salaries');
    }
};
