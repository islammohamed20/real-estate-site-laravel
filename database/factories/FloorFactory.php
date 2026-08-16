<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Phase;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Floor>
 */
class FloorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'phase_id' => Phase::factory(),
            'building_id' => Building::factory(),
            'number' => fake()->numberBetween(1, 20),
            'name' => fake()->optional()->word().' Floor',
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
