<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LeadSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadSource>
 */
class LeadSourceFactory extends Factory
{
    protected $model = LeadSource::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'color' => fake()->hexColor(),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
            'is_default' => false,
        ];
    }
}
