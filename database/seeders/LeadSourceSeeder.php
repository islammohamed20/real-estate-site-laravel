<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            ['name' => 'Website', 'color' => '#3b82f6', 'sort_order' => 1, 'is_default' => true],
            ['name' => 'Facebook', 'color' => '#1877f2', 'sort_order' => 2],
            ['name' => 'Instagram', 'color' => '#e1306c', 'sort_order' => 3],
            ['name' => 'WhatsApp', 'color' => '#22c55e', 'sort_order' => 4],
            ['name' => 'Google', 'color' => '#ea4335', 'sort_order' => 5],
            ['name' => 'Referral', 'color' => '#8b5cf6', 'sort_order' => 6],
            ['name' => 'Walk-in', 'color' => '#f59e0b', 'sort_order' => 7],
            ['name' => 'Phone', 'color' => '#10b981', 'sort_order' => 8],
            ['name' => 'Broker', 'color' => '#64748b', 'sort_order' => 9],
            ['name' => 'Campaign', 'color' => '#ec4899', 'sort_order' => 10],
            ['name' => 'Social Media', 'color' => '#a855f7', 'sort_order' => 11],
            ['name' => 'Event', 'color' => '#f43f5e', 'sort_order' => 12],
            ['name' => 'Other', 'color' => '#94a3b8', 'sort_order' => 99],
        ];

        foreach ($sources as $data) {
            LeadSource::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
