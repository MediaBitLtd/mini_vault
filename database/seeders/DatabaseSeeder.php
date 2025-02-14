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
        User::factory()->create([
            'name' => 'Joao Matos',
            'email' => 'joao.matos@media-bit.co.uk',
        ]);

        Client::factory()->create([
            'id' => 1,
            'name' => 'Mini Vault',
            'secret' => '4yPJCzknzdoUs3iHSL4DLwSTSziwFWA6SXKrYghd',
            'redirect' => 'https://vault.home.cloud/auth/callback',
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0,
        ]);
    }
}
