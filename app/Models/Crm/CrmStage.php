<?php

declare(strict_types=1);

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmStage extends Model
{
    use HasFactory;

    protected $table = 'crm_stages';

    protected $fillable = [
        'pipeline_id',
        'name',
        'color',
        'probability',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'probability' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(CrmPipeline::class, 'pipeline_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(CrmDeal::class, 'stage_id');
    }
}
