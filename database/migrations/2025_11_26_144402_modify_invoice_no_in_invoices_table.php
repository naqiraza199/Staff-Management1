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
        Schema::table('invoices', function (Blueprint $table) {
            // drop existing unique index first
            $table->dropUnique('invoices_invoice_no_unique');

            // then change column length
            $table->string('invoice_no', 20)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique('invoices_invoice_no_unique');
            $table->string('invoice_no')->unique()->change();
        });
    }
};
