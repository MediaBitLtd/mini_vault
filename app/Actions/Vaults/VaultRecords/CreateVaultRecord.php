<?php

namespace App\Actions\Vaults\VaultRecords;

use App\Http\Resources\Vaults\VaultRecordResource;
use App\Models\Vault;
use App\Models\VaultRecord;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateVaultRecord
{
    use AsAction;
    use Resources;

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'sometimes|nullable|string|max:255',
            'is_favourite' => 'sometimes|boolean',
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        /** @var Vault $vault */
        $vault = $request->route('vault');

        return $request->user()->can('view', $vault) && $vault->isUnlockable &&
            $request->user()->can('create', VaultRecord::class);
    }

    public function handle(Vault $vault, ActionRequest $request): VaultRecord
    {
        return DB::transaction(fn(): VaultRecord => $vault
            ->records()
            ->create($request->validated())
            ->refresh()
            ->load('values.field')
        );
    }

    public function jsonResponse(VaultRecord $record): JsonResponse
    {
       return $this->sendResource(
           $record,
           VaultRecordResource::class
       );
    }
}
