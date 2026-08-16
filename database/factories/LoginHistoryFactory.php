<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoginHistory>
 */
class LoginHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'device_name' => 'Web Browser',
            'device_type' => 'desktop',
            'location' => fake()->city(),
            'is_successful' => true,
            'logged_in_at' => now(),
            'logged_out_at' => now()->addHours(2),
        ];
    }
}
