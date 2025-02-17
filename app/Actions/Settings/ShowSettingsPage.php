<?php

namespace App\Actions\Settings;

use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowSettingsPage
{
    use AsAction;

    public function handle(): Response
    {
        return Inertia::render('Settings');
    }
}
