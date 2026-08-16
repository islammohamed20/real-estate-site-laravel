<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'assigned_to' => User::factory(),
            'created_by' => User::factory(),
            'priority' => 'normal',
            'status' => 'open',
            'due_at' => now()->addDays(3),
            'completed_at' => null,
        ];
    }
}
