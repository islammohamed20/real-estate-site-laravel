<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmDealStageHistory extends Model
{
    use HasFactory;

    protected $table = 'crm_deal_stage_histories';

    protected $fillable = [
        'deal_id',
        'from_stage_id',
        'to_stage_id',
        'changed_by',
        'notes',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(CrmDeal::class, 'deal_id');
    }

    public function fromStage(): BelongsTo
    {
        return $this->belongsTo(CrmStage::class, 'from_stage_id');
    }

    public function toStage(): BelongsTo
    {
        return $this->belongsTo(CrmStage::class, 'to_stage_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
