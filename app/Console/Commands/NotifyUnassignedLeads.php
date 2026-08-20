<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Lead;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class NotifyUnassignedLeads extends Command
{
    protected $signature = 'leads:notify-unassigned {--hours=1 : Minimum hours before notifying}';

    protected $description = 'Notify managers when a lead has been unassigned for too long';

    public function handle(PushNotificationService $push): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $cutoff = now()->subHours($hours);

        $leads = Lead::query()
            ->whereNull('assigned_sales_id')
            ->whereNotIn('status', ['converted', 'cancelled', 'closed', 'lost'])
            ->where('created_at', '<', $cutoff)
            ->get();

        if ($leads->isEmpty()) {
            $this->info('No unassigned leads.');
            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($leads as $lead) {
            $waitingHours = (int) max(1, round($lead->created_at->diffInHours(now())));

            $push->sendToRoles(
                ['Administrator', 'Owner', 'Sales Manager'],
                '📋 لييد غير مُسنَّد',
                ($lead->name ?: $lead->phone) . " — ينتظر منذ {$waitingHours} ساعة",
                '/real-statement-control/crm/leads/' . $lead->id,
                ['tag' => 'unassigned-lead-' . $lead->id]
            );

            $sent++;
            $this->line("Notified about unassigned lead #{$lead->id} ({$lead->name}) — {$waitingHours}h.");
        }

        $this->info("Unassigned leads notified: {$sent}.");
        return self::SUCCESS;
    }
}
