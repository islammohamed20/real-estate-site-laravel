<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InstallmentFrequency;
use App\Models\InstallmentTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstallmentTemplate>
 */
class InstallmentTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'code' => strtoupper(fake()->unique()->bothify('TPL-###')),
            'description' => fake()->sentence(),
            'down_payment_percent' => 10,
            'down_payment_amount' => null,
            'installment_count' => 16,
            'installment_frequency' => InstallmentFrequency::Quarterly,
            'maintenance_percent' => 7,
            'discount_percent' => 0,
            'first_installment_offset_months' => 0,
            'is_default' => false,
            'is_active' => true,
            'defaults_json' => [],
        ];
    }
}
