<?php

declare(strict_types=1);

namespace App\Http\Requests\Crm;

use App\Enums\LeadStage;
use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user) {
            return false;
        }

        $lead = $this->route('lead');

        return $this->isMethod('POST')
            ? $user->can('create', Lead::class)
            : $user->can('update', $lead instanceof Lead ? $lead : null);
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'assigned_sales_id' => ['nullable', 'integer', 'exists:users,id'],
            'lead_source_id' => ['nullable', 'integer', 'exists:lead_sources,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'stage' => ['nullable', 'string', Rule::in(array_map(fn ($s) => $s->value, LeadStage::cases()))],
            'source' => ['nullable', 'string', 'max:100'],
            'campaign' => ['nullable', 'string', 'max:100'],
            'unit_type' => ['nullable', 'string', 'max:100'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'required_area' => ['nullable', 'numeric', 'min:0'],
            'preferred_payment_plan' => ['nullable', 'string', 'max:100'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'last_contacted_at' => ['nullable', 'date'],
            'follow_up_at' => ['nullable', 'date'],
            'converted_at' => ['nullable', 'date'],
            'interested_project_ids' => ['nullable', 'array'],
            'interested_project_ids.*' => ['integer', 'exists:projects,id'],
            'interested_unit_ids' => ['nullable', 'array'],
            'interested_unit_ids.*' => ['integer', 'exists:units,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }
}
