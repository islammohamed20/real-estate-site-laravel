<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UnitStatus;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Phase;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'phase_id' => Phase::factory(),
            'building_id' => Building::factory(),
            'floor_id' => Floor::factory(),
            'unit_number' => strtoupper(fake()->bothify('U##-###')),
            'unit_type' => fake()->randomElement(['Apartment', 'Duplex', 'Penthouse']),
            'bedrooms' => fake()->numberBetween(1, 4),
            'bathrooms' => fake()->numberBetween(1, 4),
            'area' => fake()->randomFloat(2, 70, 300),
            'garden_area' => fake()->randomFloat(2, 0, 100),
            'roof_area' => fake()->randomFloat(2, 0, 120),
            'balcony_area' => fake()->randomFloat(2, 0, 40),
            'price_per_meter' => fake()->randomFloat(2, 800, 2500),
            'garden_price' => fake()->randomFloat(2, 0, 20000),
            'roof_price' => fake()->randomFloat(2, 0, 25000),
            'current_price' => fake()->randomFloat(2, 100000, 750000),
            'status' => UnitStatus::Available,
            'featured' => fake()->boolean(15),
            'hidden_from_website' => false,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
