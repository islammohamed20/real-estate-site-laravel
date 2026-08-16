<?php

declare(strict_types=1);

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_active;
    }

    public function rules(): array
    {
        return [
            'documentable_type' => ['required', 'in:App\\Models\\Lead,App\\Models\\Customer,App\\Models\\Offer,App\\Models\\Reservation'],
            'documentable_id' => ['required', 'integer', 'min:1'],
            'file' => ['required', 'file', 'max:10240'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
