<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove auto-increment from oauth_clients.id, convert to string
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->string('id', 36)->change();
        });

        // Convert client_id columns in related tables
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->string('client_id', 36)->change();
        });

        Schema::table('oauth_auth_codes', function (Blueprint $table) {
            $table->string('client_id', 36)->change();
        });
    }

    public function down(): void
    {
        //
    }
};
