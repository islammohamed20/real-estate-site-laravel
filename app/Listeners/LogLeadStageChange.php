<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\LeadStageChanged;
use App\Models\AuditLog;

class LogLeadStageChange
{
    public function handle(LeadStageChanged $event): void
    {
        AuditLog::create([
            'user_id' => $event->actor?->id,
            'auditable_type' => $event->lead::class,
            'auditable_id' => $event->lead->id,
            'event' => 'lead_stage_changed',
            'old_values' => ['stage' => $event->fromStage?->value],
            'new_values' => ['stage' => $event->toStage->value],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'properties' => [
                'lead_number' => $event->lead->id,
            ],
        ]);
    }
}
