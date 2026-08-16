<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->phoneNumber(),
            'whatsapp' => fake()->optional()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'occupation' => fake()->jobTitle(),
            'budget' => fake()->randomFloat(2, 100000, 1500000),
            'budget_min' => fake()->randomFloat(2, 100000, 500000),
            'budget_max' => fake()->randomFloat(2, 500000, 1500000),
            'source' => 'website',
        ];
    }
}
