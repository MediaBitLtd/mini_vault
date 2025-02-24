<?php

use Illuminate\Support\Str;

return [
    'webauthn' => [
        'allowed-origins' => [
            Str::of(env('APP_URL', 'http://localhost'))
                ->replace('https://', '')
                ->replace('http://', '')
                ->value(),
        ],
    ],
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],
];
