<?php

namespace App\Http\Resources\Vaults;

use App\Http\Resources\Categories\FieldResource;
use App\Models\VaultRecordValue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Session;

/** @mixin VaultRecordValue */
class VaultRecordValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $includeValues = filter_var(
            $request->get('include_values', Session::get('include_values')),
            FILTER_VALIDATE_BOOL,
        );

        return [
            'id' => $this->id,

            'name' => $this->name,

            'value' => $this->when(
                $this->hasAppended('value') || $includeValues,
                ! is_null($this->value) ? base64_encode($this->value) : null
            ),
            $this->mergeWhen($this->hasAttribute('invalid'), [
                'invalid' => true,
            ]),
            'field' => FieldResource::make($this->whenLoaded('field')),

            'created_at' => datetime($this->created_at),
            'updated_at' => datetime($this->updated_at),
        ];
    }
}
