<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Phase;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Phase>
 */
class PhaseFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true).' Phase';

        return [
            'project_id' => Project::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'status' => 'published',
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
