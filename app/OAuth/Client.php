<?php

namespace App\OAuth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Passport\Client as BaseClient;

/**
 * @property string $redirect
 */
class Client extends BaseClient
{
    protected $hidden = [];

    protected function isValidUniqueId($value): bool
    {
        return is_string($value) && $value !== '';
    }

    public function skipsAuthorization(Authenticatable $user, array $scopes): bool
    {
        return Str::contains($this->redirect, Config::get('app.url'));
    }
}
