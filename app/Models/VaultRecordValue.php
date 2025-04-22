<?php

namespace App\Models;

use App\Exceptions\InvalidRecordValueException;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $vault_record_id
 * @property int $field_id
 * @property string|null $name
 * @property string $uid
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property VaultRecord $record
 * @property Field $field
 */
class VaultRecordValue extends Model
{
    protected $guarded = [
        'vault_record_id',
    ];

    protected $hidden = [
        'value',
    ];

    protected static function boot()
    {
        parent::boot();
        parent::creating(function (self $value) {
            if (empty($value->uid)) {
                $value->uid = Str::uuid()->toString();
                $value->value = null; // This still encrypts the value
            }
        });
    }

    // Relationships
    public function record(): BelongsTo
    {
        return $this->belongsTo(VaultRecord::class, 'vault_record_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    // Attributes
    public function value(): Attribute
    {
        return Attribute::make(
            get: function () {
                $encryptor = $this->record->vault->getEncrypter();
                try {
                    $data = $encryptor->decrypt($this->getRawOriginal('value'));
                } catch (DecryptException) {
                    $this->setAttribute('invalid', true);

                    return null;
                }

                $uid = $data['uid'] ?? null;

                if ($uid !== $this->uid) {
                    $this->setAttribute('invalid', true);

                    return null;
                }

                return $data['value'] ?? null;
            },
            set: function ($value) {
                $encryptor = $this->record->vault->getEncrypter();
                try {
                    $data = $this->getRawOriginal('value')
                        ? $encryptor->decrypt($this->getRawOriginal('value'))
                        : [
                            'uid' => $this->uid,
                            'originator' => Auth::id(),
                            'value' => null,
                        ];
                } catch (DecryptException) {
                    throw new InvalidRecordValueException;
                }

                $uid = $data['uid'] ?? null;

                if ($uid !== $this->uid) {
                    throw new InvalidRecordValueException;
                }

                $data['value'] = $value;

                try {
                    $result = $encryptor->encrypt($data);
                } catch (EncryptException) {
                    throw new InvalidRecordValueException;
                }

                return $result;
            },
        );
    }
}
