<?php

declare(strict_types=1);

namespace App\Http\Requests\Crm;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $customer = $this->customer ?? null;

        return $this->user()?->can(
            $this->isMethod('POST') ? 'create' : 'update',
            $this->isMethod('POST') ? Customer::class : ($customer instanceof Customer ? $customer : null)
        ) ?? false;
    }

    public function rules(): array
    {
        $customerId = $this->customer?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50', "unique:customers,phone,{$customerId}"],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'string', 'max:255', "unique:customers,email,{$customerId}"],
            'address' => ['nullable', 'string', 'max:500'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0'],
            'source' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'preferred_locale' => ['nullable', 'in:ar,en'],
            'interested_project_ids' => ['nullable', 'array'],
            'interested_project_ids.*' => ['integer', 'exists:projects,id'],
            'interested_unit_ids' => ['nullable', 'array'],
            'interested_unit_ids.*' => ['integer', 'exists:units,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }
}
