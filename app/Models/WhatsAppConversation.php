<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppConversation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'whatsapp_conversations';

    public const STATUS_NEW = 'new';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'customer_phone',
        'customer_name',
        'faalwa_user_ns',
        'status',
        'assigned_to',
        'linked_lead_id',
        'linked_customer_id',
        'unread_count',
        'last_message_at',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'unread_count' => 'integer',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function linkedLead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'linked_lead_id');
    }

    public function linkedCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'linked_customer_id');
    }

    public function scopeVisibleTo($query, User $user): void
    {
        if ($user->can('view all whatsapp conversations') || $user->can('assign whatsapp')) {
            return;
        }

        // Sales reps see ONLY conversations assigned to them.
        $query->where('assigned_to', $user->id);
    }

    public function markAsRead(): void
    {
        $this->update([
            'unread_count' => 0,
            'last_seen_at' => now(),
        ]);
        $this->messages()->where('direction', 'incoming')->whereNull('read_at')->update([
            'read_at' => now(),
        ]);
    }
}
