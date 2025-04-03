<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Models\VaultRecordValue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class MigrateVaults
{
    use AsAction;

    public function handle(User $user, string $password): void
    {
        $user->load('vaults.records.values');

        $pkey = blink()->get('pkey');

        $user->key = Hash::make(Str::random(40));
        $user->password = Hash::make($password);
        $user->save();

        $newPKey = $user->getPKey($password);

        foreach ($user->vaults as $vault) {
            blink()->put('pkey', $pkey);
            $encrypter = $vault->getEncrypter();

            blink()->put('pkey', $newPKey);
            $newEncrypter = $vault->getEncrypter();

            /** @var VaultRecordValue $value */
            foreach ($vault->records->pluck('values')->flatten() as $value) {
                $valueData = $encrypter->decrypt($value->getRawOriginal('value'));
                $value->setRawAttributes([
                    'value' => $newEncrypter->encrypt($valueData),
                ]);
                $value->save();
            }

            $vault->sign(true);
            $vault->save();
        }
    }
}
