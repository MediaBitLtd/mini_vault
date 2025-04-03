<?php

namespace App\OAuth;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Passport\Client as BaseClient;

/**
 * @property string $redirect
 */
class Client extends BaseClient
{
    public function skipsAuthorization(): bool
    {
        return Str::contains($this->redirect, Config::get('app.url'));
    }
}
