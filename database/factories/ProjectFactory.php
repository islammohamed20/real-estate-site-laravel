<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->company().' Residences';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => strtoupper(fake()->unique()->bothify('PRJ-####')),
            'description' => fake()->paragraph(),
            'location' => fake()->address(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'status' => 'published',
            'featured' => fake()->boolean(20),
            'sort_order' => fake()->numberBetween(0, 100),
            'published_at' => now(),
        ];
    }
}
