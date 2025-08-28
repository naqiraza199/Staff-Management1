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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->json('client_section')->nullable();     // client_id, price_book_id, funds
            $table->json('shift_section')->nullable();      // shift_type_id, additional_shift_types, allowance_id
            $table->json('time_and_location')->nullable();  // date, times, repeat, recurrence, address, etc.
            $table->json('carer_section')->nullable();      // user_id, pay_group_id
            $table->json('job_section')->nullable();        // team_id, shift_assignment
            $table->json('instruction')->nullable();        // description
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
