<?php

declare(strict_types=1);

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class FollowUpRequest extends FormRequest
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
            'assigned_to' => ['nullable', 'exists:users,id'],
            'follow_up_at' => ['required', 'date'],
            'type' => ['required', 'in:phone_call,whatsapp,email,meeting,site_visit,follow_up,other'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'reminder' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:pending,completed,cancelled,overdue'],
            'completed_at' => ['nullable', 'date'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $related = array_filter([
                $this->input('lead_id'),
                $this->input('customer_id'),
                $this->input('deal_id'),
            ], fn ($value) => filled($value));

            if (count($related) !== 1) {
                $validator->errors()->add(
                    'customer_id',
                    __('Select exactly one customer, lead or deal for this follow-up.'),
                );
            }
        });
    }
}
