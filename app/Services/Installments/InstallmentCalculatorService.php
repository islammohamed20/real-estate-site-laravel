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

        $basePrice = ($area * $pricePerMeter) + $gardenPrice + $roofPrice;
        $excellenceAmount = round(($basePrice * $excellencePercent) / 100, 2);
        $basePriceWithExcellence = $basePrice + $excellenceAmount;

        // Discount is priced on the meter rate, but shown to the client as a
        // discount on the total unit price (no mention of the meter basis).
        $discountPercent = $this->discountPercentForTerm($isCash, $installmentYears);
        $meterSubtotal = $area * $pricePerMeter;
        $discountAmount = round($meterSubtotal * $discountPercent / 100, 2);
        $finalPrice = max(0, $basePriceWithExcellence - $discountAmount);

        // Extra down-payment discount (the essential one): applies to installments
        // whenever the down payment exceeds 10% — each point above 10% is deducted
        // from the net total, capped at 20% (reached at a 30% down payment).
        // The term discount below is the optional one (0% at 4+ years).
        $downPaymentBonusPercent = $isCash ? 0.0 : max(0.0, min($downPaymentPercent - 10, 20));
        $downPaymentDiscount = round($finalPrice * $downPaymentBonusPercent / 100, 2);
        $finalPrice = max(0, $finalPrice - $downPaymentDiscount);

        if ($isCash) {
            // Cash: the full discounted price is paid upfront.
            $downPayment = $finalPrice;
            $remaining = 0.0;
        } else {
            if ($downPayment <= 0 && $downPaymentPercent > 0) {
                $downPayment = round(($finalPrice * $downPaymentPercent) / 100, 2);
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
            'down_payment_bonus_percent' => round($downPaymentBonusPercent, 2),
            'down_payment_discount' => round($downPaymentDiscount, 2),
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

    /**
     * The discount rate is priced on the meter rate and depends on the payment
     * term: the shorter the term, the bigger the discount; cash gets the max.
     */
    private function discountPercentForTerm(bool $isCash, float $installmentYears): float
    {
        if ($isCash) {
            return 27.55;
        }

        if ($installmentYears >= 4) {
            return 0;
        }

        if ($installmentYears >= 3) {
            return 7.14;
        }

        if ($installmentYears >= 2) {
            return 12.76;
        }

        return 23.47;
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
