<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->dropColumn(['hour_day_base', 'leave_with_pay', 'leave_without_pay']);
            $table->json('hourly_base')->nullable();
            $table->json('monthly_base')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leave_credits', function (Blueprint $table) {
            $table->integer('hour_day_base')->default(0);
            $table->integer('leave_with_pay')->default(0);
            $table->integer('leave_without_pay')->default(0);
            $table->dropColumn(['hourly_base', 'monthly_base']);
        });
    }
};
