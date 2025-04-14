<?php

namespace App\Actions\Settings;

use App\Actions\Auth\VerifyWebAuthnRequest;
use App\Models\User;
use App\Traits\Resources;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use RuntimeException;

class HandleWebAuthnRegistration
{
    use AsAction;
    use Resources;

    public function handle(ActionRequest $request): User
    {
        /** @var User $user */
        $user = Auth::user();

        $registerData = Cache::pull("webauthn.register:$user->id");

        if (!$registerData) {
            throw new RuntimeException;
        }

        $requestData = VerifyWebAuthnRequest::make()->handle(
            $request,
            $registerData['challenge'] ?? '__invalid__',
            'webauthn.create'
        );

        $user->authorizations()->create([
            'authn_id' => $registerData['uid'],
            'credential_id' => $requestData->id,
            'public_key' => $requestData->publicKey,
            'counter' => $requestData->authenticatorData->signCount,
        ]);

        if (!$user->biometric_key) {
            $user->biometric_key = hash('sha256', Str::random(32));
            $user->save();
        }

        return $user;
    }

    public function asController(ActionRequest $request): User
    {
        try {
            $user = $this->handle($request);
        } catch (Exception $exception) {
            Log::debug('Someone tried webauthn and was rejected: ' . $exception->getMessage());
            throw new AuthorizationException;
        }

        return $user;
    }

    public function jsonResponse(User $user): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'key' => $user->biometric_key,
        ], 201);
    }
}
