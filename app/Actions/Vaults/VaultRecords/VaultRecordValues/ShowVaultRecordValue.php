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

class ShowVaultRecordValue
{
    use AsAction;
    use Resources;

    public function authorize(ActionRequest $request): bool
    {
        /** @var Vault $vault */
        $vault = $request->route('vault');
        /** @var VaultRecord $record */
        $record = $request->route('record');
        /** @var VaultRecordValue $value */
        $value = $request->route('value');

        return $request->user()->can('view', $vault) && $vault->isUnlockable
            && $request->user()->can('view', $record)
            && $request->user()->can('view', $value);
    }

    public function handle(Vault $vault, VaultRecord $record, VaultRecordValue $value): VaultRecordValue
    {
        return $value->load('field')->append('value');
    }

    public function jsonResponse(VaultRecordValue $value): JsonResponse
    {
        return $this->sendResource(
            $value,
            VaultRecordValueResource::class
        );
    }
}
