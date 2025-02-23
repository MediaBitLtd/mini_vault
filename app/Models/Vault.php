<?php

namespace App\Models;

use App\Exceptions\InvalidVaultSignatureException;
use App\Exceptions\VaultAlreadySignedException;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $key
 * @property string|null $signature
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property VaultRecord[]|Collection<VaultRecord> $records
 *
 * @property-read bool $isUnlockable
 */
class Vault extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'user_id',
    ];

    protected $hidden = [
        'key',
        'signature',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $user) {
            $user->key = Hash::make(Str::random(40));
        });
    }

    // Methods
    public function getVKey(): string
    {
        $pKey = blink()->get('pkey', '__invalid');
        return hash('md5', "$pKey:$this->key");
    }

    public function getEncrypter(): Encrypter
    {
        return new Encrypter($this->getVKey(), Config::get('app.cipher'));
    }

    /**
     * @throws VaultAlreadySignedException
     */
    public function sign(bool $force = false): void
    {
        if ($this->signature && !$force) {
            throw new VaultAlreadySignedException;
        }

        $this->signature = $this->getEncrypter()->encrypt([
            'id' => $this->id,
            'key' => $this->key,
            'uid' => $this->user_id,
        ]);
    }

    /**
     * @throws InvalidVaultSignatureException
     */
    public function validateVKey(): bool
    {
        if (!$this->signature) {
            throw new InvalidVaultSignatureException;
        }

        try {
            $obj = $this->getEncrypter()->decrypt($this->signature);
        } catch (DecryptException) {
            return false;
        }

        return $this->key === $obj['key'] ?? null &&
            $this->id === $obj['id'] ?? null &&
            $this->user_id = $obj['uid'] ?? null;
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(VaultRecord::class);
    }

    // Attributes
    public function isUnlockable(): Attribute
    {
        return Attribute::make(
            get: function () {
                try {
                    $valid = $this->validateVKey();
                } catch (InvalidVaultSignatureException) {
                    return false;
                }

                return $valid;
            },
        );
    }
}
