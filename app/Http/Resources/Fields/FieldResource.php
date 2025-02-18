<?php

namespace App\Http\Resources\Fields;

use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Field */
class FieldResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'label' => $this->label,
            'type' => $this->type,
            'created_at' => datetime($this->created_at),
            'updated_at' => datetime($this->updated_at),
        ];
    }
}
