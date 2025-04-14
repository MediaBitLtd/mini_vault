<?php

namespace App\Actions\Auth;

use App\Models\Authorization;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetBiometricAuthOptions
{
    use AsAction;

    public function handle(ActionRequest $request): array
    {
        $userId = $request->get('userId');
        $userBiometricKey = $request->get('userKey');

        try {
            /** @var User $user */
            $user = User::query()
                ->where('id', '=', $userId)
                ->where('biometric_key', '=', $userBiometricKey)
                ->with('authorizations')
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new AuthorizationException;
        }

        Cache::put("webauthn.auth:$user->id", [
            'challenge' => $challenge = Str::random(40),
            'pkey' => decrypt($request->get('cypher')),
        ], 120);

        return [
            'rpId' => Str::of(Config::get('app.url'))
                ->replace('https://', '')
                ->replace('http://', '')
                ->value(),
            'challenge' => $challenge,
            'allowCredentials' => $user->authorizations->map(static fn (Authorization $auth) => $auth->credential_id),
        ];
    }
}
