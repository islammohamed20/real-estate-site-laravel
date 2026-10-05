<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UnitStatus;
use App\Models\Traits\TracksDeletedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TracksDeletedBy {
        TracksDeletedBy::runSoftDelete insteadof SoftDeletes;
    }

    protected $fillable = [
        'project_id',
        'phase_id',
        'building_id',
        'floor_id',
        'unit_number',
        'unit_type',
        'images',
        'thumbnail',
        'floor_plan_path',
        'features',
        'map_lat',
        'map_lng',
        'bedrooms',
        'bathrooms',
        'area',
        'garden_area',
        'roof_area',
        'balcony_area',
        'terrace_count',
        'price_per_meter',
        'garden_price',
        'roof_price',
        'excellence_percent',
        'current_price',
        'delivery_date',
        'status',
        'featured',
        'hidden_from_website',
        'sort_order',
        'grid_row',
        'grid_col',
        'position_in_grid',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'features' => 'array',
            'map_lat' => 'decimal:7',
            'map_lng' => 'decimal:7',
            'area' => 'decimal:2',
            'garden_area' => 'decimal:2',
            'roof_area' => 'decimal:2',
            'balcony_area' => 'decimal:2',
            'terrace_count' => 'integer',
            'price_per_meter' => 'decimal:2',
            'garden_price' => 'decimal:2',
            'roof_price' => 'decimal:2',
            'excellence_percent' => 'decimal:2',
            'delivery_date' => 'date',
            'current_price' => 'decimal:2',
            'status' => UnitStatus::class,
            'featured' => 'boolean',
            'hidden_from_website' => 'boolean',
            'position_in_grid' => 'integer',
        ];
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('hidden_from_website', false)
            ->whereIn('status', [UnitStatus::Available->value, UnitStatus::Reserved->value, UnitStatus::Sold->value]);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function installmentPlans(): HasMany
    {
        return $this->hasMany(InstallmentPlan::class);
    }

    /**
     * Human-readable floor label for use in PDFs and views.
     * Returns "Ground Floor" or "Floor N" based on the linked floor's number.
     */
    public function getFloorLabelAttribute(): ?string
    {
        $floor = $this->floor;

        if (! $floor) {
            return null;
        }

        if ($floor->number === 0 || $floor->number === null) {
            return __('Ground Floor');
        }

        return __('Floor :number', ['number' => $floor->number]);
    }
}
