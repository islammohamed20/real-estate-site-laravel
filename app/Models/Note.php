<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'noteable_type',
        'noteable_id',
        'user_id',
        'body',
        'type',
        'noted_at',
    ];

    protected function casts(): array
    {
        return [
            'noted_at' => 'datetime',
        ];
    }

    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
