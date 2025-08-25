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
        Schema::create('price_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('external_id')->nullable();
            $table->string('xero_invoice_prefix')->nullable();
            $table->boolean('fixed_price')->default(false);
            $table->boolean('provider_travel')->default(false);
            $table->boolean('national_pricing')->default(false);
            $table->enum('is_archive', ['Archive', 'Unarchive'])->default('Unarchive');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_books');
    }
};
