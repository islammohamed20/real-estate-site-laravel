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
            ]
        );
    }
}
