<?php

declare(strict_types=1);

namespace App\Http\Resources\BaseResources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginatedCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'items' => $this->collection,
            'meta' => [
                'page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'last_page' => $this->lastPage(),
                'total' => $this->total(),
            ],
        ];
    }
}
