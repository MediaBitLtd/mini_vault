<?php

namespace App\Actions\Fields;

use App\Http\Resources\Categories\FieldResource;
use App\Models\Field;
use App\Traits\Resources;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class GetFields
{
    use AsAction;
    use Resources;

    public function handle(): Collection
    {
        return Field::query()->get();
    }

    public function jsonResponse(Collection $categories): JsonResponse
    {
        return $this->sendResource(
            $categories,
            FieldResource::class
        );
    }
}
