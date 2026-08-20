<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\FollowUp;
use App\Models\User;
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
            $channel = $fu->channel ? " via " . $fu->channel : "";
            $time = $fu->follow_up_at->format("H:i");

            $title = "⏰ Follow-up in " . $minutes . " min";
            $body = sprintf(
                "%s %s - %s%s at %s",
                $type,
                __("with"),
                $clientName,
                $channel,
                $time
            );

            $url = "/real-statement-control/crm/follow-ups";

            $push->sendToUsers(
                collect([$assignee]),
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
