<?php

namespace App\Actions\Vaults\VaultRecords\VaultRecordValues;

use App\Http\Resources\Vaults\VaultRecordValueResource;
use App\Models\Vault;
use App\Models\VaultRecord;
use App\Models\VaultRecordValue;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreVaultRecordValue
{
    use AsAction;
    use Resources;

    public function rules(): array
    {
        return [
            'field_id' => 'required|exists:fields,id',
            'name' => 'sometimes|nullable|max:50',
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        /** @var Vault $vault */
        $vault = $request->route('vault');
        /** @var VaultRecord $record */
        $record = $request->route('record');

        return $request->user()->can('view', $vault) && $vault->isUnlockable
            && $request->user()->can('update', $record);
    }

    public function handle(Vault $vault, VaultRecord $record, ActionRequest $request): VaultRecordValue
    {
        return $record->values()->create($request->validated())->load('field');
    }

    public function jsonResponse(VaultRecordValue $value): JsonResponse
    {
       return $this->sendResource(
           $value,
           VaultRecordValueResource::class
       );
    }
}
