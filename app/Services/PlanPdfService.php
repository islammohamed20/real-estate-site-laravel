<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\Customer;
use App\Models\InstallmentPlan;
use App\Models\Offer;
use App\Models\Unit;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class PlanPdfService
{
    /**
     * Render a saved installment plan to PDF bytes.
     *
     * @return array{content: string, filename: string}
     */
    public function renderPdf(InstallmentPlan $plan): array
    {
        $schedule = $plan->schedule_json ?? [];
        $finalPrice = (float) $plan->final_price;
        $downPayment = (float) $plan->down_payment;
        $isCash = count($schedule) === 0;

        $result = [
            'is_cash' => $isCash,
            'base_price' => $plan->base_price,
            'excellence_percent' => 0,
            'excellence_amount' => 0,
            'base_price_with_excellence' => (float) $plan->base_price,
            'discount_amount' => $plan->discount_amount,
            'final_price' => $finalPrice,
            'maintenance_percent' => $finalPrice > 0 ? round((float) $plan->maintenance_deposit / $finalPrice * 100, 2) : 0,
            'maintenance_deposit' => $plan->maintenance_deposit,
            'remaining' => $plan->remaining_amount,
            'installment_amount' => $plan->installment_amount,
            'down_payment' => $downPayment,
            'schedule' => $schedule,
        ];

        $input = [
            'down_payment_percent' => $finalPrice > 0 ? round($downPayment / $finalPrice * 100, 1) : 0,
            'customer_id' => $plan->customer_id,
            'offer_id' => $plan->offer_id,
        ];

        $company = CompanyProfile::query()->first();
        $unit = $plan->unit_id ? Unit::query()->with(['project', 'building', 'floor'])->find($plan->unit_id) : null;
        $customer = $plan->customer_id ? Customer::query()->find($plan->customer_id) : null;
        $offer = $plan->offer_id ? Offer::query()->find($plan->offer_id) : null;

        $html = view('installments.pdf', [
            'input' => $input,
            'result' => $result,
            'company' => $company,
            'logoDataUri' => $this->imageDataUri($company?->logo_dark_path ?? $company?->logo_path),
            'stampDataUri' => $this->imageDataUri($company?->stamp_path),
            'unit' => $unit,
            'customer' => $customer,
            'offer' => $offer,
        ])->render();

        $defaultConfig = (new ConfigVariables)->getDefaults();
        $defaultFontConfig = (new FontVariables)->getDefaults();

        $isArabic = app()->getLocale() === 'ar';
        $tempDir = storage_path('mpdf-temp');

        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'tempDir' => $tempDir,
            'fontDir' => array_merge($defaultConfig['fontDir'], [public_path('fonts')]),
            'fontdata' => $defaultFontConfig['fontdata'] + [
                'tajawal' => [
                    'R' => 'Tajawal-Regular.ttf',
                    'B' => 'Tajawal-Bold.ttf',
                ],
            ],
            'default_font' => 'tajawal',
            'default_font_size' => 12,
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->SetDirectionality($isArabic ? 'rtl' : 'ltr');
        $mpdf->SetFooter($isArabic ? '{PAGENO} / {nb}' : '{PAGENO} / {nb}');
        $mpdf->WriteHTML($html);

        return [
            'content' => $mpdf->Output('', 'S'),
            'filename' => 'installment-plan-'.$plan->id.'-'.now()->format('Ymd-His').'.pdf',
        ];
    }

    private function imageDataUri(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $file = $path ? public_path(ltrim($path, '/')) : null;

        if ($file !== null && is_file($file) && is_readable($file)) {
            $mime = function_exists('mime_content_type') ? mime_content_type($file) : 'image/png';

            return 'data:'.($mime ?: 'image/png').';base64,'.base64_encode((string) file_get_contents($file));
        }

        return null;
    }
}
