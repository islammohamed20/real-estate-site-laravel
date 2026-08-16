<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmDeal;
use App\Models\Crm\CrmOrganization;
use App\Models\Crm\CrmPipeline;
use App\Models\Crm\CrmStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmDeal>
 */
class CrmDealFactory extends Factory
{
    protected $model = CrmDeal::class;

    public function definition(): array
    {
        $pipeline = CrmPipeline::inRandomOrder()->first() ?? CrmPipeline::factory()->create();
        $stage = $pipeline->stages()->inRandomOrder()->first() ?? CrmStage::factory()->create(['pipeline_id' => $pipeline->id]);

        return [
            'title' => fake()->sentence(3),
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
            'organization_id' => CrmOrganization::factory(),
            'contact_id' => CrmContact::factory(),
            'assigned_to' => User::inRandomOrder()->first()?->id ?? 1,
            'created_by' => User::inRandomOrder()->first()?->id ?? 1,
            'value' => fake()->randomFloat(2, 50000, 5000000),
            'currency_code' => 'USD',
            'expected_close_date' => fake()->optional()->dateTimeBetween('now', '+6 months'),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'source' => fake()->randomElement(['Website', 'Referral', 'Call', 'Walk-in']),
            'description' => fake()->optional()->paragraph(),
            'status' => 'open',
            'stage_changed_at' => now(),
            'closed_at' => null,
        ];
    }
}
