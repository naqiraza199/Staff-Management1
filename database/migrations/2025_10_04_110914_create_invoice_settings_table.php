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
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('abn');
            $table->string('address');
            $table->string('phone');
            $table->string('payment_terms');
            $table->string('contact_email');
            $table->text('email_message')->nullable();
            $table->enum('payment_rounding', ['decimal'])->default('decimal');
            $table->string('ndia_provider_number')->nullable();
            $table->enum('cost_calculation_is_based_on', ['start_time', 'end_time'])->default('end_time');
            $table->string('cancelled_by_client_label')->default('Cancelled by client');
            $table->text('cancel_message')->default('Client Cancelled Shift on short Notice');
            $table->string('invoice_item_default_format')->default('{client} {shift} {date} {shift,start} {shift,end} {price,book} {price,ref}');
            $table->integer('default_invoice_due_days')->default(14);
            $table->boolean('invoice_based_on_approved_shift_times')->default(false);
            $table->boolean('invoice_mileage_based_on_notional_pricing')->default(false);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};
