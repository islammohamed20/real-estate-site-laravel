<?php

declare(strict_types=1);

namespace App\Http\Requests\Crm;

use App\Models\Offer;
use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $offer = $this->offer ?? null;

        return $this->user()?->can(
            $this->isMethod('POST') ? 'create' : 'update',
            $this->isMethod('POST') ? Offer::class : ($offer instanceof Offer ? $offer : null)
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'installment_template_id' => ['nullable', 'integer', 'exists:installment_templates,id'],
            'installment_plan_id' => ['nullable', 'integer', 'exists:installment_plans,id'],
            'issue_date' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'status' => ['nullable', 'in:draft,sent,accepted,rejected,expired'],
            'stamp_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
