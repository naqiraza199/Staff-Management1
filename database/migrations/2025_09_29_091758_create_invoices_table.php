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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('additional_contact_id')->nullable();
            
            $table->json('billing_reports_ids')->nullable();

            $table->string('invoice_no')->unique(); 
            $table->string('purchase_order')->nullable(); 
            $table->date('issue_date')->nullable();
            $table->date('payment_due')->nullable();

            $table->string('NDIS')->nullable();
            $table->string('ref_no')->nullable();

            $table->enum('status', ['Paid', 'Unpaid/Overdue', 'Overdue'])->default('Unpaid/Overdue');

            $table->decimal('amount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
