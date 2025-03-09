<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Field;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $dbFields = Field::query()->get();

        $categories = collect([
            [
                'record' => ['slug' => 'password', 'name' => 'Simple Password', 'description' => null, 'icon' => 'pi-lock'],
                'fields' => ['password'],
            ],
            [
                'record' => ['slug' => 'user_password', 'name' => 'User Login', 'description' => null, 'icon' => 'pi-user'],
                'fields' => ['username', 'password'],
            ],
            [
                'record' => ['slug' => 'application', 'name' => 'App Login', 'description' => null, 'icon' => 'pi-mobile'],
                'fields' => ['appname', 'username', 'password'],
            ],
            [
                'record' => ['slug' => 'pin', 'name' => 'Pin number', 'description' => null, 'icon' => 'pi-lock'],
                'fields' => ['pin'],
            ],
            [
                'record' => ['slug' => 'website', 'name' => 'Website', 'description' => null, 'icon' => 'pi-globe'],
                'fields' => ['website', 'username', 'password'],
            ],
            [
                'record' => ['slug' => '2fawebsite', 'name' => 'Website (2fa)', 'description' => 'Website record with Two-factor authentication', 'icon' => 'pi-globe'],
                'fields' => ['website', 'username', 'password', '2fa'],
            ],
            [
                'record' => ['slug' => '2fa', 'name' => 'Two factor authentication', 'description' => null, 'icon' => 'pi-qrcode'],
                'fields' => ['appname', 'username', '2fa'],
            ],
        ]);

        Category::query()->insert($categories->pluck('record')->toArray());
        $dbCategories = Category::query()->get();

        $pivot = [];
        foreach ($categories as $category) {
            foreach ($category['fields'] as $order => $fieldSlug) {
                $pivot[] = [
                    'category_id' => $dbCategories
                        ->where('slug', '=', $category['record']['slug'])
                        ->first()
                        ->id,
                    'field_id' => $dbFields
                        ->where('slug', '=', $fieldSlug)
                        ->first()
                        ->id,
                    'order' => $order + 1,
                ];
            }
        }

        DB::table('category_field')->insert($pivot);
    }
}
