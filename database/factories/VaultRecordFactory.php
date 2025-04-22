<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Field;
use App\Models\VaultRecord;
use App\Models\VaultRecordTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** @extends VaultRecord */
class VaultRecordFactory extends Factory
{
    public function configure(): self
    {
        return $this->afterCreating(function (VaultRecord $record) {
            // Change of creating a new field
            if (rand(0, 100) < 30) {
                $encrypter = $record->vault->getEncrypter();
                DB::table('vault_record_values')->insert([
                    [
                        'vault_record_id' => $record->id,
                        'field_id' => Field::query()->inRandomOrder()->first()->id,
                        'uid' => $uid = Str::uuid()->toString(),
                        'value' => $encrypter->encrypt([
                            'uid' => $uid,
                            'value' => null,
                            'originator' => null,
                        ]),
                    ],
                ]);
            }

            foreach ($record->values as $value) {
                $value->value = rand(0, 100) < 80
                    ? match ($value->field->slug) {
                        'website' => $this->faker->url(),
                        'host' => $this->faker->randomElement([
                            $this->faker->ipv4(),
                            $this->faker->ipv6(),
                            $this->faker->url(),
                        ]),
                        'password' => $this->faker->password(),
                        'username' => $this->faker->userName(),
                        'note' => $this->faker->realTextBetween(150, 400),
                        'secret_note' => $this->faker->realTextBetween(150, 400),
                        'public_key' => $this->faker->text(),
                        'private_key' => $this->faker->text(),
                        'pin' => str_pad($this->faker->randomNumber($n = rand(4, 6)), $n, '0', STR_PAD_LEFT),
                        '2fa' => json_encode([
                            'period' => 30,
                            'secret' => $this->faker->uuid(),
                        ]),
                        default => $this->faker->word(),
                    } : null;

                $value->name = rand(0, 100) < 30
                    ? $this->faker->word()." ({$value->field->slug})"
                    : null;

                $value->save();
            }

            if (rand(0, 100) < 30) {
                VaultRecordTag::factory()->count(rand(1, 3))->create([
                    'vault_record_id' => $record->id,
                ]);
            }
        });
    }

    public function definition(): array
    {
        return [
            'category_id' => Category::query()->inRandomOrder()->first()->id,
            'name' => $this->faker->words(2, true),
            'is_favourite' => rand(0, 100) < 10,
        ];
    }
}
