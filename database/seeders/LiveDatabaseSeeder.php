<?php

namespace Database\Seeders;

use App\Enums\FieldType;
use App\Models\Category;
use App\Models\Field;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LiveDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(FieldSeeder::class);
        $this->call(CategorySeeder::class);
    }
}
