<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period',
        'score',
        'grade',
        'metrics',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'metrics' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
