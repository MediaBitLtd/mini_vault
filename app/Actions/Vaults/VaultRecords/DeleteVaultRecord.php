<?php

namespace App\Actions\Vaults\VaultRecords;

use App\Models\Vault;
use App\Models\VaultRecord;
use App\Traits\Resources;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteVaultRecord
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
            && $request->user()->can('forceDelete', $record);
    }

    public function handle(Vault $vault, VaultRecord $record): void
    {
        $record->forceDelete();
    }

    public function jsonResponse(): JsonResponse
    {
        return $this->sendSuccessfulResponse();
    }
}
