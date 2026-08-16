<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\Customer;
use App\Models\Lead;

class CustomerLeadSyncService
{
    /**
     * Fields that exist on both entities and should stay in sync.
     */
    private const SHARED_FIELDS = [
        'name',
        'phone',
        'whatsapp',
        'email',
        'address',
        'occupation',
        'budget',
    ];

    /**
     * After editing a Customer, push its profile data to every linked Lead
     * so the lead copies never go stale. Only non-empty values overwrite,
     * so a field kept empty on one side doesn't wipe the other.
     */
    public function syncFromCustomer(Customer $customer): void
    {
        $data = $this->nonEmpty($customer->only(self::SHARED_FIELDS));

        foreach ($customer->leads as $lead) {
            $lead->update($data);
        }
    }

    /**
     * After editing a Lead, push its profile data to the owning Customer.
     * budget_min/budget_max are mirrored from the lead's single budget when
     * they are empty, so the customer profile stays consistent.
     */
    public function syncFromLead(Lead $lead): void
    {
        if ($lead->customer_id === null) {
            return;
        }

        $data = $this->nonEmpty($lead->only(self::SHARED_FIELDS));
        $customer = $lead->customer;

        if ($customer === null) {
            return;
        }

        $customer->update($data);

        // Mirror the lead's budget into the customer's budget range when unset.
        if (! empty($data['budget']) && $customer->budget_min === null && $customer->budget_max === null) {
            $customer->update(['budget' => $data['budget']]);
        }
    }

    private function nonEmpty(array $data): array
    {
        return array_filter($data, fn ($value) => $value !== null && $value !== '');
    }
}
