<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallmentPlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_plan_id',
        'offer_id',
        'installment_number',
        'due_date',
        'amount',
        'paid_amount',
        'paid_at',
        'paid_by',
        'payment_method',
        'payment_notes',
        'balance_after',
        'notes',
        'is_custom',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'is_custom' => 'boolean',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(InstallmentPlan::class, 'installment_plan_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Check if this installment is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return (float) $this->paid_amount >= (float) $this->amount && (float) $this->amount > 0;
    }

    /**
     * Check if partially paid.
     */
    public function isPartiallyPaid(): bool
    {
        return (float) $this->paid_amount > 0 && ! $this->isFullyPaid();
    }

    /**
     * Remaining amount for this installment.
     */
    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    /**
     * Overdue if due_date < today and not fully paid.
     */
    public function isOverdue(): bool
    {
        return ! $this->isFullyPaid() && $this->due_date->isPast();
    }
}
