<?php

namespace App\Actions\Groups;

use App\Http\Resources\Vaults\VaultRecordResource;
use App\Models\Field;
use App\Models\VaultRecord;
use App\Traits\Resources;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowFavourites
{
    use AsAction;
    use Resources;

    public function handle(): LengthAwarePaginator
    {
        return VaultRecord::query()
            ->whereRelation('vault', 'user_id', '=', Auth::id())
            ->where('is_favourite', '=', true)
            ->with(['values.field', 'vault', 'category'])
            ->paginate();
    }

    public function asController(ActionRequest $request): Response|LengthAwarePaginator
    {
        if ($request->wantsJson()) {
            return $this->handle();
        }

        return $this->htmlResponse();
    }

    public function htmlResponse(): Response
    {
        return Inertia::render('Groups/Favourites', [
            'fields' => Field::all(),
        ]);
    }

    public function jsonResponse(LengthAwarePaginator $items): JsonResponse
    {
        return $this->sendResource(
            $items,
            VaultRecordResource::class
        );
    }
}
