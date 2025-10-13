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
        Schema::create('timesheet_reports', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            
            $table->date('date');
            $table->json('clients')->nullable(); 
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->time('break_time')->nullable();
            $table->decimal('hours', 5, 2)->default(0); 
            $table->decimal('distance', 8, 2)->nullable();
            $table->decimal('expense', 10, 2)->nullable();
            $table->json('allowances')->nullable(); 
            
            $table->timestamp('clockin')->nullable();
            $table->timestamp('clockout')->nullable();

            $table->enum('status', ['Clockin', 'Approved'])->default('Clockin');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheet_reports');
    }
};
