<?php

declare(strict_types=1);

namespace Database\Seeders;

use Database\Seeders\Crm\CrmSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            CompanyProfileSeeder::class,
            InstallmentTemplateSeeder::class,
            CrmSeeder::class,
            WhatsAppMessageTemplateSeeder::class,
            HomeSectionSeeder::class,
            EmailTemplateSeeder::class,
        ]);
    }
}
