<?php

namespace App\Actions\Groups;

use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowAll
{
    use AsAction;

    public function handle(): Collection
    {
        // todo
        return new Collection();
    }

    public function htmlResponse(Collection $items): Response
    {
        return Inertia::render('Groups/AllItems', [
            'items' => $items, // todo convert to resources
        ]);
    }
}
