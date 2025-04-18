<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $authn_id
 * @property string $credential_id
 * @property string $public_key
 * @property int $counter
 *
 * @property User $user
 */
class Authorization extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'user_id',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Attributes
    public function public_key(): Attribute
    {
        return Attribute::make(
            get: fn() => decrypt($this->getRawOriginal('public_key')),
            set: fn($val) => encrypt($val),
        );
    }
}
