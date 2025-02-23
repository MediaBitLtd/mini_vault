<?php

namespace App\Models;

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
    protected $guarded = [
        'id',
        'user_id',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
