<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\LeadCreated;
use App\Events\LeadStageChanged;
use App\Events\LeadStageUpdated;
use App\Events\OfferCreated;
use App\Listeners\ConvertLeadToCustomerOnStage;
use App\Listeners\LogLeadStageChange;
use App\Listeners\SendWhatsAppOnLeadCreated;
use App\Listeners\SendWhatsAppOnLeadStageUpdated;
use App\Listeners\SendWhatsAppOnOfferCreated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        LeadCreated::class => [
            SendWhatsAppOnLeadCreated::class,
        ],
        OfferCreated::class => [
            SendWhatsAppOnOfferCreated::class,
        ],
        LeadStageUpdated::class => [
            SendWhatsAppOnLeadStageUpdated::class,
        ],
        LeadStageChanged::class => [
            LogLeadStageChange::class,
            ConvertLeadToCustomerOnStage::class,
        ],
    ];
}
