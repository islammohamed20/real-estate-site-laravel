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
        'balance_after',
        'notes',
        'is_custom',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
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
}
