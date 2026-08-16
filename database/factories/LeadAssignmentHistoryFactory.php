<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Lead;
use App\Models\LeadAssignmentHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadAssignmentHistory>
 */
class LeadAssignmentHistoryFactory extends Factory
{
    protected $model = LeadAssignmentHistory::class;

    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'from_user_id' => User::factory(),
            'to_user_id' => User::factory(),
            'assigned_by' => User::factory(),
            'notes' => fake()->optional()->sentence(),
            'assigned_at' => now(),
        ];
    }
}
