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
            'id' => $this->id,
            'name' => $this->name,
            'is_unlockable' => $this->isUnlockable,
            'created_at' => datetime($this->created_at),
            'updated_at' => datetime($this->updated_at),
        ];
    }
}
