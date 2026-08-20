<?php

declare(strict_types=1);

namespace App\Services\Installments;

use App\Enums\InstallmentFrequency;
use App\Models\InstallmentTemplate;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class InstallmentCalculatorService
{
    public function calculate(array $input): array
    {
        $area = (float) Arr::get($input, 'area', 0);
        $pricePerMeter = (float) Arr::get($input, 'price_per_meter', 0);
        $gardenPrice = (float) Arr::get($input, 'garden_price', 0);
        $roofPrice = (float) Arr::get($input, 'roof_price', 0);
        $excellencePercent = (float) Arr::get($input, 'excellence_percent', 0);
        $downPayment = (float) Arr::get($input, 'down_payment', 0);
        $downPaymentPercent = (float) Arr::get($input, 'down_payment_percent', 0);
        $maintenancePercent = (float) Arr::get($input, 'maintenance_percent', 0);
        $installmentYears = (float) Arr::get($input, 'installment_years', 0);
        $paymentMethod = Arr::get($input, 'payment_method', 'installments');
        $isCash = $paymentMethod === 'cash';
        $frequency = InstallmentFrequency::from(Arr::get($input, 'installment_type', InstallmentFrequency::Monthly->value));
        $firstInstallmentDate = CarbonImmutable::parse(Arr::get($input, 'first_installment_date', now()->toDateString()));

        $installmentCount = $isCash
            ? 0
            : ($installmentYears > 0
                ? (int) round($installmentYears * 12 / $frequency->monthsPerInstallment())
                : (int) Arr::get($input, 'installment_count', 0));

        // Excel model: unit price = area × (price/m² + discrimination% × price/m²),
        // with garden/roof as local extensions.
        $basePrice = ($area * $pricePerMeter) + $gardenPrice + $roofPrice;
        $excellenceAmount = round(($basePrice * $excellencePercent) / 100, 2);
        $basePriceWithExcellence = $basePrice + $excellenceAmount;

        // Down payment as a percent of the unit price (Excel semantics: the
        // entered amount is compared against the unit price, e.g. 1,776,000 on
        // a 3,552,000 unit = 50%). Cash = 100% paid upfront.
        if ($isCash) {
            $downPaymentPercent = 100.0;
        } elseif ($downPayment > 0 && $basePriceWithExcellence > 0) {
            $downPaymentPercent = $downPayment / $basePriceWithExcellence * 100;
        }

        // Excel discount: every point of down payment above the 10% baseline
        // earns a 30% discount point — e.g. 50% down → (50−10)×30% = 12% discount.
        $downPaymentBonusPercent = max(0.0, $downPaymentPercent - 10) * 0.30;
        $discountPercent = $downPaymentBonusPercent;
        $discountAmount = round($basePriceWithExcellence * $discountPercent / 100, 2);
        $finalPrice = max(0, $basePriceWithExcellence - $discountAmount);

        $discountPlanApplied = ! $isCash && $downPaymentPercent > 10;
        $discountPlanDiscount = $discountPlanApplied ? round($discountAmount, 2) : 0.0;

        if ($isCash) {
            // Cash: the full discounted price is paid upfront.
            $downPayment = $finalPrice;
            $remaining = 0.0;
        } else {
            if ($downPayment <= 0 && $downPaymentPercent > 0) {
                // Percent-based down payment is computed on the unit price
                // (Excel: D17 = downPaymentPercent × unit price).
                $downPayment = round(($basePriceWithExcellence * $downPaymentPercent) / 100, 2);
            }

            $remaining = max(0, $finalPrice - $downPayment);
        }

        $maintenanceDeposit = round(($finalPrice * $maintenancePercent) / 100, 2);
        $installmentAmount = $installmentCount > 0 ? round($remaining / $installmentCount, 2) : 0.0;

        $schedule = [];
        $runningBalance = $remaining;
        for ($index = 1; $index <= $installmentCount; $index++) {
            $dueDate = $firstInstallmentDate->addMonths(($index - 1) * $frequency->monthsPerInstallment());
            $amount = $index === $installmentCount
                ? round($runningBalance, 2)
                : $installmentAmount;

            $runningBalance = round($runningBalance - $amount, 2);
            $schedule[] = [
                'installment_number' => $index,
                'due_date' => $dueDate->toDateString(),
                'amount' => $amount,
                'balance_after' => max(0, $runningBalance),
            ];
        }

        return [
            'base_price' => round($basePrice, 2),
            'excellence_percent' => round($excellencePercent, 2),
            'excellence_amount' => $excellenceAmount,
            'base_price_with_excellence' => round($basePriceWithExcellence, 2),
            'discount_percent' => round($discountPercent, 2),
            'discount_amount' => round($discountAmount, 2),
            'discount_plan_applied' => $discountPlanApplied,
            'discount_plan_discount' => round($discountPlanDiscount, 2),
            'down_payment_bonus_percent' => round($downPaymentBonusPercent, 2),
            'down_payment_discount' => round($discountAmount, 2),
            'final_price' => round($finalPrice, 2),
            'maintenance_percent' => round($maintenancePercent, 2),
            'maintenance_deposit' => $maintenanceDeposit,
            'down_payment' => round($downPayment, 2),
            'remaining' => round($remaining, 2),
            'installment_amount' => round($installmentAmount, 2),
            'payment_method' => $paymentMethod,
            'is_cash' => $isCash,
            'schedule' => $schedule,
        ];
    }

    public function calculateFromTemplate(InstallmentTemplate $template, array $input): array
    {
        return $this->calculate(array_merge($template->defaults(), $input));
    }

    public function validatePlanInput(array $input): void
    {
        $years = (float) Arr::get($input, 'installment_years', 0);
        $count = (int) Arr::get($input, 'installment_count', 0);

        if ($years <= 0 && $count < 1) {
            throw new InvalidArgumentException('Installment years or count must be at least 1.');
        }
    }
}
