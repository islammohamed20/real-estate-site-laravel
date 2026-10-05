<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeminiUsageLog extends Model
{
    protected $fillable = [
        'phone',
        'push_name',
        'customer_message',
        'ai_reply',
        'prompt_tokens',
        'output_tokens',
        'thoughts_tokens',
        'total_tokens',
        'service_tier',
        'model',
        'latency_ms',
        'success',
        'error',
    ];

    protected $casts = [
        'prompt_tokens' => 'integer',
        'output_tokens' => 'integer',
        'thoughts_tokens' => 'integer',
        'total_tokens' => 'integer',
        'latency_ms' => 'integer',
        'success' => 'boolean',
    ];
}
