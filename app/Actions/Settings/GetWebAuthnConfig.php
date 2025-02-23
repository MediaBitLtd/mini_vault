<?php

namespace App\Actions\Settings;

use App\Models\Authorization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GetWebAuthnConfig
{
    use AsAction;

    public function handle(): array
    {
        /** @var User $user */
        $user = Auth::user();

        Cache::put("webauthn.register:$user->id", [
            'challenge' => $challenge = Str::random(40),
            'uid' => $authnId = Str::uuid(),
        ], 120);

        return [
            'challenge' => $challenge,
            'rp' => [
                'name' => Config::get('app.name'),
                'id' => Str::of(Config::get('app.url'))
                    ->replace('https://', '')
                    ->replace('http://', '')
                    ->value(),
            ],
            'user' => [
                'id' => $authnId,
                'name' => $user->fullName,
                'displayName' => $user->fullName,
            ],
            'excludeCredentials' => $user->authorizations->map(static fn(Authorization $auth) => [
                'id' => $auth->credential_id,
                'type' => 'public-key',
            ]),
            'timeout' => 60_000,
            'attestation' => 'direct',
            'authenticatorSelection' => [
                'residentKey' => 'preferred',
                'userVerification' => 'preferred',
                'authenticatorAttachment' => 'platform',
            ],
            'pubKeyCredParams' => [
                [
                    'alg' => -7,
                    'type' => 'public-key',
                ],
                [
                    'alg' => -257,
                    'type' => 'public-key',
                ],
            ],
            'hints' => [
                'client-device',
            ],
            'extensions' => [
                'credProps' => true,
            ],
        ];
    }

    public function htmlResponse(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function jsonResponse(array $config): JsonResponse
    {
        return new JsonResponse($config, 201);
    }
}
