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
            ['slug' => 'secret_note', 'label' => 'Secret Note', 'type' => FieldType::TEXTAREA->value, 'sensitive' => true],
            ['slug' => 'pin', 'label' => 'Pin', 'type' => FieldType::PIN->value, 'sensitive' => true],
            ['slug' => '2fa', 'label' => 'Two Factor Auth', 'type' => FieldType::TWO_FA->value, 'sensitive' => false],
            ['slug' => 'host', 'label' => 'Host', 'type' => FieldType::TEXT->value, 'sensitive' => false],
            ['slug' => 'public_key', 'label' => 'Public Key', 'type' => FieldType::TEXTAREA->value, 'sensitive' => false],
            ['slug' => 'private_key', 'label' => 'Private Key', 'type' => FieldType::TEXTAREA->value, 'sensitive' => true],
        ];

        Field::query()->insert($fields);
    }
}
