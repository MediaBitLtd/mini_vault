<?php

namespace App\Actions\Vaults;

use App\Http\Resources\Vaults\VaultResource;
use App\Models\Vault;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowVault
{
    use AsAction;
    use Resources;

    public function handle(Vault $vault): Vault
    {
        return $vault;
    }

    public function htmlResponse(Vault $vault): Response|RedirectResponse
    {
        if (!$vault->isUnlockable) {
            return redirect()->route('dashboard')->with('message', 'Invalid vault');
        }

        return Inertia::render('Vaults/Show', [
            'vault' => $vault,
        ]);
    }

    public function jsonResponse(Vault $vault): JsonResponse
    {
        return $this->sendResource(
            $vault,
            VaultResource::class
        );
    }
}
