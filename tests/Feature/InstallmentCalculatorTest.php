<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\Installments\InstallmentCalculatorService;
use Tests\TestCase;

class InstallmentCalculatorTest extends TestCase
{
    public function test_excel_sample_replicates_the_spreadsheet_numbers(): void
    {
        $service = app(InstallmentCalculatorService::class);
        $result = $service->calculate([
            'area' => 185,
            'price_per_meter' => 19200,
            'excellence_percent' => 0,
            'down_payment' => 1776000, // 50% of the unit price
            'maintenance_percent' => 7,
            'installment_count' => 12,
            'installment_type' => 'quarterly',
            'first_installment_date' => now()->toDateString(),
        ]);

        $this->assertSame(3552000.0, (float) $result['base_price_with_excellence']);
        // (50% − 10%) × 30% = 12% discount
        $this->assertSame(12.0, (float) $result['discount_percent']);
        $this->assertSame(426240.0, (float) $result['discount_amount']);
        $this->assertSame(3125760.0, (float) $result['final_price']);
        $this->assertSame(1776000.0, (float) $result['down_payment']);
        $this->assertSame(1349760.0, (float) $result['remaining']);
        $this->assertSame(112480.0, (float) $result['installment_amount']);
        $this->assertCount(12, $result['schedule']);
    }

    public function test_discount_scales_with_the_down_payment_percentage(): void
    {
        $service = app(InstallmentCalculatorService::class);

        // 30% down → (30−10)×30% = 6% discount
        $result = $service->calculate([
            'area' => 100,
            'price_per_meter' => 10000,
            'down_payment_percent' => 30,
            'maintenance_percent' => 7,
            'installment_years' => 4,
            'installment_type' => 'quarterly',
            'first_installment_date' => now()->toDateString(),
        ]);

        $this->assertSame(6.0, (float) $result['discount_percent']);
        $this->assertTrue($result['discount_plan_applied']);
        $this->assertGreaterThan(0, (float) $result['discount_plan_discount']);
    }

    public function test_no_discount_when_down_payment_is_at_or_below_ten_percent(): void
    {
        $service = app(InstallmentCalculatorService::class);

        $result = $service->calculate([
            'area' => 100,
            'price_per_meter' => 10000,
            'down_payment_percent' => 10,
            'maintenance_percent' => 7,
            'installment_years' => 5,
            'installment_type' => 'quarterly',
            'first_installment_date' => now()->toDateString(),
        ]);

        $this->assertSame(0.0, (float) $result['discount_percent']);
        $this->assertFalse($result['discount_plan_applied']);
        $this->assertSame(1000000.0, (float) $result['final_price']);
    }

    public function test_cash_payment_applies_the_full_upfront_discount(): void
    {
        $service = app(InstallmentCalculatorService::class);

        $result = $service->calculate([
            'area' => 100,
            'price_per_meter' => 10000,
            'payment_method' => 'cash',
            'maintenance_percent' => 7,
            'installment_years' => 0,
            'installment_type' => 'quarterly',
            'first_installment_date' => now()->toDateString(),
        ]);

        // Cash = 100% down → (100−10)×30% = 27%
        $this->assertSame(27.0, (float) $result['discount_percent']);
        $this->assertTrue($result['is_cash']);
        $this->assertSame(0.0, (float) $result['remaining']);
        $this->assertSame((float) $result['final_price'], (float) $result['down_payment']);
        $this->assertSame([], $result['schedule']);
    }
}
