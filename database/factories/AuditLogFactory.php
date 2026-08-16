<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'auditable_type' => User::class,
            'auditable_id' => User::factory(),
            'event' => 'updated',
            'old_values' => [],
            'new_values' => [],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'properties' => [],
        ];
    }
}
