<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Building;
use App\Models\Phase;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'phase_id' => Phase::factory(),
            'name' => fake()->unique()->word().' Tower',
            'code' => strtoupper(fake()->unique()->bothify('BLD-##')),
            'status' => 'published',
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
