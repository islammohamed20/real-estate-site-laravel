<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\InstallmentFrequency;
use App\Models\InstallmentTemplate;
use Illuminate\Database\Seeder;

class InstallmentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        InstallmentTemplate::query()->updateOrCreate(
            ['code' => 'standard-10-down-16-q'],
            [
                'name' => '10% Down Payment / 16 Quarterly Installments',
                'description' => 'Standard payment plan with 10% down payment and 16 quarterly installments.',
                'down_payment_percent' => 10,
                'installment_count' => 16,
                'installment_frequency' => InstallmentFrequency::Quarterly,
                'maintenance_percent' => 7,
                'discount_percent' => 0,
                'first_installment_offset_months' => 0,
                'is_default' => true,
                'is_active' => true,
                'defaults_json' => [
                    'maintenance_percent' => 7,
                    'installment_type' => InstallmentFrequency::Quarterly->value,
                ],
            ]
        );
    }
}
