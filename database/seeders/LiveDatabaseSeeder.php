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
        // Roles

        // Fields

        $fields = [
            ['slug' => 'username', 'label' => 'User Name', 'type' => FieldType::TEXT->value],
            ['slug' => 'password', 'label' => 'Password', 'type' => FieldType::PASSWORD->value],
            ['slug' => 'website', 'label' => 'Website', 'type' => FieldType::URL->value],
        ];
        Field::query()->insert($fields);
        $dbFields = Field::query()->get();

        // Categories

        $categories = collect([
            [
                'record' => ['slug' => 'password', 'name' => 'Simple Password', 'description' => null, 'icon' => 'padlock'],
                'fields' => ['password'],
            ],
            [
                'record' => ['slug' => 'user_password', 'name' => 'User Login', 'description' => null, 'icon' => 'padlock'],
                'fields' => ['username', 'password'],
            ],
            [
                'record' => ['slug' => 'website', 'name' => 'Website', 'description' => null, 'icon' => 'padlock'],
                'fields' => ['website', 'username', 'password'],
            ],
        ]);

        Category::query()->insert($categories->pluck('record')->toArray());
        $dbCategories = Category::query()->get();

        $pivot = [];
        foreach ($categories as $category) {
            foreach ($category['fields'] as $fieldSlug) {
                $pivot[] = [
                    'category_id' => $dbCategories
                        ->where('slug', '=', $category['record']['slug'])
                        ->first()
                        ->id,
                    'field_id' => $dbFields
                        ->where('slug', '=', $fieldSlug)
                        ->first()
                        ->id,
                ];
            }
        }

        DB::table('category_field')->insert($pivot);
    }
}
