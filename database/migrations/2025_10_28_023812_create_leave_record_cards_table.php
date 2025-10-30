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
        Schema::create('leave_record_cards', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id');
            $table->string('date_transferred')->nullable();

            $table->string('period')->nullable();
            $table->string('particulars')->nullable();
            $table->string('particulars_type')->nullable();

            $table->decimal('earned_vacation', 8, 3)->nullable();
            $table->decimal('balance_vacation', 8, 3)->nullable();
            $table->decimal('absence_w_vacation', 8, 3)->nullable();
            $table->decimal('absence_wo_vacation', 8, 3)->nullable();

            $table->decimal('earned_sick', 8, 3)->nullable();
            $table->decimal('balance_sick', 8, 3)->nullable();
            $table->decimal('absence_w_sick', 8, 3)->nullable();
            $table->decimal('absence_wo_sick', 8, 3)->nullable();

            $table->string('status')->nullable();
            $table->string('remarks')->nullable();

            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('regulars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_record_cards');
    }
};
