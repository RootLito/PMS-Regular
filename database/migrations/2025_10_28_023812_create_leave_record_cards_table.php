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
            $table->string('period');
            $table->string('particulars');
            $table->string('particulars_type');

            $table->decimal('earned_vacation', 8, 3);
            $table->decimal('balance_vacation', 8, 3);
            $table->decimal('absence_w_vacation', 8, 3);
            $table->decimal('absence_wo_vacation', 8, 3);

            $table->decimal('earned_sick', 8, 3);
            $table->decimal('balance_sick', 8, 3);
            $table->decimal('absence_w_sick', 8, 3);
            $table->decimal('absence_wo_sick', 8, 3);

            $table->string('status');
            $table->string('remarks');

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
