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
        Schema::table('shifts', function (Blueprint $table) {

            // 🔗 One UUID for whole recurring series
            $table->uuid('series_uuid')
                  ->nullable()
                  ->after('id')
                  ->index();

            // 🧩 Optional parent shift (master shift)
            $table->unsignedBigInteger('parent_shift_id')
                  ->nullable()
                  ->after('series_uuid');

            // 🔐 Foreign key (safe & optional)
            $table->foreign('parent_shift_id')
                  ->references('id')
                  ->on('shifts')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {

            // Drop FK first
            $table->dropForeign(['parent_shift_id']);

            // Drop columns
            $table->dropColumn([
                'series_uuid',
                'parent_shift_id',
            ]);
        });
    }
};
