<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VaultRecordValue;

class VaultRecordValuePolicy
{
    public function view(User $user, VaultRecordValue $value): bool
    {
        return $user->id === $value->record->vault->user_id;
    }

    public function update(User $user, VaultRecordValue $value): bool
    {
        return $user->id === $value->record->vault->user_id;
    }

    public function delete(User $user, VaultRecordValue $value): bool
    {
        return $user->id === $value->record->vault->user_id;
    }
}
