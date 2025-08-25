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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->string('support_item_number', 50)->nullable();
            $table->string('support_item_name')->nullable();
            $table->string('registration_group_number', 50)->nullable();
            $table->string('registration_group_name')->nullable();
            $table->string('support_category_name')->nullable();
            $table->string('support_category_number', 50)->nullable();
            $table->string('support_category_number_pace', 50)->nullable();
            $table->string('support_category_name_pace')->nullable();
            $table->string('unit', 10)->nullable();
            $table->string('quote', 10)->nullable();
            $table->string('start_date', 20)->nullable();
            $table->string('end_date', 20)->nullable();
            $table->decimal('act', 10, 2)->nullable();
            $table->decimal('nsw', 10, 2)->nullable();
            $table->decimal('nt', 10, 2)->nullable();
            $table->decimal('qld', 10, 2)->nullable();
            $table->decimal('sa', 10, 2)->nullable();
            $table->decimal('tas', 10, 2)->nullable();
            $table->decimal('vic', 10, 2)->nullable();
            $table->decimal('wa', 10, 2)->nullable();
            $table->decimal('remote', 10, 2)->nullable();
            $table->decimal('very_remote', 10, 2)->nullable();
            $table->string('non_face_to_face_support_provision', 10)->nullable();
            $table->string('provider_travel', 10)->nullable();
            $table->string('short_notice_cancellations', 10)->nullable();
            $table->string('NDIA_requested_reports', 10)->nullable();
            $table->string('irregular_SIL_supports', 10)->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
