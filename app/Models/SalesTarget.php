<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sales_team_id',
        'period',
        'leads_target',
        'offers_target',
        'reservations_target',
        'deals_target',
        'deal_value_target',
    ];

    protected function casts(): array
    {
        return [
            'leads_target' => 'integer',
            'offers_target' => 'integer',
            'reservations_target' => 'integer',
            'deals_target' => 'integer',
            'deal_value_target' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(SalesTeam::class, 'sales_team_id');
    }
}
