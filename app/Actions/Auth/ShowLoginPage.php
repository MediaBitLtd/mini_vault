<?php

namespace App\Actions\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowLoginPage
{
    use AsAction;

    public function handle(): Response|RedirectResponse
    {
        if (str_starts_with(request()->session()->get('url.intended') ?? '', url('/oauth/authorize'))) {
            $data = parse_url(request()->session()->get('url.intended'));
            $queryData = [];
            parse_str($data['query'], $queryData);

            if (! str_starts_with($queryData['redirect_uri'] ?? '', url(''))) {
                $intendedUrl = request()->session()->get('url.intended');
                Session::put('oauth_redirect', $intendedUrl);
            }
        }

        if (! Session::has('oauth_state') && ! Session::has('oauth_redirect')) {
            Session::put('oauth_state', $state = Str::random(40));

            $query = http_build_query([
                'client_id' => 1,
                'redirect_uri' => route('auth.callback'),
                'response_type' => 'code',
                'scope' => '*',
                'state' => $state,
                'prompt' => 'login',
            ]);

            return redirect(route('passport.authorizations.authorize').'?'.$query);
        }

        return Inertia::render('Auth/Login');
    }
}
