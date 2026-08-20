<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\CrmActivityNotification;
use App\Services\EvolutionApiService;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Alert salespeople and managers when a lead's follow-up deadline passed
 * without being marked done (converted, cancelled or closed).
 */
class NotifyOverdueFollowUps extends Command
{
    protected $signature = 'sales:notify-overdue-followups';

    protected $description = 'Notify the assigned salesperson and managers about overdue lead follow-ups';

    public function handle(EvolutionApiService $evolution): int
    {
        $overdue = Lead::query()
            ->whereNotNull('follow_up_at')
            ->where('follow_up_at', '<', now())
            ->whereNotNull('assigned_sales_id')
            ->whereNotIn('status', ['converted', 'cancelled', 'closed', 'lost'])
            ->with(['assignedSales:id,name'])
            ->get();

        if ($overdue->isEmpty()) {
            $this->info('No overdue follow-ups.');

            return self::SUCCESS;
        }

        $notified = 0;

        foreach ($overdue as $lead) {
            // Deduplicate: skip when an unread notification already exists for this lead.
            $existing = DB::table('notifications')
                ->where('type', CrmActivityNotification::class)
                ->whereNull('read_at')
                ->where('data', 'like', '%"lead_id":'.$lead->id.'%')
                ->exists();

            if ($existing) {
                continue;
            }

            $rep = $lead->assignedSales;
            $overdueFor = (int) max(1, round($lead->follow_up_at->diffInHours(now())));
            $payload = [
                'lead_id' => $lead->id,
                'name' => $lead->name ?: $lead->phone,
                'hours' => $overdueFor,
                'action_url' => route('dashboard.crm.leads.show', $lead),
            ];

            // Alert the assigned salesperson (respecting their notification preferences).
            if ($rep instanceof User && $rep->is_active && $rep->acceptsNotification('followup_overdue')) {
                Notification::send($rep, new CrmActivityNotification('followup_overdue', $payload, null));
            }

            // Alert the relevant managers too (only the rep's team manager + global).
            CrmActivityNotification::notifyRelevant(
                new CrmActivityNotification('followup_overdue', $payload, null),
                $rep,
                includeOwner: false,
            );

            // WhatsApp alert to the sales manager (once per lead, unread-dedup applies on next run).
            $evolution->sendToSalesManager(
                "📌 متابعة متأخرة\n"
                ."العميل: {$lead->name}\n"
                ."رقم: {$lead->phone}\n"
                ."المندوب: {$rep?->name}\n"
                ."متأخرة منذ: {$overdueFor} ساعة"
            );

            // Push notification to the assigned salesperson
            if ($rep instanceof User && $rep->is_active) {
                app(PushNotificationService::class)->sendToUsers(
                    collect([$rep]),
                    '⚠️ متابعة متأخرة',
                    "{$lead->name} — متأخرة منذ {$overdueFor} ساعة",
                    '/real-statement-control/crm/leads/' . $lead->id,
                    ['tag' => 'overdue-followup-' . $lead->id]
                );
            }

            $notified++;
            $this->line("Notified about lead #{$lead->id} ({$lead->name}) — overdue {$overdueFor}h.");
        }

        $this->info("Overdue follow-ups notified: {$notified}.");

        return self::SUCCESS;
    }
}
