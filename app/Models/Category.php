<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string|null $description
 * @property string $icon
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Field[]|Collection<Field> $fields
 */
class Category extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    // Relationships
    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class, 'category_field');
    }
}
