<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        CompanyProfile::query()->updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Venecia Developments',
                'legal_name' => 'Venecia Developments LLC',
                'currency_code' => 'EGP',
                'default_language' => 'en',
                'maintenance_percent' => 7.00,
                'smtp_from_name' => config('app.name'),
                'smtp_from_email' => config('mail.from.address'),
                'seo_title' => 'Venecia Developments — شركة فينسيا للاستثمار والتطوير العقاري',
                'seo_description' => 'رواد في صناعة التطوير العقاري والمجتمعات السكنية الفاخرة. نقدم حلولا معمارية متكاملة وتصاميم أيقونية بأرقى المواقع وأسهل أنظمة السداد.',
            ]
        );
    }
}
