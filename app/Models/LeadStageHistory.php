<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeadStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadStageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'user_id',
        'stage_from',
        'stage_to',
        'notes',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'stage_from' => LeadStage::class,
            'stage_to' => LeadStage::class,
            'changed_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
