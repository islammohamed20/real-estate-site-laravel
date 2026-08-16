<?php

declare(strict_types=1);

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class DealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'string', 'max:255'],
            'pipeline_id' => [$required, 'exists:crm_pipelines,id'],
            'stage_id' => [$required, 'exists:crm_stages,id'],
            'organization_id' => ['nullable', 'exists:crm_organizations,id'],
            'contact_id' => ['nullable', 'exists:crm_contacts,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'unit_id' => ['nullable', 'exists:units,id'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'currency_code' => ['nullable', 'string', 'max:10'],
            'expected_close_date' => ['nullable', 'date'],
            'priority' => [$required, 'in:low,medium,high'],
            'source' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:open,won,lost'],
        ];
    }
}
