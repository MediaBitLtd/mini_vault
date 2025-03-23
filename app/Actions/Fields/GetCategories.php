<?php

namespace App\Actions\Fields;

use App\Http\Resources\Categories\CategoryResource;
use App\Models\Category;
use App\Traits\Resources;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GetCategories
{
    use AsAction;
    use Resources;

    public function handle(): Collection
    {
        return Category::query()->with('fields')->get();
    }

    public function jsonResponse(Collection $categories): JsonResponse
    {
        return $this->sendResource(
            $categories,
            CategoryResource::class
        );
    }
}
