<?php

namespace App\Models;

use App\Exceptions\InvalidVaultSignatureException;
use App\Exceptions\VaultAlreadySigned;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property bool $isUnlocked
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

    public function getEncrypter(): Encrypter
    {
        return new Encrypter($this->getVKey(), Config::get('app.cipher'));;
    }

    public function getVKey(): string
    {
        $pKey = blink()->get('pkey', '__invalid');
        return hash('md5', "$pKey:$this->key");
    }

    /**
     * @throws VaultAlreadySigned
     */
    public function sign(bool $force = false): void
    {
        if ($this->signature && !$force) {
            throw new VaultAlreadySigned;
        }

        $this->signature = $this->getEncrypter()->encrypt($this->key);
    }

    /**
     * @throws InvalidVaultSignatureException
     */
    public function validateVKey(): bool
    {
        if (!$this->signature) {
            throw new InvalidVaultSignatureException;
        }

        return $this->key === $this->getEncrypter()->decrypt($this->signature);
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Attributes
    public function isUnlocked(): Attribute
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
