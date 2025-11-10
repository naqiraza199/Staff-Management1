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
        Schema::table('shift_notes', function (Blueprint $table) {
            $table->foreignId('user_id')->default(1)->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_notes', function (Blueprint $table) {
            //
        });
    }
};
