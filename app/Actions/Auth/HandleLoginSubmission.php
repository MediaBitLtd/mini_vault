<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

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

    public function handle(ActionRequest $request): RedirectResponse
    {
        if (Auth::attempt($request->validated())) {
            /** @var User $user */
            $user = Auth::user();

            Cache::put('oauth.pkey',  $user->getPKey($request->get('password')));

            return redirect()->intended('');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
