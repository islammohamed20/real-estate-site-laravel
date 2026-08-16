<?php

declare(strict_types=1);

namespace App\Http\Requests\Crm;

use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmDeal;
use App\Models\Crm\CrmOrganization;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;

class NoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string'],
            'type' => ['required', 'in:call,meeting,note'],
            'related_type' => ['required', 'in:lead,customer,deal,organization,contact,project,unit,offer,reservation'],
            'related_id' => ['required', 'integer'],
            'noted_at' => ['nullable', 'date'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'noteable_type' => $this->resolveNoteableType($this->input('related_type')),
            'noteable_id' => $this->input('related_id'),
        ]);
    }

    private function resolveNoteableType(string $type): string
    {
        return match ($type) {
            'lead' => Lead::class,
            'customer' => Customer::class,
            'deal' => CrmDeal::class,
            'organization' => CrmOrganization::class,
            'contact' => CrmContact::class,
            'project' => Project::class,
            'unit' => Unit::class,
            'offer' => Offer::class,
            'reservation' => Reservation::class,
        };
    }
}
