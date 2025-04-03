<?php

namespace App\DataTransferObjects\WebAuthn;

use Spatie\LaravelData\Data;

class WebAuthnAuthenticatorData extends Data
{
    public function __construct(
        public string $rpIdHash,
        public ?int $signCount,
    ) {}
}
