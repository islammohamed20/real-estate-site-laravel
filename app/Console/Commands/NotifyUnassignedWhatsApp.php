<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Notifications\CrmActivityNotification;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class NotifyUnassignedWhatsApp extends Command
{
    protected $signature = 'whatsapp:notify-unassigned {--hours=1 : Minimum waiting hours before notifying}';

    protected $description = 'Notify sales managers when an unassigned WhatsApp conversation has not been replied to for over an hour';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $cutoff = now()->subHours($hours);

        // Conversations that are unassigned, open (not closed), and had activity before the cutoff.
        $conversations = WhatsAppConversation::query()
            ->whereNull('assigned_to')
            ->where('status', '!=', WhatsAppConversation::STATUS_CLOSED)
            ->where('last_message_at', '<', $cutoff)
            ->whereHas('messages', fn (Builder $q) => $q->where('direction', 'incoming'))
            ->get();

        if ($conversations->isEmpty()) {
            $this->info('No unassigned conversations awaiting a reply.');

            return self::SUCCESS;
        }

        $notified = 0;

        foreach ($conversations as $conversation) {
            // Only notify when the last message is an incoming one (no reply sent yet).
            $lastMessage = $conversation->messages()->latest('id')->first();

            if (! $lastMessage instanceof WhatsAppMessage || $lastMessage->direction !== 'incoming') {
                continue;
            }

            // Deduplicate: skip if an unread notification already exists for this conversation.
            $existing = DB::table('notifications')
                ->where('type', CrmActivityNotification::class)
                ->whereNull('read_at')
                ->where('data', 'like', '%"conversation_id":'.$conversation->id.'%')
                ->exists();

            if ($existing) {
                $this->line("Already notified for conversation #{$conversation->id} — skipped.");
                continue;
            }

            $hoursWaiting = (int) max(1, round($lastMessage->created_at->diffInHours(now())));

            $notification = new CrmActivityNotification('whatsapp_unassigned', [
                'conversation_id' => $conversation->id,
                'customer_name' => $conversation->customer_name,
                'customer_phone' => $conversation->customer_phone,
                'hours' => $hoursWaiting,
                'action_url' => route('dashboard.whatsapp.index'),
            ], null);

            CrmActivityNotification::notifyManagers($notification);
            $notified++;
            $displayName = $conversation->customer_name ?: $conversation->customer_phone;
            $this->line("Notified managers about conversation #{$conversation->id} ({$displayName}) — {$hoursWaiting}h waiting.");
        }

        $this->info("Done. {$notified} conversation(s) notified.");

        return self::SUCCESS;
    }
}
