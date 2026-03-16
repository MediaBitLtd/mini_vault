<?php

namespace App\Filament\Resources\OAuthClientResource\Pages;

use App\Filament\Resources\OAuthClientResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateOAuthClient extends CreateRecord
{
    protected static string $resource = OAuthClientResource::class;

    protected ?string $plaintextSecret = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id'] = Str::uuid()->toString();
        $data['personal_access_client'] = $data['personal_access_client'] ?? 0;
        $data['password_client'] = $data['password_client'] ?? 0;
        $data['revoked'] = $data['revoked'] ?? 0;
        $data['requires_user_key'] = $data['requires_user_key'] ?? 0;

        $this->plaintextSecret = Str::random(40);
        $data['secret'] = $this->plaintextSecret;

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Client secret')
            ->body("Copy this secret now — it won't be shown again: {$this->plaintextSecret}")
            ->persistent()
            ->success()
            ->send();
    }
}
