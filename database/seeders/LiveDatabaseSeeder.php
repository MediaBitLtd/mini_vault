<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LiveDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(FieldSeeder::class);
        $this->call(CategorySeeder::class);
    }
}
