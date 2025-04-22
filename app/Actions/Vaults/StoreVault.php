<?php

namespace App\Actions\Vaults;

use App\Exceptions\VaultAlreadySignedException;
use App\Http\Resources\Vaults\VaultResource;
use App\Models\User;
use App\Models\Vault;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class StoreVault
{
    use AsAction;
    use Resources;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30',
        ];
    }

    /**
     * @throws VaultAlreadySignedException
     */
    public function handle(string $name): Vault
    {
        /** @var User $user */
        $user = Auth::user();

        /** @var Vault $vault */
        $vault = $user->vaults()->create([
            'name' => $name,
        ]);

        $vault->sign();
        $vault->save();

        return $vault;
    }

    public function withValidator(Validator $validator, ActionRequest $request): void
    {
        /** @var User $user */
        $user = Auth::user();

        $validator->after(function (Validator $validator) use ($user, $request) {
            if ($user->vaults()->where('name', '=', $request->get('name'))->exists()) {
                $validator->errors()->add('name', 'Vault name already in use.');
            }
        });
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
