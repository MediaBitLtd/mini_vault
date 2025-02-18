<?php

namespace App\Http\Resources\Vaults;

use App\Http\Resources\Fields\FieldResource;
use App\Models\VaultRecordValue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin VaultRecordValue */
class VaultRecordValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'value' => $this->whenAppended('value'),
            $this->mergeWhen($this->hasAttribute('invalid'), [
                'invalid' => true,
            ]),
            'field' => FieldResource::make($this->whenLoaded('field')),

            'created_at' => datetime($this->created_at),
            'updated_at' => datetime($this->updated_at),
        ];
    }
}
