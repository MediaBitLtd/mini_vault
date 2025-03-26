<?php

namespace Database\Factories;

use App\Models\Authorization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Authorization */
class AuthorizationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'authn_id' => $this->faker->uuid(),
            'credential_id' => Str::random(32),
            'public_key' => $this->faker->text(),
            'counter' => $this->faker->randomNumber(3),
        ];
    }
}
