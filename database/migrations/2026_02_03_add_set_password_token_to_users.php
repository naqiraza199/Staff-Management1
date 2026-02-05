<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('set_password_token')->nullable()->after('password');
            $table->timestamp('set_password_sent_at')->nullable()->after('set_password_token');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['set_password_token', 'set_password_sent_at']);
        });
    }
};
