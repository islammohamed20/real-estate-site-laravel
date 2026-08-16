<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\InstallmentPlan;
use App\Models\InstallmentTemplate;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Offer>
 */
class OfferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'offer_number' => strtoupper('OFF-'.Str::random(10)),
            'customer_id' => Customer::factory(),
            'lead_id' => Lead::factory(),
            'sales_id' => User::factory(),
            'project_id' => Project::factory(),
            'unit_id' => Unit::factory(),
            'installment_template_id' => InstallmentTemplate::factory(),
            'installment_plan_id' => InstallmentPlan::factory(),
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'subtotal' => fake()->randomFloat(2, 100000, 1000000),
            'discount_amount' => fake()->randomFloat(2, 0, 50000),
            'total_amount' => fake()->randomFloat(2, 100000, 1000000),
            'qr_code_path' => null,
            'notes' => fake()->sentence(),
            'status' => 'draft',
            'stamp_text' => 'Company Stamp',
        ];
    }
}
