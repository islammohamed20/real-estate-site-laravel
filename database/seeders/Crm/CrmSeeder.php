<?php

declare(strict_types=1);

namespace Database\Seeders\Crm;

use App\Models\Crm\CrmActivity;
use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmDeal;
use App\Models\Crm\CrmOrganization;
use App\Models\Crm\CrmPipeline;
use App\Models\Crm\CrmStage;
use App\Models\LeadSource;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class CrmSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first() ?? User::factory()->create(['email' => 'admin@venecia-dev.com']);

        $this->seedLeadSources();
        $this->seedTags();

        $pipeline = CrmPipeline::firstOrCreate(
            ['slug' => 'real-estate-sales'],
            [
                'name' => 'Real Estate Sales',
                'color' => '#6366f1',
                'is_default' => true,
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $stageNames = [
            ['name' => 'New', 'color' => '#94a3b8', 'probability' => 10, 'sort_order' => 1],
            ['name' => 'Contacted', 'color' => '#60a5fa', 'probability' => 25, 'sort_order' => 2],
            ['name' => 'Qualified', 'color' => '#22c55e', 'probability' => 50, 'sort_order' => 3],
            ['name' => 'Proposal', 'color' => '#f59e0b', 'probability' => 70, 'sort_order' => 4],
            ['name' => 'Negotiation', 'color' => '#f97316', 'probability' => 90, 'sort_order' => 5],
            ['name' => 'Won', 'color' => '#10b981', 'probability' => 100, 'sort_order' => 6],
            ['name' => 'Lost', 'color' => '#ef4444', 'probability' => 0, 'sort_order' => 7],
        ];

        foreach ($stageNames as $data) {
            CrmStage::firstOrCreate(
                ['pipeline_id' => $pipeline->id, 'name' => $data['name']],
                array_merge($data, ['is_active' => true])
            );
        }

        $openStages = $pipeline->stages()->whereIn('name', ['New', 'Contacted', 'Qualified', 'Proposal', 'Negotiation'])->get();

        CrmOrganization::factory()
            ->count(8)
            ->has(CrmContact::factory()->count(rand(1, 3)), 'contacts')
            ->create()
            ->each(function (CrmOrganization $org) use ($pipeline, $openStages, $admin): void {
                $contact = $org->contacts->first();

                CrmDeal::factory()
                    ->count(rand(1, 3))
                    ->create([
                        'pipeline_id' => $pipeline->id,
                        'stage_id' => $openStages->random()->id,
                        'organization_id' => $org->id,
                        'contact_id' => $contact?->id,
                        'assigned_to' => $admin->id,
                        'created_by' => $admin->id,
                    ])
                    ->each(function (CrmDeal $deal) use ($admin, $contact): void {
                        if ($contact) {
                            $deal->contacts()->attach($contact->id, ['is_primary' => true]);
                        }

                        CrmActivity::factory()
                            ->count(rand(1, 5))
                            ->create([
                                'deal_id' => $deal->id,
                                'contact_id' => $contact?->id,
                                'created_by' => $admin->id,
                                'activityable_id' => $deal->id,
                                'activityable_type' => CrmDeal::class,
                            ]);
                    });
            });

        CrmPipeline::factory()->count(2)->create()->each(function (CrmPipeline $p): void {
            CrmStage::factory()->count(rand(4, 7))->create(['pipeline_id' => $p->id]);
        });
    }

    private function seedLeadSources(): void
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
            ['name' => 'Other', 'color' => '#94a3b8', 'sort_order' => 99],
        ];

        foreach ($sources as $data) {
            LeadSource::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['is_active' => true])
            );
        }
    }

    private function seedTags(): void
    {
        $tags = [
            ['name' => 'Hot Lead', 'color' => '#ef4444'],
            ['name' => 'VIP', 'color' => '#f59e0b'],
            ['name' => 'Investor', 'color' => '#22c55e'],
            ['name' => 'Urgent', 'color' => '#dc2626'],
            ['name' => 'High Budget', 'color' => '#8b5cf6'],
            ['name' => 'First Home', 'color' => '#3b82f6'],
            ['name' => 'Follow-up', 'color' => '#64748b'],
            ['name' => 'Interested', 'color' => '#10b981'],
            ['name' => 'Broker', 'color' => '#94a3b8'],
        ];

        foreach ($tags as $data) {
            Tag::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, ['is_active' => true, 'sort_order' => 0])
            );
        }
    }
}
