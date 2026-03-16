<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('oauth_clients')
            ->whereNotNull('secret')
            ->eachById(function ($client) {
                if (! str_starts_with($client->secret, '$2y$')) {
                    DB::table('oauth_clients')
                        ->where('id', $client->id)
                        ->update(['secret' => Hash::make($client->secret)]);
                }
            });
    }

    public function down(): void
    {
        //
    }
};
