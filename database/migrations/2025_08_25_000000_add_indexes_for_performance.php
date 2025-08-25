<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('company_id');
            $table->index('is_archive');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('is_archive');
        });

        Schema::table('team_has_staffs', function (Blueprint $table) {
            $table->index('team_id');
            $table->index('user_id');
        });

        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (Schema::hasColumn('documents', 'client_id')) {
                    $table->index('client_id');
                }
                if (Schema::hasColumn('documents', 'user_id')) {
                    $table->index('user_id');
                }
            });
        }

        if (Schema::hasTable('price_books')) {
            Schema::table('price_books', function (Blueprint $table) {
                if (Schema::hasColumn('price_books', 'user_id')) {
                    $table->index('user_id');
                }
            });
        }

        if (Schema::hasTable('price_book_details')) {
            Schema::table('price_book_details', function (Blueprint $table) {
                if (Schema::hasColumn('price_book_details', 'price_book_id')) {
                    $table->index('price_book_id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['company_id']);
            $table->dropIndex(['is_archive']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['is_archive']);
        });

        Schema::table('team_has_staffs', function (Blueprint $table) {
            $table->dropIndex(['team_id']);
            $table->dropIndex(['user_id']);
        });

        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (Schema::hasColumn('documents', 'client_id')) {
                    $table->dropIndex(['client_id']);
                }
                if (Schema::hasColumn('documents', 'user_id')) {
                    $table->dropIndex(['user_id']);
                }
            });
        }

        if (Schema::hasTable('price_books')) {
            Schema::table('price_books', function (Blueprint $table) {
                if (Schema::hasColumn('price_books', 'user_id')) {
                    $table->dropIndex(['user_id']);
                }
            });
        }

        if (Schema::hasTable('price_book_details')) {
            Schema::table('price_book_details', function (Blueprint $table) {
                if (Schema::hasColumn('price_book_details', 'price_book_id')) {
                    $table->dropIndex(['price_book_id']);
                }
            });
        }
    }
}; 