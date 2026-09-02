<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'oauth_provider')) {
                $table->string('oauth_provider')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'oauth_id')) {
                $table->string('oauth_id')->nullable()->after('oauth_provider');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('oauth_id');
            }
            if (!Schema::hasColumn('users', 'oauth_data')) {
                $table->json('oauth_data')->nullable()->after('avatar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['oauth_provider', 'oauth_id', 'avatar', 'oauth_data']);
        });
    }
};
