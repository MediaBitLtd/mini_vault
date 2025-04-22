<?php

namespace Database\Factories;

use App\Models\VaultRecordTag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VaultRecordTag>
 */
class VaultRecordTagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
