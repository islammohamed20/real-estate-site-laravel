<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Crm\CrmDeal;
use App\Services\CRM\CustomerConversionService;

class CrmDealObserver
{
    public function __construct(
        private readonly CustomerConversionService $conversionService,
    ) {}

    public function saved(CrmDeal $deal): void
    {
        if ($deal->isDirty('status') || $deal->wasRecentlyCreated) {
            $this->conversionService->convertIfDealWon($deal);
        }
    }
}
