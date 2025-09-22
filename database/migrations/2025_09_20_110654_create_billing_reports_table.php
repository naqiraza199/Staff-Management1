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
        Schema::create('billing_reports', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('shift_id')->constrained()->onDelete('cascade');
            $table->string('staff')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('hours_x_rate')->nullable();
            $table->decimal('additional_cost', 10, 2)->nullable();
            $table->string('distance_x_rate')->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->decimal('running_total', 10, 2)->nullable();
            $table->enum('status', ['Paid', 'Unpaid'])->default('Unpaid');
            $table->foreignId('price_book_id')->nullable();
            $table->string('fund')->nullable();
            $table->decimal('mileage', 10, 2)->nullable();
            $table->decimal('expense', 10, 2)->nullable();
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_remove')->default(false);
            $table->boolean('update_shift_time')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_reports');
    }
};
