<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VaultRecord;

class VaultRecordPolicy
{
    public function view(User $user, VaultRecord $vaultRecord): bool
    {
        return $user->id === $vaultRecord->vault->user_id;
    }

    public function update(User $user, VaultRecord $vaultRecord): bool
    {
        return $user->id === $vaultRecord->vault->user_id;
    }

    public function delete(User $user, VaultRecord $vaultRecord): bool
    {
        return $user->id === $vaultRecord->vault->user_id;
    }

    public function restore(User $user, VaultRecord $vaultRecord): bool
    {
        return $user->id === $vaultRecord->vault->user_id;
    }

    public function forceDelete(User $user, VaultRecord $vaultRecord): bool
    {
        return $user->id === $vaultRecord->vault->user_id;
    }
}
