<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyProfile>
 */
class CompanyProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'legal_name' => fake()->company().' LLC',
            'currency_code' => 'USD',
            'default_language' => 'en',
            'maintenance_percent' => 7,
        ];
    }
}
