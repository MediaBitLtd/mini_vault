<?php

namespace App\Actions\Vaults\VaultRecords;

use App\Http\Resources\Vaults\VaultRecordResource;
use App\Models\Vault;
use App\Traits\Resources;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetVaultRecords
{
    use AsAction;
    use Resources;

    public function rules(): array
    {
        return [
            'q' => 'sometimes|string',
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        /** @var Vault $vault */
        $vault = $request->route('vault');
        return $request->user()->can('view', $vault) && $vault->isUnlockable;
    }

    public function handle(Vault $vault, ActionRequest $request): LengthAwarePaginator
    {
        return $vault->records()
            ->when($request->has('q'), fn($q) => $q
                ->where('name', 'like', "%{$request->get('q')}%")
            )
            ->with('values.field')
            ->paginate();
    }

    public function jsonResponse(LengthAwarePaginator $vaults): JsonResponse
    {
       return $this->sendResource(
           $vaults,
           VaultRecordResource::class
       );
    }
}
