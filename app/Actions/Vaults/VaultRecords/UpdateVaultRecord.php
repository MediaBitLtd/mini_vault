<?php

namespace App\Actions\Vaults\VaultRecords;

use App\Http\Resources\Vaults\VaultRecordResource;
use App\Models\Vault;
use App\Models\VaultRecord;
use App\Models\VaultRecordValue;
use App\Traits\Resources;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\JsonResponse;

class UpdateVaultRecord
{
    use AsAction;
    use Resources;

    public function rules(): array
    {
        return [
            'name' => 'sometimes|nullable|string|max:255',
            'values' => 'sometimes|array',
            'values.*.id' => 'required|exists:vault_record_values,id',
            'values.*.value' => 'sometimes|nullable|string',
            'vault_id' => 'sometimes|exists:vaults,id',
            'is_favourite' => 'sometimes|boolean',
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

    public function handle(Vault $vault, VaultRecord $record, ActionRequest $request): VaultRecord
    {
        return DB::transaction(function () use($record, $request) : VaultRecord {
            $input = $request->validated();
            $newVaultId = $input['vault_id'] ?? null;

            unset($input['vault_id']);

            if (isset($input['values'])) {
                $record->load('values');

                foreach ($input['values'] as $value) {
                    /** @var VaultRecordValue $valueModel */
                    $valueModel = $record->values
                        ->where('id', '=', $value['id'])
                        ->firstOrFail();
                    $valueModel->value = $value['value'] ?? false
                        ? base64_decode($value['value'])
                        : null;
                    $valueModel->save();
                }

                $toDelete = $record->values->whereNotIn('id', collect($input['values'])->pluck('id'));
                foreach ($toDelete as $value) {
                    $value->delete();
                }

                unset($input['values']);
            }

            $record->update($input);

            if ($newVaultId) {
                $record = MoveRecordToVault::make()->handle($record, $newVaultId);
            }

            return $record;
        });
    }

    public function jsonResponse(VaultRecord $record): JsonResponse
    {
        return $this->sendResource(
            $record->load('values.field'),
            VaultRecordResource::class
        );
    }
}
