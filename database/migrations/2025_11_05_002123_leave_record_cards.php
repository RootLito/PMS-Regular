<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_record_cards', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id');
            $table->json('records')->nullable(); 

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
