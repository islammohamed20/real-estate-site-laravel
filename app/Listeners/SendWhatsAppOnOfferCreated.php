<?php

namespace App\Listeners;

use App\Events\OfferCreated;
use App\Services\EvolutionApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWhatsAppOnOfferCreated implements ShouldQueue
{
    public function __construct(
        protected EvolutionApiService $whatsappService
    ) {
        //
    }

    public function handle(OfferCreated $event): void
    {
        $offer = $event->offer;

        $message = "💰 *New Offer Created*\n\n"
            ."*Offer Number:* {$offer->offer_number}\n"
            ."*Customer:* ".($offer->customer ? $offer->customer->name : 'N/A')."\n"
            ."*Unit:* ".($offer->unit ? $offer->unit->unit_number : 'N/A')."\n"
            ."*Project:* ".($offer->unit && $offer->unit->project ? $offer->unit->project->name : 'N/A')."\n"
            ."*Total Price:* ".($offer->total_price ? 'EGP '.number_format((float) $offer->total_price) : 'N/A')."\n\n"
            ."Please review this offer.";

        $this->whatsappService->sendToSalesManager($message);
    }
}
