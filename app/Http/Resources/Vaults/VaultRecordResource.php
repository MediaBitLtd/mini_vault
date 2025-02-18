<?php

namespace App\Http\Resources\Vaults;

use App\Models\VaultRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin VaultRecord */
class VaultRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_favourite' => $this->is_favourite,

            'values' => VaultRecordValueResource::collection($this->whenLoaded('values')),

            'created_at' => datetime($this->created_at),
            'updated_at' => datetime($this->updated_at),
        ];
    }
}
