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
        Schema::create('staff_payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pay_group_id')->nullable()->constrained()->onDelete('set null');
            $table->string('allowances')->nullable(); 
            $table->string('daily_hours')->nullable(); 
            $table->string('weekly_hours')->nullable(); 
            $table->string('external_system_identifier')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_payroll_settings');
    }
};
