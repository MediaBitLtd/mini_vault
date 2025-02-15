<?php

namespace App\Http\Resources\Vaults;

use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Vault */
class VaultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'is_unlocked' => $this->isUnlocked,
        ];
    }
}
