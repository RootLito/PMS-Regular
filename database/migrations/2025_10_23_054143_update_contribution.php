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
            $table->decimal('first_half', 15, 2)->nullable()->after('employee_id');
            $table->decimal('second_half', 15, 2)->nullable()->after('first_half');
            $table->decimal('total_net_amount', 15, 2)->nullable()->after('second_half');
            $table->decimal('gsis_gs', 15, 2)->nullable()->after('rate_per_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regular_contributions', function (Blueprint $table) {
            $table->dropColumn(['first_half', 'second_half', 'total_net_amount', 'gsis_gs']);
        });
    }
};
