<?php

namespace App\Actions\Dashboard;

use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowOnboardPage
{
    use AsAction;

    public function handle(): Response
    {
        return Inertia::render('Onboard');
    }
}
