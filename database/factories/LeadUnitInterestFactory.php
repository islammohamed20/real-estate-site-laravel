<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadUnitInterest;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadUnitInterest>
 */
class LeadUnitInterestFactory extends Factory
{
    protected $model = LeadUnitInterest::class;

    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'customer_id' => Customer::factory(),
            'unit_id' => Unit::factory(),
            'priority' => fake()->randomElement(['low', 'normal', 'high']),
            'notes' => fake()->optional()->sentence(),
            'status' => 'interested',
        ];
    }
}
