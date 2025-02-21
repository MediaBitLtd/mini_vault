<?php

namespace App\Actions\Vaults\VaultRecords;

use App\Exceptions\InvalidRecordValueException;
use App\Http\Resources\Vaults\VaultRecordValueResource;
use App\Models\Vault;
use App\Models\VaultRecord;
use App\Models\VaultRecordValue;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateVaultRecordValue
{
    use AsAction;
    use Resources;

    public function rules(): array
    {
        return [
            'value' => 'required|string',
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

    public function handle(
        Vault $vault,
        VaultRecord $record,
        VaultRecordValue $value,
        ActionRequest $request
    ): VaultRecordValue {
        $value->value = $request->get('value');
        $value->save();

        return $value->load('field');
    }

    public function asController(
        Vault $vault,
        VaultRecord $record,
        VaultRecordValue $value,
        ActionRequest $request
    ): VaultRecordValue {
        try {
            $value = $this->handle($vault, $record, $value, $request);
        } catch (InvalidRecordValueException) {
            throw new BadRequestHttpException('Cound\'t save record value');
        }

        return $value;
    }

    public function jsonResponse(VaultRecordValue $value): JsonResponse
    {
        return $this->sendResource(
            $value,
            VaultRecordValueResource::class
        );
    }
}
