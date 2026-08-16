<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\Crm\CrmDeal;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class CustomerConversionService
{
    /**
     * Lead stages that should automatically convert the lead to a customer.
     */
    private const CONVERTIBLE_STAGES = [
        'contract',
        'delivered',
    ];

    /**
     * Convert a lead into a customer. If the lead already belongs to a customer,
     * that customer is returned. Otherwise a customer is created from the lead
     * data and the lead is linked to it.
     */
    public function convertFromLead(Lead $lead, ?string $source = null): Customer
    {
        if ($lead->customer_id !== null) {
            return $lead->customer;
        }

        return DB::transaction(function () use ($lead, $source): Customer {
            $customer = Customer::query()->firstOrCreate(
                ['phone' => $lead->phone],
                [
                    'name' => $lead->name,
                    'whatsapp' => $lead->whatsapp,
                    'email' => $lead->email,
                    'address' => $lead->address,
                    'occupation' => $lead->occupation,
                    'budget' => $lead->budget,
                    'source' => $source ?? $lead->source,
                    'notes' => $lead->notes,
                ]
            );

            $lead->update([
                'customer_id' => $customer->id,
                'converted_at' => now(),
            ]);

            $customer->interestedProjects()->syncWithoutDetaching(
                $lead->interestedProjects()->pluck('projects.id')
            );

            $lead->tags->each(function ($tag) use ($customer): void {
                $customer->tags()->syncWithoutDetaching([$tag->id]);
            });

            return $customer;
        });
    }

    /**
     * Convert a lead if its current stage represents a closed-won state.
     */
    public function convertIfStageReached(Lead $lead): ?Customer
    {
        if (! in_array($lead->stage->value, self::CONVERTIBLE_STAGES, true)) {
            return null;
        }

        return $this->convertFromLead($lead, 'lead_stage_'.strtolower($lead->stage->label()));
    }

    /**
     * Convert the related lead when a deal is won.
     */
    public function convertIfDealWon(CrmDeal $deal): ?Customer
    {
        if ($deal->status !== 'won' || $deal->lead_id === null) {
            return null;
        }

        return $this->convertFromLead($deal->lead, 'deal_won');
    }

    /**
     * Convert the related lead when a reservation is converted.
     */
    public function convertIfReservationConverted(Reservation $reservation): ?Customer
    {
        if ($reservation->status !== 'converted' || $reservation->lead_id === null) {
            return null;
        }

        return $this->convertFromLead($reservation->lead, 'reservation_converted');
    }
}
