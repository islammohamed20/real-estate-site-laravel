<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\Customer;
use App\Models\InstallmentPlan;
use App\Models\Offer;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;
use Mpdf\MpdfException;

class PlanPdfService
{
    public function __construct(
        private readonly PdfRendererService $renderer,
    ) {}

    /**
     * Render a saved installment plan to PDF bytes.
     *
     * @return array{content: string, filename: string}
     *
     * @throws MpdfException  If PDF generation fails.
     */
    public function renderPdf(InstallmentPlan $plan): array
    {
        $schedule = $plan->schedule_json ?? [];
        $finalPrice = (float) $plan->final_price;
        $downPayment = (float) $plan->down_payment;
        $isCash = count($schedule) === 0;

        // Use the stored values (from the migration) so the saved-plan PDF
        // matches exactly what the customer saw in the calculator.
        $excellencePercent = (float) ($plan->excellence_percent ?? 0);
        $excellenceAmount = (float) ($plan->excellence_amount ?? 0);
        $basePriceWithExcellence = (float) ($plan->base_price_with_excellence ?? $plan->base_price);
        $discountPercent = (float) ($plan->discount_percent ?? 0);

        $result = [
            'is_cash' => $isCash,
            'base_price' => $plan->base_price,
            'excellence_percent' => $excellencePercent,
            'excellence_amount' => $excellenceAmount,
            'base_price_with_excellence' => $basePriceWithExcellence,
            'discount_percent' => $discountPercent,
            'discount_amount' => $plan->discount_amount,
            'final_price' => $finalPrice,
            'maintenance_percent' => $finalPrice > 0 ? round((float) $plan->maintenance_deposit / $finalPrice * 100, 2) : 0,
            'maintenance_deposit' => $plan->maintenance_deposit,
            'remaining' => $plan->remaining_amount,
            'installment_amount' => $plan->installment_amount,
            'down_payment' => $downPayment,
            'schedule' => $schedule,
        ];

        // down_payment_percent must be computed on base_price_with_excellence
        // (same formula as InstallmentCalculatorService), NOT on final_price,
        // so the badge matches the calculator's display.
        $input = [
            'down_payment_percent' => $basePriceWithExcellence > 0
                ? round($downPayment / $basePriceWithExcellence * 100, 1)
                : 0,
            'customer_id' => $plan->customer_id,
            'offer_id' => $plan->offer_id,
        ];

        $company = CompanyProfile::query()->first();
        $unit = $plan->unit_id ? Unit::query()->with(['project', 'building', 'floor'])->find($plan->unit_id) : null;
        $customer = $plan->customer_id ? Customer::query()->find($plan->customer_id) : null;
        $offer = $plan->offer_id ? Offer::query()->find($plan->offer_id) : null;

        $reference = 'PLAN-'.$plan->id.'-'.now()->format('Ymd');

        $html = view('installments.pdf', [
            'input' => $input,
            'result' => $result,
            'company' => $company,
            'reference' => $reference,
            'logoDataUri' => $this->renderer->imageDataUri($company?->logo_dark_path ?? $company?->logo_path),
            'stampDataUri' => $this->renderer->imageDataUri($company?->stamp_path),
            'unit' => $unit,
            'customer' => $customer,
            'offer' => $offer,
        ])->render();

        try {
            $content = $this->renderer->renderHtml($html);
        } catch (MpdfException $e) {
            Log::error('PlanPdfService: PDF generation failed for plan '.$plan->id, [
                'exception' => $e,
            ]);
            throw $e;
        }

        return [
            'content' => $content,
            'filename' => 'installment-plan-'.$plan->id.'-'.now()->format('Ymd-His').'.pdf',
        ];
    }
}
