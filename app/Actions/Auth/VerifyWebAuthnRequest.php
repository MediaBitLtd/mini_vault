<?php

namespace App\Actions\Auth;

use App\DataTransferObjects\WebAuthn\WebAuthnRequestData;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class VerifyWebAuthnRequest
{
    use AsAction;

    public function handle(
        ActionRequest $request,
        ?string $challenge = null,
        ?string $type = null,
    ): WebAuthnRequestData {
        $response = $request->get('response');
        $data = WebAuthnRequestData::from([
            'id' => $request->get('id'),
            'authenticatorAttachment' => $request->get('authenticatorAttachment'),
            'credentialType' => $request->get('type'),
            'clientData' => json_decode(base64_decode($response['clientDataJSON'] ?? ''), true),
            'authenticatorData' => $this->parseAuthenticatorData(
                base64_decode($response['authenticatorData'] ?? '')
            ),
            'publicKey' => $response['publicKey'] ?? null,
            'publicKeyAlg' => $response['publicKeyAlg'] ?? null,
        ]);

        if (isset($challenge) && $challenge !== $data->clientData->challenge) {
            throw new BadRequestException;
        }

        if (isset($type) && $type !== $data->clientData->type) {
            throw new BadRequestException;
        }

        $origin = Str::of($data->clientData->origin)
            ->replace('https://', '')
            ->replace('http://', '')
            ->value();

        if (!in_array($origin, Config::get('auth.webauthn.allowed-origins'))) {
            throw new BadRequestException;
        }

        return $data;
    }

    protected function parseAuthenticatorData(string $data): array
    {
        $flags = last(unpack('C', substr($data, 32, 1)));

        return [
            'rpIdHash' => base64_encode(substr($data, 0, 32)),
            'flags' => [
                'userPresent' => !!($flags & 1),
                'userVerified' => !!($flags & 4),
                'attestedData' => !!($flags & 64),
                'extensionsIncluded' => !!($flags & 128),
            ],
            'signCount' => strlen($data) > 36
                ? last(unpack('N', substr($data, 33, 4)))
                : null,
        ];
    }
}
