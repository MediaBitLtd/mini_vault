<?php

namespace App\Actions\Vaults;

use App\Http\Resources\Vaults\VaultResource;
use App\Models\User;
use App\Traits\Resources;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class GetVaults
{
    use AsAction;
    use Resources;

    public function handle(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->vaults;
    }

    public function jsonResponse(Collection $vaults): JsonResponse
    {
        return $this->sendResource(
            $vaults,
            VaultResource::class
        );
    }
}
