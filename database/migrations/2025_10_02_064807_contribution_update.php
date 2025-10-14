<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove invalid employee_id rows (optional: comment out if you want to handle them manually)
        DB::table('regular_contributions')
            ->whereNotNull('employee_id')
            ->whereNotIn('employee_id', function ($query) {
                $query->select('id')->from('regulars');
            })
            ->delete();

        Schema::table('regular_contributions', function (Blueprint $table) {
            if (!Schema::hasColumn('regular_contributions', 'employee_id')) {
                // Add the column and the foreign key constraint
                $table->foreignId('employee_id')
                    ->after('id')
                    ->constrained('regulars')
                    ->onDelete('cascade');
            } else {
                // First check if a foreign key already exists to avoid duplication
                // This assumes you are not re-running the migration blindly.
                // Add the foreign key constraint
                $table->foreign('employee_id')
                    ->references('id')
                    ->on('regulars')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regular_contributions', function (Blueprint $table) {
            // Drop foreign key first (if exists)
            $table->dropForeign(['employee_id']);

            if (Schema::hasColumn('regular_contributions', 'employee_id')) {
                $table->dropColumn('employee_id');
            }
        });
    }
};
