<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'purpose',
        'status',
        'channel',
        'ip_address',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }
}
