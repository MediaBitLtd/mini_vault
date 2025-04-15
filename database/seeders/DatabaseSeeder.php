<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;
use Laravel\Passport\PersonalAccessClient;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LiveDatabaseSeeder::class);

        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'is_admin' => true,
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
        ]);

        Client::factory()->create([
            'id' => 1,
            'name' => 'Mini Vault',
            'secret' => '4yPJCzknzdoUs3iHSL4DLwSTSziwFWA6SXKrYghd',
            'redirect' => config('app.url') . '/auth/callback',
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'requires_user_key' => true,
        ]);

        Client::factory()->create([
            'id' => 2,
            'name' => 'Mini Vault NPKey',
            'secret' => '8tLejTaJdWPKMMIDTvfTfLlFyPmCOdOsipXFAw3t',
            'redirect' => config('app.url') . '/auth/callback',
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'requires_user_key' => false,
        ]);

        Client::factory()->create([
            'id' => 3,
            'name' => 'Mini Vault PAC',
            'secret' => 'JK5uPgcKrpvy4KCGXEIai59qxNCSBvvZ0YfEpyN5',
            'redirect' => 'http://localhost',
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
            'requires_user_key' => true,
        ]);

        PersonalAccessClient::query()->create([
            'client_id' => 3,
        ]);
    }
}
