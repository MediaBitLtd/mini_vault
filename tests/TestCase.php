<?php

namespace Tests;

use App\Models\User;
use App\Models\Vault;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\ActionRequest;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    public function getVaultAndUser(): array
    {
        $user = User::factory()->create();

        blink()->put('pkey', $user->getPKey('password'));

        /** @var Vault $vault */
        $vault = Vault::factory()->create(['user_id' => $user->id]);

        $vault->sign();
        $vault->save();

        return [$vault, $user];
    }

    public function createRequest(array $data, array $rules = []): ActionRequest
    {
        $request = new ActionRequest($data);
        $request->setValidator(Validator::make($data, $rules));
        return $request;
    }
}
