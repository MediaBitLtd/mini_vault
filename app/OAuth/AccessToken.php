<?php

namespace App\OAuth;

use DateTimeImmutable;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Bridge\AccessToken as PassportAccessToken;
use Laravel\Passport\Client;
use Lcobucci\JWT\UnencryptedToken;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;

class AccessToken extends PassportAccessToken
{
    use AccessTokenTrait;

    private function convertToJWT(): UnencryptedToken
    {
        $this->initJwtConfiguration();

        $builder = $this->jwtConfiguration
            ->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new DateTimeImmutable)
            ->canOnlyBeUsedAfter(new DateTimeImmutable)
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string) $this->getUserIdentifier())
            ->withClaim('scopes', $this->getScopes());

        // Verify if the client will need access to vaults

        /** @var Client $client */
        $client = Client::query()->findOrFail($this->getClient()->getIdentifier());
        if ((bool) $client->getAttribute('requires_user_key')) {
            $pkey = Cache::pull("oauth.pkey:{$this->getUserIdentifier()}");

            throw_if(! $pkey);

            return $builder->withClaim('cypher', encrypt($pkey))
                ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
        }

        return $builder
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }
}
