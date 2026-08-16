<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FollowUp>
 */
class FollowUpFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'customer_id' => Customer::factory(),
            'assigned_to' => User::factory(),
            'created_by' => User::factory(),
            'follow_up_at' => now()->addDay(),
            'completed_at' => null,
            'channel' => 'phone',
            'notes' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
