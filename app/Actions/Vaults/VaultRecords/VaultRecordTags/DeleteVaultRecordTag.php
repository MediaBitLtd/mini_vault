<?php

namespace App\Actions\Vaults\VaultRecords\VaultRecordTags;

use App\Models\Vault;
use App\Models\VaultRecord;
use App\Models\VaultRecordTag;
use App\Traits\Resources;
use Illuminate\Auth\Access\AuthorizationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteVaultRecordTag
{
    use AsAction;
    use Resources;

    public function authorize(ActionRequest $request): bool
    {
        /** @var Vault $vault */
        $vault = $request->route('vault');
        /** @var VaultRecord $record */
        $record = $request->route('record');

        return $request->user()->can('view', $vault) && $request->user()->can('update', $record);
    }

    public function handle(Vault $vault, VaultRecord $record, VaultRecordTag|string $tag): void
    {
        if (! ($tag instanceof VaultRecordTag)) {
            $tag = $record->tags()->where('name', '=', $tag)->firstOrFail();
        } elseif ($tag->vault_record_id !== $record->id) {
            throw new AuthorizationException;
        }

        $tag->delete();
    }

    public function jsonResponse(): JsonResponse
    {
        return $this->sendSuccessfulResponse();
    }
}
