<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $vault_id
 * @property int $category_id
 * @property string|null $name
 * @property bool $is_favourite
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property Vault $vault
 * @property Category $category
 * @property VaultRecordValue[]|Collection $values
 * @property VaultRecordTag[]|Collection $tags
 */
class VaultRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'vault_id',
    ];

    protected $casts = [
        'is_favourite' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        parent::created(function (self $record) {
            $valuesToInsert = collect();
            $encrypter = $record->vault->getEncrypter();
            $record->category->fields->each(fn (Field $field) => $valuesToInsert->push([
                'vault_record_id' => $record->id,
                'field_id' => $field->id,
                'uid' => $uid = Str::uuid()->toString(),
                'value' => $encrypter->encrypt([
                    'uid' => $uid,
                    'value' => null,
                    'originator' => Auth::id(),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            VaultRecordValue::query()->insert($valuesToInsert->toArray());
        });
    }

    // Relationships
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(VaultRecordValue::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(VaultRecordTag::class);
    }
}
