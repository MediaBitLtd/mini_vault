<?php

namespace App\Actions\Groups;

use App\Enums\FieldType;
use App\Models\VaultRecord;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowAuthenticatorRecords
{
    use AsAction;

    public function handle(): Collection
    {
        return VaultRecord::query()
            ->whereHas('values.field', static fn ($q) => $q
                ->where('type', '=', FieldType::TWO_FA->value)
            )
            ->with([
                'vault',
                'values' => static fn($q) => $q
                    ->with('field')
                    ->whereRelation('field', 'type', '=', FieldType::TWO_FA->value)
                    ->orWhereRelation('field', 'slug', '=', 'appname'),
                'category',
            ])
            ->get();
    }
}
