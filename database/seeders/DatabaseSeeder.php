<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Laravel\Passport\Client;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LiveDatabaseSeeder::class);
        User::factory()->create([
            'first_name' => 'Joao',
            'last_name' => 'Matos',
            'email' => 'joao.matos@media-bit.co.uk',
        ]);

        Client::factory()->create([
            'id' => 1,
            'name' => 'Mini Vault',
            'secret' => '4yPJCzknzdoUs3iHSL4DLwSTSziwFWA6SXKrYghd',
            'redirect' => 'https://vault.home.cloud/auth/callback',
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'requires_user_key' => true,
        ]);

        Client::factory()->create([
            'id' => 2,
            'name' => 'Mini Vault NPKey',
            'secret' => '8tLejTaJdWPKMMIDTvfTfLlFyPmCOdOsipXFAw3t',
            'redirect' => 'https://vault.home.cloud/auth/callback',
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'requires_user_key' => false,
        ]);
    }
}
