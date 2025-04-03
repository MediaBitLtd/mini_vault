<?php

namespace App\Actions\Dashboard;

use App\Actions\Auth\MigrateVaults;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class OnboardSetPassword
{
    use AsAction;

    public function rules(): array
    {
        return [
            'password' => 'required|string|confirmed|min:8',
        ];
    }

    public function authorized(): bool
    {
        return Auth::user() && !Auth::user()->onboard;
    }

    public function handle(string $password): array
    {
        /** @var User $user */
        $user = Auth::user();

        return DB::transaction(function () use ($user, $password): array {
            MigrateVaults::make()->handle($user, $password);

            Cache::put("oauth.pkey:$user->id", $user->getPKey($password), 60);

            return [
                'access_token' => $user->createToken('Mini Vault PAC')->accessToken,
            ];
        });
    }

    public function asController(ActionRequest $request): array
    {
        return $this->handle($request->get('password'));
    }
}
