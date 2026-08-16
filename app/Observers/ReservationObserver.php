<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Reservation;
use App\Services\CRM\CustomerConversionService;

class ReservationObserver
{
    public function __construct(
        private readonly CustomerConversionService $conversionService,
    ) {}

    public function saved(Reservation $reservation): void
    {
        if ($reservation->isDirty('status') || $reservation->wasRecentlyCreated) {
            $this->conversionService->convertIfReservationConverted($reservation);
        }
    }
}
