<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Crm\CrmDeal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'customer_id',
        'deal_id',
        'assigned_to',
        'created_by',
        'follow_up_at',
        'completed_at',
        'type',
        'channel',
        'priority',
        'reminder',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_at' => 'datetime',
            'completed_at' => 'datetime',
            'reminder' => 'boolean',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(CrmDeal::class, 'deal_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopePending($query)
    {
        return $query->whereNull('completed_at')->where('status', '!=', 'cancelled');
    }

    public function scopeOverdue($query)
    {
        return $query->pending()->where('follow_up_at', '<', now());
    }
}
