<?php

declare(strict_types=1);

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CrmActivity extends Model
{
    use HasFactory;

    protected $table = 'crm_activities';

    protected $fillable = [
        'deal_id',
        'contact_id',
        'created_by',
        'activityable_type',
        'activityable_id',
        'type',
        'subject',
        'body',
        'due_at',
        'completed_at',
        'outcome',
        'duration',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(CrmDeal::class, 'deal_id')->withDefault();
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activityable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public static function types(): array
    {
        return [
            'call',
            'email',
            'meeting',
            'note',
            'task',
            'whatsapp',
            'sms',
            'follow_up',
        ];
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'call' => 'phone',
            'email' => 'mail',
            'meeting' => 'calendar',
            'note' => 'file-text',
            'task' => 'check-square',
            'whatsapp' => 'message-circle',
            'sms' => 'message-square',
            'follow_up' => 'clock',
            default => 'activity',
        };
    }
}
