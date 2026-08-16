<?php

declare(strict_types=1);

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmPipeline extends Model
{
    use HasFactory;

    protected $table = 'crm_pipelines';

    protected $fillable = [
        'name',
        'slug',
        'color',
        'is_default',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function stages(): HasMany
    {
        return $this->hasMany(CrmStage::class, 'pipeline_id')->orderBy('sort_order');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(CrmDeal::class, 'pipeline_id');
    }
}
