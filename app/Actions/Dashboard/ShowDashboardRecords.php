<?php

namespace App\Actions\Dashboard;

use App\Actions\Fields\GetCategories;
use App\Actions\Groups\ShowAuthenticatorRecords;
use App\Actions\Groups\ShowFavourites;
use App\Actions\Vaults\GetVaults;
use App\Http\Resources\Vaults\DashboardResource;
use App\Traits\Resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowDashboardRecords
{
    use AsAction;
    use Resources;

    public function handle(): Collection
    {
        Session::flash('include_values');
        JsonResource::withoutWrapping();

        return collect([
            'authenticator' => ShowAuthenticatorRecords::make()->handle(),
            'favourites' => ShowFavourites::make()->handle(),
            'vaults' => GetVaults::make()->handle(),
            'categories' => GetCategories::make()->handle(),
        ]);
    }

    public function jsonResponse(Collection $data): JsonResponse
    {
        return new JsonResponse(DashboardResource::make($data));
    }
}
