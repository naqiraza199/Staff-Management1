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
        Schema::table('documents', function (Blueprint $table) {
                $table->longText('signature')->nullable();   // Base64 or stored image path
                $table->timestamp('signed_at')->nullable();  // When client signed
                $table->boolean('is_verified')->default(false);
                $table->string('signature_token')->nullable()->unique(); // secure token for link
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            //
        });
    }
};
