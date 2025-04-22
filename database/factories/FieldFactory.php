<?php

namespace Database\Factories;

use App\Enums\FieldType;
use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Field
 */
class FieldFactory extends Factory
{
    public function definition(): array
    {
        return [
            'label' => $this->faker->text(50),
            'type' => $this->faker->randomElement(FieldType::cases()),
            'slug' => $this->faker->slug(),
        ];
    }
}
