<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LiveDatabaseSeeder::class);

        DB::table('users')->insert([
            [
                'first_name' => 'Admin',
                'last_name' => 'Account',
                'email' => env('ADMIN_EMAIL', 'admin@example.com'),
                'is_admin' => true,
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'key' => Hash::make(Str::random(40)),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        if ($user = env('USER_EMAIL')) {
            DB::table('users')->insert([
                [
                    'first_name' => 'User',
                    'last_name' => 'Account',
                    'email' => $user,
                    'is_admin' => false,
                    'password' => Hash::make(env('USER_PASSWORD', 'password')),
                    'key' => Hash::make(Str::random(40)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        DB::table('oauth_clients')->insert([
            [
                'id' => env('VITE_CLIENT_ID', '120cd582-de2a-4c00-a578-05d04133f83d'),
                'name' => 'Mini Vault',
                'secret' => Hash::make(env('VITE_CLIENT_SECRET', '4yPJCzknzdoUs3iHSL4DLwSTSziwFWA6SXKrYghd')),
                'redirect' => config('app.url').'/auth/callback',
                'personal_access_client' => false,
                'password_client' => false,
                'revoked' => false,
                'requires_user_key' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Mini Vault PAC',
                'secret' => Hash::make(Str::random(40)),
                'redirect' => 'http://localhost',
                'personal_access_client' => true,
                'password_client' => false,
                'revoked' => false,
                'requires_user_key' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
