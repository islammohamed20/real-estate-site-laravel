<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'images',
        'cover_image_path',
        'location',
        'city',
        'country',
        'map_lat',
        'map_lng',
        'status',
        'featured',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'map_lat' => 'decimal:7',
            'map_lng' => 'decimal:7',
            'featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function phases(): HasMany
    {
        return $this->hasMany(Phase::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class)->orderBy('sort_order');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
