<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\LeadStageChanged;
use App\Services\CRM\CustomerConversionService;

class ConvertLeadToCustomerOnStage
{
    public function __construct(
        private readonly CustomerConversionService $conversionService,
    ) {}

    public function handle(LeadStageChanged $event): void
    {
        $this->conversionService->convertIfStageReached($event->lead);
    }
}
