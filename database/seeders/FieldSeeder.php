<?php

namespace Database\Seeders;

use App\Enums\FieldType;
use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            ['slug' => 'appname', 'label' => 'Application Name', 'type' => FieldType::TEXT->value, 'sensitive' => false],
            ['slug' => 'username', 'label' => 'User Name', 'type' => FieldType::TEXT->value, 'sensitive' => false],
            ['slug' => 'password', 'label' => 'Password', 'type' => FieldType::PASSWORD->value, 'sensitive' => true],
            ['slug' => 'website', 'label' => 'Website', 'type' => FieldType::URL->value, 'sensitive' => false],
            ['slug' => 'note', 'label' => 'Note', 'type' => FieldType::TEXTAREA->value, 'sensitive' => false],
            ['slug' => 'pin', 'label' => 'Pin', 'type' => FieldType::PIN->value, 'sensitive' => true],
            ['slug' => '2fa', 'label' => 'Two Factor Auth', 'type' => FieldType::TWO_FA->value, 'sensitive' => false],
        ];

        Field::query()->insert($fields);
    }
}
