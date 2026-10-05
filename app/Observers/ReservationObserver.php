<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\UnitStatus;
use App\Models\Reservation;
use App\Services\CRM\CustomerConversionService;

class ReservationObserver
{
    public function __construct(
        private readonly CustomerConversionService $conversionService,
    ) {}

    public function saved(Reservation $reservation): void
    {
        if ($reservation->isDirty('status') || $reservation->isDirty('unit_id') || $reservation->wasRecentlyCreated) {
            $previousUnitId = $reservation->getOriginal('unit_id');
            $this->syncUnitAvailability($reservation);

            if ($reservation->isDirty('unit_id') && $previousUnitId) {
                $this->releaseUnitIfUnused((int) $previousUnitId);
            }

            $this->conversionService->convertIfReservationConverted($reservation);
        }
    }

    public function deleted(Reservation $reservation): void
    {
        if ($reservation->unit_id) {
            $this->releaseUnitIfUnused((int) $reservation->unit_id);
        }
    }

    /**
     * Lock the unit while an active reservation exists on it (pending/paid),
     * and release it back to available when the reservation is cancelled/expired
     * — unless another active reservation still holds the unit.
     */
    protected function syncUnitAvailability(Reservation $reservation): void
    {
        if (! $reservation->unit_id) {
            return;
        }

        $unit = $reservation->unit;
        $hasOtherActive = Reservation::query()
            ->where('unit_id', $unit->id)
            ->whereIn('status', ['pending', 'paid'])
            ->where('id', '!=', $reservation->id)
            ->exists();

        if (in_array($reservation->status, ['pending', 'paid'], true) || $hasOtherActive) {
            // An active reservation exists on this unit → it must stay reserved.
            if ($unit->status !== UnitStatus::Reserved) {
                $unit->update(['status' => UnitStatus::Reserved->value]);
            }
        } elseif (in_array($reservation->status, ['cancelled', 'expired'], true)) {
            // No other active reservation → release the unit back to available.
            if ($unit->status === UnitStatus::Reserved) {
                $unit->update(['status' => UnitStatus::Available->value]);
            }
        }
    }

    private function releaseUnitIfUnused(int $unitId): void
    {
        $hasActive = Reservation::query()
            ->where('unit_id', $unitId)
            ->whereIn('status', ['pending', 'paid'])
            ->exists();

        if ($hasActive) {
            return;
        }

        $unit = \App\Models\Unit::query()->find($unitId);
        if ($unit && $unit->status === UnitStatus::Reserved) {
            $unit->update(['status' => UnitStatus::Available->value]);
        }
    }
}
