<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LeadStage;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => null,
            'assigned_sales_id' => null,
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'whatsapp' => fake()->optional()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'address' => fake()->optional()->address(),
            'occupation' => fake()->optional()->jobTitle(),
            'budget' => fake()->randomFloat(2, 100000, 1000000),
            'stage' => LeadStage::New,
            'status' => 'active',
            'source' => 'website',
            'campaign' => fake()->optional()->word(),
            'unit_type' => fake()->optional()->randomElement(['Apartment', 'Villa', 'Duplex', 'Penthouse']),
            'bedrooms' => fake()->optional()->numberBetween(1, 5),
            'required_area' => fake()->optional()->randomFloat(2, 80, 300),
            'preferred_payment_plan' => fake()->optional()->randomElement(['Cash', 'Installments 5 years', 'Installments 8 years']),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'notes' => fake()->sentence(),
            'follow_up_at' => fake()->optional()->dateTimeBetween('now', '+14 days'),
        ];
    }

    public function withCustomer(): static
    {
        return $this->state(fn () => ['customer_id' => Customer::factory()]);
    }
}
