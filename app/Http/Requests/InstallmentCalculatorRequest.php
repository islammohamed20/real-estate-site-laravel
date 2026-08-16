<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallmentCalculatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $normalizeNullableId = static function ($value): ?int {
            if ($value === null || $value === '' || $value === '0' || $value === 0) {
                return null;
            }

            return is_numeric($value) ? (int) $value : null;
        };

        $this->merge([
            'customer_id' => $normalizeNullableId($this->input('customer_id')),
            'offer_id' => $normalizeNullableId($this->input('offer_id')),
        ]);
    }

    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'building_id' => ['nullable', 'integer', 'exists:buildings,id'],
            'floor_id' => ['nullable', 'integer', 'exists:floors,id'],
            'unit_id' => ['nullable', 'integer', 'exists:units,id'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'price_per_meter' => ['required', 'numeric', 'min:0'],
            'area' => ['required', 'numeric', 'min:0'],
            'garden_price' => ['nullable', 'numeric', 'min:0'],
            'roof_price' => ['nullable', 'numeric', 'min:0'],
            'excellence_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'down_payment_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'maintenance_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'installment_count' => ['nullable', 'integer', 'min:1'],
            'installment_years' => ['required', 'numeric', 'min:0.5', 'max:50'],
            'installment_type' => ['required', 'string', 'in:monthly,quarterly,semi_annual'],
            'payment_method' => ['nullable', 'string', 'in:cash,installments'],
            'first_installment_date' => ['required', 'date'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'offer_id' => ['nullable', 'integer', 'exists:offers,id'],
            'save_to_crm' => ['sometimes', 'boolean'],
        ];
    }
}
