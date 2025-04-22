<?php

namespace App\Actions\Vaults\VaultRecords\VaultRecordTags;

use App\Http\Resources\Vaults\VaultRecordTagResource;
use App\Models\Vault;
use App\Models\VaultRecord;
use App\Models\VaultRecordTag;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreVaultRecordTag
{
    use AsAction;
    use Resources;

    public function rules(): array
    {
        return [
            'name' => 'sometimes|nullable|max:255',
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        /** @var Vault $vault */
        $vault = $request->route('vault');
        /** @var VaultRecord $record */
        $record = $request->route('record');

        return $request->user()->can('view', $vault) && $request->user()->can('update', $record);
    }

    public function handle(Vault $vault, VaultRecord $record, ActionRequest $request): VaultRecordTag
    {
        return $record->tags()->create($request->validated());
    }

    public function jsonResponse(VaultRecordTag $tag): JsonResponse
    {
        return $this->sendResource(
            $tag,
            VaultRecordTagResource::class
        );
    }
}
