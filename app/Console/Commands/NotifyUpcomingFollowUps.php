<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\FollowUp;
use App\Models\SalesTeam;
use App\Models\User;
use App\Notifications\CrmActivityNotification;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyUpcomingFollowUps extends Command
{
    protected $signature = "follow-ups:remind {--minutes=60 : Minutes before follow-up to send reminder}";
    protected $description = "Send push notifications for follow-ups due within N minutes";

    public function handle(PushNotificationService $push): int
    {
        $minutes = (int) $this->option("minutes");
        $from = now();
        $to = now()->addMinutes($minutes);

        $followUps = FollowUp::pending()
            ->where("follow_up_at", ">=", $from)
            ->where("follow_up_at", "<=", $to)
            ->whereNull("reminded_at")
            ->where("reminder", true)
            ->with(["assignee", "lead", "customer"])
            ->get();

        if ($followUps->isEmpty()) {
            $this->info("No upcoming follow-ups to remind.");
            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($followUps as $fu) {
            $assignee = $fu->assignee;
            if (!$assignee) continue;

            $clientName = $fu->customer?->name ?? $fu->lead?->name ?? __("Unknown");
            $type = $fu->type ?? __("Follow-up");
            $time = $fu->follow_up_at->format("H:i");

            $title = "⏰ Follow-up in " . $minutes . " min";
            $body = sprintf(
                "%s %s - %s at %s",
                $type,
                __("with"),
                $clientName,
                $time
            );

            $url = "/real-statement-control/crm/follow-ups";
            $notification = new CrmActivityNotification('followup_reminder', [
                'follow_up_id' => $fu->id,
                'name' => $clientName,
                'type' => $type,
                'minutes' => $minutes,
                'action_url' => $url,
            ]);

            if (! $assignee->hasAnyRole(['Administrator', 'Owner'])
                && $assignee->acceptsNotification('followup_reminder')) {
                \Illuminate\Support\Facades\Notification::send($assignee, $notification);
            }
            CrmActivityNotification::notifyRelevant($notification, $assignee, includeOwner: false);

            $managerIds = SalesTeam::query()
                ->whereHas('members', fn ($query) => $query->whereKey($assignee->id))
                ->pluck('manager_id')
                ->filter();
            $recipientIds = $managerIds->push($assignee->id)->unique()->values();
            $recipients = User::query()
                ->where('is_active', true)
                ->where(function ($query) use ($recipientIds): void {
                    $query->whereIn('id', $recipientIds)
                        ->orWhereHas('roles', fn ($role) => $role->whereIn('name', ['Administrator', 'Owner']));
                })
                ->get();

            $push->sendToUsers(
                $recipients,
                $title,
                $body,
                $url,
                ["tag" => "follow-up-" . $fu->id]
            );

            $fu->update(["reminded_at" => now()]);
            $sent++;
        }

        $this->info("Sent {$sent} follow-up reminder(s).");
        return self::SUCCESS;
    }
}
