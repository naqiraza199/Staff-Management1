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
        Schema::create('staff_contacts', function (Blueprint $table) {
            $table->id();
             $table->foreignId('user_id')->constrained()->onDelete('cascade');
             $table->string('kin_name')->nullable(); 
             $table->string('kin_relation')->nullable(); 
             $table->string('kin_contact')->nullable(); 
             $table->string('kin_email')->nullable(); 
             $table->boolean('same_as_kin')->nullable(); 
             $table->string('emergency_contact_name')->nullable(); 
             $table->string('emergency_contact_relation')->nullable(); 
             $table->string('emergency_contact_contact')->nullable(); 
             $table->string('emergency_contact_email')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_contacts');
    }
};
