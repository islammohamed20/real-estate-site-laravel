<?php

declare(strict_types=1);

namespace App\Http\Requests\Crm;

use App\Models\Crm\CrmDeal;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'related_type' => ['required', 'in:lead,customer,deal,project,unit'],
            'related_id' => ['required', 'integer'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'status' => ['nullable', 'in:open,in_progress,completed,cancelled'],
            'due_at' => ['nullable', 'date'],
            'reminder' => ['nullable', 'boolean'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'taskable_type' => $this->resolveTaskableType($this->input('related_type')),
            'taskable_id' => $this->input('related_id'),
        ]);
    }

    private function resolveTaskableType(string $type): string
    {
        return match ($type) {
            'lead' => Lead::class,
            'customer' => Customer::class,
            'deal' => CrmDeal::class,
            'project' => Project::class,
            'unit' => Unit::class,
        };
    }
}
