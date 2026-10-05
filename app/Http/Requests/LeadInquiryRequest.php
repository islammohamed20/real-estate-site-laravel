<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'unit_id' => ['nullable', 'integer', 'exists:units,id'],
            'message' => ['nullable', 'string', 'max:4000'],
            'source' => ['nullable', 'string', 'max:100'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'follow_up_at' => ['nullable', 'date'],
            'turnstile' => Rule::when(
                config('services.turnstile.enabled') && config('services.turnstile.secret_key'),
                ['required', 'string', new Turnstile],
                []
            ),
        ];
    }
}
