<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Services\EvolutionApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWhatsAppOnLeadCreated implements ShouldQueue
{
    public function __construct(
        protected EvolutionApiService $whatsappService
    ) {
        //
    }

    public function handle(LeadCreated $event): void
    {
        $lead = $event->lead;

        $message = "🔔 *New Lead Created*\n\n"
            ."*Name:* {$lead->name}\n"
            ."*Phone:* {$lead->phone}\n"
            ."*Source:* ".($lead->source ?: 'N/A')."\n"
            ."*Budget:* ".($lead->budget ? 'EGP '.number_format((float) $lead->budget) : 'N/A')."\n"
            ."*Project:* ".($lead->interestedProject ? $lead->interestedProject->name : 'N/A')."\n"
            ."*Unit:* ".($lead->interestedUnit ? $lead->interestedUnit->unit_number : 'N/A')."\n\n"
            ."Please follow up with this lead.";

        $this->whatsappService->sendToSalesManager($message);
    }
}
