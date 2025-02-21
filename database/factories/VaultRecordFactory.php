<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\VaultRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends VaultRecord */
class VaultRecordFactory extends Factory
{
    public function configure(): self
    {
        return $this->afterCreating(function (VaultRecord $record) {
            foreach ($record->values as $value) {
                if (rand(0, 100) < 40) {
                    continue; // Leave field empty
                }

                $value->value = match($value->field->slug) {
                    'website' => $this->faker->url(),
                    'password' => $this->faker->password(),
                    'username' => $this->faker->userName(),
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
