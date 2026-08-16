<?php

declare(strict_types=1);

namespace App\Events;

use App\Enums\LeadStage;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadStageChanged
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Lead $lead,
        public readonly ?LeadStage $fromStage,
        public readonly LeadStage $toStage,
        public readonly ?User $actor = null,
    ) {}
}
