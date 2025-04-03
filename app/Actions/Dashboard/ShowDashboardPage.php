<?php

namespace App\Actions\Dashboard;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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

    public function asController(): Response|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->onboard) {
            return redirect()->route('onboard');
        }

        return $this->handle();
    }
}
