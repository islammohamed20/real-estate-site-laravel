<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\InstallmentPlan;
use App\Models\InstallmentTemplate;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstallmentPlan>
 */
class InstallmentPlanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'installment_template_id' => InstallmentTemplate::factory(),
            'customer_id' => Customer::factory(),
            'project_id' => Project::factory(),
            'unit_id' => Unit::factory(),
            'created_by' => User::factory(),
            'name' => fake()->words(3, true),
            'status' => 'draft',
            'currency_code' => 'USD',
            'base_price' => fake()->randomFloat(2, 100000, 1000000),
            'discount_amount' => fake()->randomFloat(2, 0, 50000),
            'final_price' => fake()->randomFloat(2, 100000, 1000000),
            'maintenance_deposit' => fake()->randomFloat(2, 0, 100000),
            'down_payment' => fake()->randomFloat(2, 10000, 200000),
            'remaining_amount' => fake()->randomFloat(2, 50000, 900000),
            'installment_amount' => fake()->randomFloat(2, 5000, 50000),
            'installment_count' => 16,
            'installment_type' => 'quarterly',
            'schedule_json' => [],
            'starts_at' => now()->toDateString(),
            'last_installment_adjustment' => 0,
            'saved_from_calculator' => true,
        ];
    }
}
