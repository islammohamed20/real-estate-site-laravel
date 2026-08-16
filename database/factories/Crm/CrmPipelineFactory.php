<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Crm\CrmPipeline;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CrmPipeline>
 */
class CrmPipelineFactory extends Factory
{
    protected $model = CrmPipeline::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'color' => fake()->hexColor(),
            'is_default' => false,
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
        ];
    }
}
