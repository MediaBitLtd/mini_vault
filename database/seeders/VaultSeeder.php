<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vault;
use App\Models\VaultRecord;
use Exception;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VaultSeeder extends Seeder
{
    public function __construct(protected Generator $faker) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var User $user */
        $user = User::query()->where('is_admin', '=', false)->inRandomOrder()->firstOrFail();

        if (! Hash::check('password', $user->password)) {
            throw new Exception('Picked a random user that doesn\'t have the right password');
        }

        DB::transaction(function () use ($user) {
            blink()->put('pkey', $user->getPKey('password'));

            /** @var Vault $vault */
            $vault = Vault::factory()->create(['user_id' => $user->id]);

            $vault->sign();
            $vault->save();

            VaultRecord::factory()->for($vault)->someWithTags()->count(rand(50, 100))->create();
        });
    }
}
