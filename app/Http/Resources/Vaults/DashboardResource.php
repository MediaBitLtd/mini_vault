<?php

namespace App\Http\Resources\Vaults;

use App\Http\Resources\Categories\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'authenticator' => VaultRecordResource::collection($this->get('authenticator')),
            'favourites' => VaultRecordResource::collection($this->get('favourites')),
            'vaults' => VaultResource::collection($this->get('vaults')),
            'categories' => CategoryResource::collection($this->get('categories')),
        ];
    }
}
