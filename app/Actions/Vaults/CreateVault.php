<?php

namespace App\Actions\Vaults;

use App\Exceptions\VaultAlreadySigned;
use App\Http\Resources\Vaults\VaultResource;
use App\Models\User;
use App\Models\Vault;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateVault
{
    use AsAction;
    use Resources;

    public function roles(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    /**
     * @throws VaultAlreadySigned
     */
    public function handle(ActionRequest $request): Vault
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var Vault $vault */

        // TODO validated is not working???
        $vault = $user->vaults()->create([
            'name' => $request->get('name'),
        ]);
        $vault->sign();
        $vault->save();

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
