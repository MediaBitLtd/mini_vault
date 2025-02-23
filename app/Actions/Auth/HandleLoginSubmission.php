<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

            // TODO !important, this cant be on cache lol
            Cache::put('oauth.pkey', $user->getPKey($request->get('password')));

            return Inertia::location($request->session()->get('url.intended', route('dashboard')));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
