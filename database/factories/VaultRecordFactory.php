<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\VaultRecord;
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
                // TODO change this
                $encrypter = $record->vault->getEncrypter();
                DB::table('vault_record_values')->insert([
                    [
                        'vault_record_id' => $record->id,
                        'field_id' => 4,
                        'uid' => $uid = Str::uuid()->toString(),
                        'value' => $encrypter->encrypt([
                            'uid' => $uid,
                            'value' => null,
                            'originator' => null,
                        ]),
                    ],
                    [
                        'vault_record_id' => $record->id,
                        'field_id' => 4,
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
                if (rand(0, 100) < 30) {
                    continue; // Leave field empty
                }

                $value->value = match($value->field->slug) {
                    'website' => $this->faker->url(),
                    'password' => $this->faker->password(),
                    'username' => $this->faker->userName(),
                    'note' => $this->faker->realTextBetween(150, 400),
                    default => $this->faker->word(),
                };
                $value->save();
            }
        });
    }

    public function definition(): array
    {
        return [
            'category_id' => Category::query()->inRandomOrder()->first()->id,
            'name' => $this->faker->boolean() ? $this->faker->sentence(2) : null,
            'is_favourite' => rand(0, 100) < 10,
        ];
    }
}
