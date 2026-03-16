<?php

namespace App\Filament\Resources\OAuthClientResource\Pages;

use App\Filament\Resources\OAuthClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateOAuthClient extends CreateRecord
{
    protected static string $resource = OAuthClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id'] = Str::uuid()->toString();
        $data['personal_access_client'] = $data['personal_access_client'] ?? 0;
        $data['password_client'] = $data['password_client'] ?? 0;
        $data['revoked'] = $data['revoked'] ?? 0;
        $data['requires_user_key'] = $data['requires_user_key'] ?? 0;

        $data['secret'] = Str::random(40);

        return $data;
    }
}
