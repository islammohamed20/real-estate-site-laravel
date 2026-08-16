<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstallmentPlan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'installment_template_id',
        'customer_id',
        'lead_id',
        'project_id',
        'building_id',
        'floor_id',
        'unit_id',
        'offer_id',
        'created_by',
        'name',
        'status',
        'currency_code',
        'base_price',
        'discount_amount',
        'final_price',
        'maintenance_deposit',
        'down_payment',
        'remaining_amount',
        'installment_amount',
        'installment_count',
        'installment_type',
        'schedule_json',
        'starts_at',
        'last_installment_adjustment',
        'saved_from_calculator',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_price' => 'decimal:2',
            'maintenance_deposit' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'installment_amount' => 'decimal:2',
            'last_installment_adjustment' => 'decimal:2',
            'schedule_json' => 'array',
            'starts_at' => 'date',
            'saved_from_calculator' => 'boolean',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InstallmentTemplate::class, 'installment_template_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InstallmentPlanItem::class);
    }
}
