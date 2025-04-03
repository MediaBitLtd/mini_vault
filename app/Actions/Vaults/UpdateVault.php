<?php

namespace App\Actions\Vaults;

use App\Http\Resources\Vaults\VaultResource;
use App\Models\Vault;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateVault
{
    use AsAction;
    use Resources;

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2',
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('update', $request->route('vault'));
    }

    public function handle(Vault $vault, ActionRequest $request): Vault
    {
        $vault->update($request->validated());

        return $vault;
    }

    public function jsonResponse(Vault $vault): JsonResponse
    {
        return $this->sendResource(
            $vault,
            VaultResource::class
        );
    }
}
