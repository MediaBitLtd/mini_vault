<?php

namespace App\Actions\Auth;

use App\Models\Authorization;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use RuntimeException;

class HandleWebauthnLogin
{
    use AsAction;

    public function handle(ActionRequest $request)
    {
        $requestData = VerifyWebAuthnRequest::make()->handle($request, null, 'webauthn.get');

        /** @var Authorization $authorization */
        $authorization = Authorization::query()
            ->where('credential_id', '=', $requestData->id)
            ->with('user')
            ->firstOrFail();

        $user = $authorization->user;
        $authData = Cache::pull("webauthn.auth:$user->id");
        $expectedChallenge = $authData['challenge'] ?? '__invalid__';

        if ($requestData->clientData->challenge !== $expectedChallenge) {
            throw new RuntimeException;
        }

        $isValidSignature = $this->verifySignature($authorization->public_key, $request->get('response'));

        if (!$isValidSignature) {
            throw new AuthorizationException;
        }

        Cache::put("oauth.pkey:$user->id", $authData['pkey'], 60);

        return [
            'access_token' => $user->createToken('Mini Vault PAC')->accessToken,
        ];
    }

    public function asController(ActionRequest $request)
    {
        try {
            $data = $this->handle($request);
        } catch (Exception $e) {
            throw new AuthorizationException;
        }

        return $data;
    }

    protected function verifySignature(
        string $publicKey,
        array $data,
    ): bool {
        $signature = $data['signature'];
        $clientData = $data['clientDataJSON'];
        $authenticatorData = $data['authenticatorData'];

        // TODO !important

        return true;
    }
}
