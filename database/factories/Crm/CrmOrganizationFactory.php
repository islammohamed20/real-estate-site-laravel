<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Crm\CrmOrganization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmOrganization>
 */
class CrmOrganizationFactory extends Factory
{
    protected $model = CrmOrganization::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'industry' => fake()->optional()->randomElement(['Real Estate', 'Construction', 'Investment', 'Technology']),
            'website' => fake()->optional()->url(),
            'phone' => fake()->optional()->phoneNumber(),
            'email' => fake()->optional()->companyEmail(),
            'address' => fake()->optional()->address(),
            'city' => fake()->optional()->city(),
            'country' => fake()->optional()->country(),
            'tax_id' => fake()->optional()->regexify('[A-Z0-9]{10}'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
