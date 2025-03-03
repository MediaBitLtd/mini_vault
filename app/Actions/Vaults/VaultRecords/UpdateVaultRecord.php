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
            'name' => 'sometimes|string|max:255',
            'values' => 'sometimes|array',
            'values.*.id' => 'required|exists:vault_record_values,id',
            'values.*.value' => 'required|nullable|string',
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

            if (isset($input['values'])) {
                $record->load('values');

                foreach ($input['values'] as $value) {
                    /** @var VaultRecordValue $valueModel */
                    $valueModel = $record->values
                        ->where('id', '=', $value['id'])
                        ->firstOrFail();
                    $valueModel->value = $value['value']
                        ? base64_decode($value['value'])
                        : null;
                    $valueModel->save();
                }

                unset($input['values']);
            }

            $record->update($input);

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
