<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
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
