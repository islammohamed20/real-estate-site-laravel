<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Notifications\CrmActivityNotification;
use App\Services\EvolutionApiService;
use App\Services\Sales\LeadAssignmentService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Sales response SLA automation.
 *
 * 1. When an assigned conversation has an incoming message waiting longer than
 *    `--sla-minutes`, the sales manager is alerted (dashboard + WhatsApp).
 * 2. When it waits longer than `--escalate-minutes`, the conversation (and its
 *    linked lead, if any) is automatically re-assigned to another salesperson.
 */
class EnforceResponseSla extends Command
{
    protected $signature = 'sales:enforce-response-sla
        {--sla-minutes=30 : Minutes an assigned conversation may wait before the manager is alerted}
        {--escalate-minutes=120 : Minutes before the conversation is auto re-assigned to another salesperson}';

    protected $description = 'Alert managers and auto re-assign conversations where a salesperson exceeds the response SLA';

    public function handle(
        EvolutionApiService $evolution,
        LeadAssignmentService $assignment,
    ): int {
        $slaMinutes = max(1, (int) $this->option('sla-minutes'));
        $escalateMinutes = max($slaMinutes + 1, (int) $this->option('escalate-minutes'));

        $slaCutoff = now()->subMinutes($slaMinutes);
        $escalateCutoff = now()->subMinutes($escalateMinutes);

        $conversations = WhatsAppConversation::query()
            ->whereNotNull('assigned_to')
            ->where('status', '!=', WhatsAppConversation::STATUS_CLOSED)
            ->where('last_message_at', '<', $slaCutoff)
            ->whereHas('messages', fn (Builder $q) => $q->where('direction', WhatsAppMessage::DIRECTION_INCOMING))
            ->with(['assignedTo:id,name', 'linkedLead'])
            ->get();

        if ($conversations->isEmpty()) {
            $this->info('No assigned conversations exceed the response SLA.');

            return self::SUCCESS;
        }

        $notified = 0;
        $reassigned = 0;

        foreach ($conversations as $conversation) {
            $lastMessage = $conversation->messages()->latest('id')->first();

            // Only act when the customer is still waiting (last message is incoming).
            if (! $lastMessage instanceof WhatsAppMessage || $lastMessage->direction !== WhatsAppMessage::DIRECTION_INCOMING) {
                continue;
            }

            $waitingMinutes = (int) max(1, round($lastMessage->created_at->diffInMinutes(now())));
            $displayName = $conversation->customer_name ?: $conversation->customer_phone;

            if ($waitingMinutes >= $escalateMinutes) {
                if ($this->reassignConversation($conversation, $assignment)) {
                    $reassigned++;
                    $this->line("Re-assigned conversation #{$conversation->id} ({$displayName}) after {$waitingMinutes} min.");
                }
                continue;
            }

            if ($this->notifyManager($conversation, $lastMessage, $waitingMinutes, $evolution)) {
                $notified++;
                $this->line("Notified manager: conversation #{$conversation->id} ({$displayName}) waiting {$waitingMinutes} min.");
            }
        }

        $this->info("SLA pass complete — {$notified} alert(s), {$reassigned} re-assignment(s).");

        return self::SUCCESS;
    }

    /**
     * Send one manager alert per conversation (deduplicated while unread).
     */
    protected function notifyManager(
        WhatsAppConversation $conversation,
        WhatsAppMessage $lastMessage,
        int $waitingMinutes,
        EvolutionApiService $evolution,
    ): bool {
        $existing = $this->hasNotificationFor($conversation, 'whatsapp_sla', null);

        if ($existing) {
            return false;
        }

        $repName = $conversation->assignedTo?->name ?? __('Unknown');

        $notification = new CrmActivityNotification('whatsapp_sla', [
            'conversation_id' => $conversation->id,
            'customer_name' => $conversation->customer_name,
            'customer_phone' => $conversation->customer_phone,
            'rep_name' => $repName,
            'minutes' => $waitingMinutes,
            'action_url' => route('dashboard.whatsapp.index'),
        ], null);

        // Team-aware: only the assigned rep's team manager + global users.
        CrmActivityNotification::notifyRelevant($notification, $conversation->assignedTo, includeOwner: false);

        // WhatsApp alert to the configured sales manager number.
        $evolution->sendToSalesManager(
            "⚠️ تنبيه SLA — عميل بانتظار الرد\n"
            ."العميل: {$conversation->customer_name}\n"
            ."رقم: {$conversation->customer_phone}\n"
            ."المندوب: {$repName}\n"
            ."مدة الانتظار: {$waitingMinutes} دقيقة\n"
            ."سيُرحَّل تلقائيًا لمندوب آخر إذا لم يرد خلال ساعتين."
        );

        return true;
    }

    /**
     * Move the conversation and its linked lead to another salesperson.
     */
    protected function reassignConversation(
        WhatsAppConversation $conversation,
        LeadAssignmentService $assignment,
    ): bool {
        // Cooldown: never reassign the same conversation more than once per 12h,
        // otherwise it would ping-pong between salespeople on every pass.
        if ($this->hasNotificationFor($conversation, 'whatsapp_reassigned', now()->subHours(12))) {
            $this->line("Conversation #{$conversation->id} already re-assigned recently — skipped.");

            return false;
        }

        $currentUserId = (int) $conversation->assigned_to;
        $targetUserId = $assignment->pickRandomIdExcluding($currentUserId);

        if ($targetUserId === null) {
            $this->warn("No other salesperson available for conversation #{$conversation->id}.");

            return false;
        }

        $conversation->update(['assigned_to' => $targetUserId]);

        // Keep the linked lead in sync with the same salesperson.
        if ($conversation->linkedLead instanceof Lead) {
            $assignment->reassignRandomly($conversation->linkedLead);
        }

        $displayName = $conversation->customer_name ?: $conversation->customer_phone;

        // Team-aware: only the NEW owner's team manager + global users.
        CrmActivityNotification::notifyRelevant(new CrmActivityNotification('whatsapp_reassigned', [
            'conversation_id' => $conversation->id,
            'customer_name' => $displayName,
            'from_user_id' => $currentUserId,
            'to_user_id' => $targetUserId,
            'action_url' => route('dashboard.whatsapp.index'),
        ], null), User::query()->find($targetUserId), includeOwner: false);

        return true;
    }

    /**
     * Check whether a notification of a specific payload type already exists
     * for this conversation, optionally within a time window.
     */
    protected function hasNotificationFor(WhatsAppConversation $conversation, string $payloadType, ?\Carbon\CarbonInterface $since): bool
    {
        $query = DB::table('notifications')
            ->where('type', CrmActivityNotification::class)
            ->where('data', 'like', '%"conversation_id":'.$conversation->id.'%')
            ->where('data', 'like', '%"type":"'.$payloadType.'"%');

        if ($since !== null) {
            $query->where('created_at', '>', $since);
        } else {
            $query->whereNull('read_at');
        }

        return $query->exists();
    }
}
