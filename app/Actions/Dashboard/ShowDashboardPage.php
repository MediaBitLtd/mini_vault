<?php

namespace App\Actions\Dashboard;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowDashboardPage
{
    use AsAction;

    public function handle(): Response
    {
        return Inertia::render('Dashboard');
    }

    public function asController(): Response|\Symfony\Component\HttpFoundation\Response|RedirectResponse
    {
        $redirectUrl = Session::pull('oauth_redirect');

        if ($redirectUrl) {
            return Inertia::location($redirectUrl);
        }

        /** @var User $user */
        $user = Auth::user();

        if (!$user->onboard) {
            return redirect()->route('onboard');
        }

        return $this->handle();
    }
}
