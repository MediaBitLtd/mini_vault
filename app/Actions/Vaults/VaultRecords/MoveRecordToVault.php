<?php

namespace App\Actions\Vaults\VaultRecords;

use App\Models\Vault;
use App\Models\VaultRecord;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class MoveRecordToVault
{
    use AsAction;

    public function handle(VaultRecord $record, Vault|int $vault): VaultRecord
    {
        if (!($vault instanceof Vault)) {
            $vault = Vault::query()->findOrFail($vault);
        }

        if ($vault->user_id !== $record->vault->user_id || $vault->user_id !== Auth::id()) {
            throw new AuthorizationException;
        }

        $values = [];
        $encryptor = $record->vault->getEncrypter();
        foreach ($record->values as $value) {
            $values[$value->id] = $encryptor->decrypt($value->getRawOriginal('value'));
        }

        $record->vault()->associate($vault);
        $record->save();
        $record->refresh();

        $encryptor = $vault->getEncrypter();
        foreach ($record->values as $value) {
            DB::table('vault_record_values')
                ->where('id', '=', $value->id)
                ->update([
                    'value' => $encryptor->encrypt($values[$value->id]),
                    'updated_at' => now(),
                ]);
        }

        return $record->load('values');
    }
}
