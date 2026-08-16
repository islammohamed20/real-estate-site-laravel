<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\Installments\InstallmentCalculatorService;
use Tests\TestCase;

class InstallmentCalculatorTest extends TestCase
{
    public function test_discount_plan_applies_for_four_years_with_down_payment_above_ten_percent(): void
    {
        $service = app(InstallmentCalculatorService::class);
        $result = $service->calculate([
            'area' => 100,
            'price_per_meter' => 10000,
            'down_payment_percent' => 15,
            'maintenance_percent' => 7,
            'installment_years' => 4,
            'installment_type' => 'quarterly',
            'first_installment_date' => now()->toDateString(),
        ]);

        $this->assertTrue($result['discount_plan_applied']);
        $this->assertGreaterThan(0, $result['discount_plan_discount']);
    }

    public function test_discount_plan_applies_for_three_years_with_down_payment_above_ten_percent(): void
    {
        $service = app(InstallmentCalculatorService::class);
        $result = $service->calculate([
            'area' => 100,
            'price_per_meter' => 10000,
            'down_payment_percent' => 15,
            'maintenance_percent' => 7,
            'installment_years' => 3,
            'installment_type' => 'quarterly',
            'first_installment_date' => now()->toDateString(),
        ]);

        $this->assertTrue($result['discount_plan_applied']);
        $this->assertGreaterThan(0, $result['discount_plan_discount']);
    }

    public function test_discount_plan_does_not_apply_for_five_years_with_down_payment_above_ten_percent(): void
    {
        $service = app(InstallmentCalculatorService::class);
        $result = $service->calculate([
            'area' => 100,
            'price_per_meter' => 10000,
            'down_payment_percent' => 15,
            'maintenance_percent' => 7,
            'installment_years' => 5,
            'installment_type' => 'quarterly',
            'first_installment_date' => now()->toDateString(),
        ]);

        $this->assertFalse($result['discount_plan_applied']);
    }
}
