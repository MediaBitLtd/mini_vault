<?php

namespace App\Traits;

use App\Http\Resources\BaseResources\GenericCollectionResource;
use App\Http\Resources\BaseResources\GenericModelResource;
use App\Http\Resources\BaseResources\PaginatedCollectionResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Enumerable;
use JsonSerializable;

trait Resources
{
    /**
     * Generates a single resource
     *
     * @param JsonSerializable $data
     * @param string $resource_class
     * @return JsonResource
     */
    protected function makeSingleResource(
        JsonSerializable $data,
        string $resource_class
    ): JsonResource {
        /** @var JsonResource $resource_class */
        return $resource_class::make($data);
    }

    /**
     * Generates a paginated resource
     *
     * @param JsonSerializable $data
     * @param string $resource_class
     * @return JsonResource
     */
    protected function makePaginatedResource(
        JsonSerializable $data,
        string $resource_class
    ): JsonResource {
        // -- This is required to bake the correct resource -- //
        /** @var JsonResource $resource_class */
        $resource_class::collection($data);

        return new PaginatedCollectionResource($data);
    }

    /**
     * Generates a collection resource
     *
     * @param JsonSerializable $data
     * @param string $resource_class
     * @return JsonResource
     */
    protected function makeCollectionResource(
        JsonSerializable $data,
        string $resource_class
    ): JsonResource {
        // this is the best method :/
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $data,
            $data->count(),
            $data->count() > 0 ? $data->count() : 15,
            1
        );

        // -- This is required to bake the correct resource -- //
        /** @var JsonResource $resource_class */
        $resource_class::collection($paginator);

        return new GenericCollectionResource($paginator);
    }

    /**
     * Checks that every argument passed in params is numeric
     *
     * @param array $params
     * @return void
     */
    protected function validateRouteParams(int|string ...$params): void
    {
        collect($params)->each(function ($val) {
            if (!is_numeric($val))
                throw new ModelNotFoundException;
        });
    }

    /**
     * Generates a resource based on input and sends as JsonResponse
     * By default **GenericModelResource** will be used
     *
     * @param JsonSerializable $data
     * @param string $resource_class
     * @return JsonResponse
     */
    public function sendResource(
        JsonSerializable $data,
        string $resource_class = GenericModelResource::class
    ): JsonResponse {
        // Generic collection
        if ($data instanceof Enumerable) {
            return new JsonResponse(
                $this->makeCollectionResource($data, $resource_class)
            );
        }

        // Paginatable collection
        if ($data instanceof LengthAwarePaginator) {
            return new JsonResponse(
                $this->makePaginatedResource($data, $resource_class)
            );
        }

        // Simple resource
        return new JsonResponse(
            $this->makeSingleResource($data, $resource_class)
        );
    }

    /**
     * Sends an empty successful response
     *
     * @return JsonResponse
     */
    public function sendSuccessfulResponse(): JsonResponse
    {
        return new JsonResponse([], 204);
    }
}
