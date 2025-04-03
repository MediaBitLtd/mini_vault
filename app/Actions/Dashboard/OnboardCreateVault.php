<?php

namespace App\Actions\Dashboard;

use App\Actions\Vaults\StoreVault;
use App\Exceptions\VaultAlreadySignedException;
use App\Http\Resources\Vaults\VaultResource;
use App\Models\Vault;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class OnboardCreateVault
{
    use AsAction;
    use Resources;

    public function rules(): array
    {
        return [
            'name' => 'sometimes|nullable|string|max:30',
        ];
    }

    /**
     * @throws VaultAlreadySignedException
     */
    public function handle(?string $name = null): Vault
    {
        if (is_null($name)) {
            $name = 'My Vault';
        }

        return StoreVault::make()->handle($name);
    }

    public function asController(ActionRequest $request): Vault
    {
        try {
            return $this->handle($request->validated('name'));
        } catch (VaultAlreadySignedException) {
            throw new BadRequestException;
        }
    }

    public function jsonResponse(Vault $vault): JsonResponse
    {
        return $this->sendResource(
            $vault,
            VaultResource::class
        );
    }
}
