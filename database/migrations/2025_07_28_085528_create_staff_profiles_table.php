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
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('salutation', ['Mr', 'Mrs', 'Miss', 'Ms', 'Mx', 'Doctor', 'Them', 'They']);
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('mobile_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('role_type', ['Carer', 'Office User']);
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('gender', ['Male', 'Female', 'Intersex', 'Non-binary', 'Unspecified', 'Prefer not to say']);
            $table->date('dob')->nullable();
            $table->enum('employment_type', ['Casual', 'Part-Time', 'Full-Time', 'Contractor', 'Ohters']);
            $table->text('address')->nullable();
            $table->string('profile_pic')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_profiles');
    }
};
