<?php

namespace App\Actions\Vaults;

use App\Http\Resources\Vaults\VaultResource;
use App\Models\Category;
use App\Models\Vault;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowVault
{
    use AsAction;
    use Resources;

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('view', $request->route('vault'));
    }

    public function handle(Vault $vault): Vault
    {
        return $vault;
    }

    public function htmlResponse(Vault $vault): Response|RedirectResponse
    {
        if (!$vault->isUnlockable) {
            return redirect()->route('dashboard')->with('message', 'Invalid vault');
        }

        $categories = Category::all();

        return Inertia::render('Vaults/VaultsShow', [
            'vault' => $vault,
            'categories' => $categories,
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
