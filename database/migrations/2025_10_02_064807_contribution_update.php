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
        Schema::table('regular_contributions', function (Blueprint $table) {
            if (!Schema::hasColumn('regular_contributions', 'employee_id')) {
                $table->foreignId('employee_id')->after('id')->constrained('regulars')->onDelete('cascade');
            } else {
                $table->foreign('employee_id')->references('id')->on('regulars')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regular_contributions', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            if (Schema::hasColumn('regular_contributions', 'employee_id')) {
                $table->dropColumn('employee_id');
            }
        });
    }
};

