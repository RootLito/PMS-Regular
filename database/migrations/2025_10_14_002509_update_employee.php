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
        Schema::table('regulars', function (Blueprint $table) {
            $table->string('item_no')->after('sl_code');
            $table->string('appointed_date')->after('item_no');
            $table->integer('salary_grade')->after('appointed_date');
            $table->integer('step')->after('salary_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regulars', function (Blueprint $table) {
            $table->dropColumn(['item_no', 'appointed_date', 'salary_grade', 'step']);
        });
    }
};
