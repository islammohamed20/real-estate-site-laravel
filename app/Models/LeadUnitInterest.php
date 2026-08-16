<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadUnitInterest extends Model
{
    use HasFactory;

    protected $table = 'lead_unit_interests';

    protected $fillable = [
        'lead_id',
        'customer_id',
        'unit_id',
        'priority',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'bedrooms' => 'integer',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
