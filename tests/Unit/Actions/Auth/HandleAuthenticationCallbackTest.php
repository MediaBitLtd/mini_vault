<?php

namespace Actions\Auth;

use App\Actions\Auth\HandleAuthenticationCallback;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Lorisleiva\Actions\ActionRequest;
use Tests\TestCase;

class HandleAuthenticationCallbackTest extends TestCase
{
    public function test_action()
    {
        Session::put('oauth_state', 'test');

        Inertia::shouldReceive('render')
            ->once()
            ->with('Auth/Callback', ['code' => 'test_code']);
        Inertia::shouldReceive('location')
            ->once()
            ->with('/');

        $request = new ActionRequest([
            'state' => 'test',
            'code' => 'test_code',
        ]);

        HandleAuthenticationCallback::make()->handle($request);
        HandleAuthenticationCallback::make()->handle($request);
    }
}
