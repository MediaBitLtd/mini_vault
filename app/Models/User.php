<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string $key
 * @property string|null $biometric_key
 * @property string $timezone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Vault[]|Collection<Vault> $vaults
 * @property Authorization[]|Collection<Authorization> $authorizations
 *
 * @property-read string $fullName
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'timezone',
    ];

    protected $hidden = [
        'password',
        'key',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'key' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function (self $user) {
            $user->key = Hash::make(Str::random(40));
        });
    }

    // Methods
    public function getPKey(string $password): string
    {
        return hash('sha256', "$password:$this->key");
    }

    // Relationships
    public function vaults(): HasMany
    {
        return $this->hasMany(Vault::class);
    }

    public function authorizations(): HasMany
    {
        return $this->hasMany(Authorization::class);
    }

    // Attributes
    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => "$this->first_name $this->last_name",
        );
    }
}
