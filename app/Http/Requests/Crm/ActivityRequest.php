<?php

declare(strict_types=1);

namespace App\Http\Requests\Crm;

use App\Models\Crm\CrmActivity;
use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'deal_id' => ['nullable', 'integer', 'exists:crm_deals,id'],
            'contact_id' => ['nullable', 'integer', 'exists:crm_contacts,id'],
            'type' => ['required', 'in:'.implode(',', CrmActivity::types())],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'outcome' => ['nullable', 'string', 'max:100'],
            'duration' => ['nullable', 'string', 'max:30'],
        ];
    }
}
