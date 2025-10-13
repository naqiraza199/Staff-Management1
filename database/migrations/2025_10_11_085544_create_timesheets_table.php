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
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            $table->unsignedInteger('approved_status')->default(0);

            $table->unsignedInteger('weekday_12a_6a')->default(0);
            $table->unsignedInteger('weekday_6a_8p')->default(0);
            $table->unsignedInteger('weekday_8p_10p')->default(0);
            $table->unsignedInteger('weekday_10p_12a')->default(0);
            $table->unsignedInteger('saturday')->default(0);
            $table->unsignedInteger('sunday')->default(0);
            $table->unsignedInteger('standard_hours')->default(0);
            $table->unsignedInteger('break_time')->default(0);
            $table->unsignedInteger('public_holidays')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->unsignedInteger('mileage')->default(0);
            $table->unsignedInteger('expense')->default(0);
            $table->unsignedInteger('sleepover')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
