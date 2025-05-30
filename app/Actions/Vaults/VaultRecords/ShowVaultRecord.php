<?php

namespace App\Actions\Vaults\VaultRecords;

use App\Http\Resources\Vaults\VaultRecordResource;
use App\Models\Vault;
use App\Models\VaultRecord;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowVaultRecord
{
    use AsAction;
    use Resources;

    public function authorize(ActionRequest $request): bool
    {
        /** @var Vault $vault */
        $vault = $request->route('vault');
        /** @var VaultRecord $record */
        $record = $request->route('record');

        return $request->user()->can('view', $vault) && $vault->isUnlockable
            && $request->user()->can('view', $record);
    }

    public function handle(Vault $vault, VaultRecord $record, ActionRequest $request): VaultRecord
    {
        return $record->load('values.field');
    }

    public function jsonResponse(VaultRecord $record): JsonResponse
    {
        return $this->sendResource(
            $record,
            VaultRecordResource::class
        );
    }
}
