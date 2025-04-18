<?php

namespace App\Filament\Resources\OAuthClientResource\Pages;

use App\Filament\Resources\OAuthClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOAuthClients extends ListRecords
{
    protected static string $resource = OAuthClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
