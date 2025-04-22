<?php

namespace App\Http\Resources\Vaults;

use App\Http\Resources\Categories\CategoryResource;
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

            'category' => CategoryResource::make($this->whenLoaded('category')),
            'values' => VaultRecordValueResource::collection($this->whenLoaded('values')),

            'vault' => VaultResource::make($this->whenLoaded('vault')),

            'tags' => $this->tags->map(static fn ($tag) => $tag->name),

            'created_at' => datetime($this->created_at),
            'updated_at' => datetime($this->updated_at),
        ];
    }
}
