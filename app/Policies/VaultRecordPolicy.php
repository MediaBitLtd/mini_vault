<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VaultRecord;

class VaultRecordPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VaultRecord $vaultRecord): bool
    {
        return $user->id === $vaultRecord->vault->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VaultRecord $vaultRecord): bool
    {
        return $user->id === $vaultRecord->vault->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VaultRecord $vaultRecord): bool
    {
        return $user->id === $vaultRecord->vault->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VaultRecord $vaultRecord): bool
    {
        return $user->id === $vaultRecord->vault->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VaultRecord $vaultRecord): bool
    {
        return $user->id === $vaultRecord->vault->user_id;
    }
}
