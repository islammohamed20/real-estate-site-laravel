<?php

namespace App\Listeners;

use App\Events\LeadStageUpdated;
use App\Services\EvolutionApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWhatsAppOnLeadStageUpdated implements ShouldQueue
{
    public function __construct(
        protected EvolutionApiService $whatsappService
    ) {
        //
    }

    public function handle(LeadStageUpdated $event): void
    {
        $lead = $event->lead;

        $message = "📊 *Lead Stage Updated*\n\n"
            ."*Name:* {$lead->name}\n"
            ."*Phone:* {$lead->phone}\n"
            ."*Previous Stage:* {$event->oldStage}\n"
            ."*New Stage:* {$event->newStage}\n\n"
            ."Please take appropriate action.";

        $this->whatsappService->sendToSalesManager($message);
    }
}
