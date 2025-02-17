<?php

namespace App\Actions\Vaults;

use App\Models\Vault;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteVault
{
    use AsAction;
    use Resources;

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('delete', $request->route('vault'));
    }

    public function handle(Vault $vault): void
    {
        $vault->delete();
    }

    public function htmlResponse(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function jsonResponse(): JsonResponse
    {
        return $this->sendSuccessfulResponse();
    }
}
