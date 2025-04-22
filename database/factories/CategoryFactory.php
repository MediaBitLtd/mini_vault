<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Category
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->boolean() ? $this->faker->text() : null,
            'icon' => 'pi pi-lock',
            'slug' => $this->faker->slug(),
        ];
    }
}
