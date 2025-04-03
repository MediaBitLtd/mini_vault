<?php

namespace App\Providers;

use App\OAuth\AccessTokenRepository;
use App\OAuth\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Bridge\AccessTokenRepository as PassportAccessTokenRepository;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        App::bind(PassportAccessTokenRepository::class, AccessTokenRepository::class);
        Passport::useClientModel(Client::class);
    }

    public function boot(): void
    {
        URL::forceRootUrl(config('app.url'));

        if (str_contains(config('app.url'), 'https')) {
            URL::forceScheme('https');
            // Override incoming requests to be HTTPS in "Laravel's eyes"
            $this->app['request']->server->set('HTTPS', 'on');
        }
    }
}
