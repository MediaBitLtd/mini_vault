<?php

declare(strict_types=1);

namespace App\Http\Resources\BaseResources;

use Illuminate\Http\Resources\Json\JsonResource;

class GenericModelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'created_at' => datetime($this->created_at),
            'updated_at' => datetime($this->updated_at),
            $this->mergeWhen(isset($this->deleted_at), ['deleted_at' => datetime($this->deleted_at)]),
        ];
    }
}
