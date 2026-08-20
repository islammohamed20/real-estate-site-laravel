<?php

declare(strict_types=1);

namespace App\Http\Requests\Crm;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $reservation = $this->reservation ?? null;

        return $this->user()?->can(
            $this->isMethod('POST') ? 'create' : 'update',
            $this->isMethod('POST') ? Reservation::class : ($reservation instanceof Reservation ? $reservation : null)
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'unit_id' => [
                'required',
                'integer',
                'exists:units,id',
                function (string $attribute, $value, $fail) {
                    $reservation = $this->reservation ?? null;
                    $newStatus = $this->input('status', $reservation?->status?->value ?? 'pending');

                    // Only active statuses can conflict with another active reservation.
                    if (! in_array($newStatus, ['pending', 'paid'], true)) {
                        return;
                    }

                    $query = Reservation::query()
                        ->where('unit_id', $value)
                        ->whereIn('status', ['pending', 'paid']);

                    if ($reservation instanceof Reservation) {
                        $query->where('id', '!=', $reservation->id);
                    }

                    if ($query->exists()) {
                        $fail(__('This unit already has an active reservation (pending or paid).'));
                    }
                },
            ],
            'reserved_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:reserved_at'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'status' => ['nullable', 'in:pending,paid,converted,cancelled,expired'],
        ];
    }
}
