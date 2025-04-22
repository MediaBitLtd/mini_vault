<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
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
 * @property bool $onboard
 * @property bool $is_admin
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Vault[]|Collection<Vault> $vaults
 * @property Authorization[]|Collection<Authorization> $authorizations
 *
 * @property-read string $fullName
 */
class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;

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
        'onboard' => 'boolean',
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

    public function getFilamentName(): string
    {
        return $this->first_name;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
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
            get: fn () => "$this->first_name $this->last_name",
        );
    }
}
