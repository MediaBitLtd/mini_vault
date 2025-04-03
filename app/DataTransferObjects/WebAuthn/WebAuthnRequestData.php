<?php

namespace App\DataTransferObjects\WebAuthn;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Data;

class WebAuthnRequestData extends Data
{
    public function __construct(
        public string $id,
        #[In('platform')]
        public string $authenticatorAttachment,
        #[In('public-key')]
        public string $credentialType,
        public WebAuthnClientData $clientData,
        public WebAuthnAuthenticatorData $authenticatorData,
        public ?string $publicKey,
        public ?string $publicKeyAlg,
    ) {}
}
