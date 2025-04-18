<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;

class HandleLoginSubmission
{
    use AsAction;

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function handle(ActionRequest $request): Response
    {
        if (Auth::attempt($request->validated())) {
            /** @var User $user */
            $user = Auth::user();

            Session::pull('oauth_redirect');
            Cache::put(
                "oauth.pkey:$user->id",
                $user->getPKey($request->get('password')),
                60,
            );

            return Inertia::location($request->session()->get('url.intended', route('dashboard')));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
