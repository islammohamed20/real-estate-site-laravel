<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_number' => strtoupper('RSV-'.Str::random(10)),
            'customer_id' => Customer::factory(),
            'lead_id' => Lead::factory(),
            'sales_id' => User::factory(),
            'unit_id' => Unit::factory(),
            'reserved_at' => now(),
            'expires_at' => now()->addDays(7),
            'deposit_amount' => fake()->randomFloat(2, 5000, 50000),
            'status' => 'active',
            'notes' => fake()->sentence(),
        ];
    }
}
