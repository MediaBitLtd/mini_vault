<?php

namespace App\DataTransferObjects\WebAuthn;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Data;

class WebAuthnClientData extends Data
{
    public function __construct(
        #[In('webauthn.get', 'webauthn.create')]
        public string $type,
        public string $challenge,
        public string $origin,
        public ?bool $crossOrigin,
    ) {}
}
