<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InstallmentFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallmentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'down_payment_percent',
        'down_payment_amount',
        'installment_count',
        'installment_frequency',
        'maintenance_percent',
        'discount_percent',
        'first_installment_offset_months',
        'is_default',
        'is_active',
        'defaults_json',
    ];

    protected function casts(): array
    {
        return [
            'down_payment_percent' => 'decimal:2',
            'down_payment_amount' => 'decimal:2',
            'maintenance_percent' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'defaults_json' => 'array',
            'installment_frequency' => InstallmentFrequency::class,
        ];
    }

    public function plans(): HasMany
    {
        return $this->hasMany(InstallmentPlan::class);
    }

    public function defaults(): array
    {
        return array_merge([
            'excellence_percent' => 0,
            'down_payment_percent' => $this->down_payment_percent,
            'down_payment' => $this->down_payment_amount,
            'installment_count' => $this->installment_count,
            'installment_type' => $this->installment_frequency?->value,
            'maintenance_percent' => $this->maintenance_percent,
            'discount' => 0,
            'first_installment_date' => now()->addMonths((int) $this->first_installment_offset_months)->toDateString(),
        ], $this->defaults_json ?? []);
    }
}
