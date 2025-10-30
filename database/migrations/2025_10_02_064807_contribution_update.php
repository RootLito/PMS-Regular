<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('regular_contributions')
            ->whereNotNull('employee_id')
            ->whereNotIn('employee_id', function ($query) {
                $query->select('id')->from('regulars');
            })
            ->delete();

        Schema::table('regular_contributions', function (Blueprint $table) {
            if (!Schema::hasColumn('regular_contributions', 'employee_id')) {
                $table->foreignId('employee_id')
                    ->after('id')
                    ->constrained('regulars')
                    ->onDelete('cascade');
            } else {
                $existingForeignKey = DB::select("SELECT CONSTRAINT_NAME
                    FROM information_schema.key_column_usage
                    WHERE TABLE_NAME = 'regular_contributions'
                    AND COLUMN_NAME = 'employee_id'");

                if (empty($existingForeignKey)) {
                    $table->foreign('employee_id')
                        ->references('id')
                        ->on('regulars')
                        ->onDelete('cascade');
                }
            }
        });
    }

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
