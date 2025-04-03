<?php

declare(strict_types=1);

namespace App\Http\Resources\BaseResources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GenericCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param array|mixed $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'items' => $this->collection,
        ];
    }
}
