<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Crm\CrmPipeline;
use App\Models\Crm\CrmStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmStage>
 */
class CrmStageFactory extends Factory
{
    protected $model = CrmStage::class;

    public function definition(): array
    {
        return [
            'pipeline_id' => CrmPipeline::factory(),
            'name' => fake()->randomElement(['New', 'Contacted', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost']),
            'color' => fake()->hexColor(),
            'probability' => fake()->numberBetween(0, 100),
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
        ];
    }
}
