<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmOrganization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmContact>
 */
class CrmContactFactory extends Factory
{
    protected $model = CrmContact::class;

    public function definition(): array
    {
        return [
            'organization_id' => CrmOrganization::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'mobile' => fake()->optional()->phoneNumber(),
            'job_title' => fake()->optional()->jobTitle(),
            'source' => fake()->optional()->randomElement(['Website', 'Referral', 'Call', 'Walk-in']),
            'is_primary' => false,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
