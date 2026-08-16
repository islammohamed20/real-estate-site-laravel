<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Events\LeadCreated;
use App\Enums\LeadStage;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LeadCreationService
{
    public function createFromInquiry(array $data): Lead
    {
        return DB::transaction(function () use ($data): Lead {
            $customer = Customer::query()->where('phone', Arr::get($data, 'phone'))->first();

            $lead = Lead::query()->create([
                'customer_id' => $customer?->id,
                'assigned_sales_id' => Arr::get($data, 'assigned_to') ?? Arr::get($data, 'assigned_sales_id'),
                'lead_source_id' => Arr::get($data, 'lead_source_id'),
                'name' => Arr::get($data, 'name'),
                'phone' => Arr::get($data, 'phone'),
                'whatsapp' => Arr::get($data, 'whatsapp') ?? Arr::get($data, 'phone'),
                'email' => Arr::get($data, 'email'),
                'address' => Arr::get($data, 'address'),
                'occupation' => Arr::get($data, 'occupation'),
                'budget' => Arr::get($data, 'budget'),
                'stage' => Arr::has($data, 'stage') ? Arr::get($data, 'stage') : LeadStage::New,
                'status' => Arr::get($data, 'status', 'active'),
                'source' => Arr::get($data, 'source', 'website'),
                'campaign' => Arr::get($data, 'campaign'),
                'unit_type' => Arr::get($data, 'unit_type'),
                'bedrooms' => Arr::get($data, 'bedrooms'),
                'required_area' => Arr::get($data, 'required_area'),
                'preferred_payment_plan' => Arr::get($data, 'preferred_payment_plan'),
                'priority' => Arr::get($data, 'priority', 'normal'),
                'notes' => Arr::get($data, 'message') ?? Arr::get($data, 'notes'),
                'last_contacted_at' => Arr::get($data, 'last_contacted_at'),
                'follow_up_at' => Arr::get($data, 'follow_up_at'),
                'converted_at' => Arr::get($data, 'converted_at'),
            ]);

            if ($customer !== null && Arr::has($data, 'project_id') && $data['project_id'] !== null) {
                $customer->interestedProjects()->syncWithoutDetaching([$data['project_id']]);
            }

            if (Arr::has($data, 'project_id') && $data['project_id'] !== null) {
                $lead->interestedProjects()->syncWithoutDetaching([$data['project_id']]);
            }

            if (Arr::has($data, 'unit_id') && $data['unit_id'] !== null) {
                $lead->notes = trim(($lead->notes ?? '')."\nUnit inquiry: ".(string) $data['unit_id']);
                $lead->save();
            }

            $lead->load(['customer', 'interestedProjects']);

            LeadCreated::dispatch($lead);

            return $lead;
        });
    }
}
