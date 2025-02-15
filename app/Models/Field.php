<?php

namespace App\Models;

use App\Enums\FieldType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $slug
 * @property string $label
 * @property FieldType $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Field extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'type' => FieldType::class,
    ];
}
