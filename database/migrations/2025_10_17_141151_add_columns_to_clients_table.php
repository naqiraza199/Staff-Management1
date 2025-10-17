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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('NDIS_number')->nullable(); 
            $table->string('aged_care_recipient_ID')->nullable(); 
            $table->string('reference_number')->nullable(); 
            $table->string('custom_field')->nullable(); 
            $table->string('PO_number')->nullable(); 
            $table->string('client_type')->nullable(); 
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
};
