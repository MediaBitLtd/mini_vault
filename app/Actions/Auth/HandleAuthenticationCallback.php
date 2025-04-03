<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HandleAuthenticationCallback
{
    use AsAction;

    public function handle(ActionRequest $request): Response|\Symfony\Component\HttpFoundation\Response
    {
        $state = Session::pull('oauth_state');

        if (!(strlen($state) > 0 && $state === $request->get('state'))) {
            return Inertia::location('/');
        }

        return Inertia::render('Auth/Callback', [
            'code' => $request->get('code'),
        ]);
    }
}
