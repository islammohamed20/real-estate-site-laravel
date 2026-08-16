<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Crm\CrmActivity;
use App\Models\Crm\CrmDeal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmActivity>
 */
class CrmActivityFactory extends Factory
{
    protected $model = CrmActivity::class;

    public function definition(): array
    {
        $type = fake()->randomElement(CrmActivity::types());
        $due = fake()->optional()->dateTimeBetween('-1 month', '+1 month');

        $completed = null;
        if ($due && fake()->boolean(60)) {
            $end = clone $due;
            $end->modify('+7 days');
            $completed = fake()->dateTimeBetween($due, $end);
        }

        return [
            'deal_id' => CrmDeal::factory(),
            'contact_id' => null,
            'created_by' => User::inRandomOrder()->first()?->id ?? 1,
            'activityable_id' => fn (array $attrs) => $attrs['deal_id'],
            'activityable_type' => CrmDeal::class,
            'type' => $type,
            'subject' => fake()->optional()->sentence(),
            'body' => fake()->optional()->paragraph(),
            'due_at' => $due,
            'completed_at' => $completed,
            'outcome' => fake()->optional()->word(),
            'duration' => fake()->optional()->randomElement(['5m', '15m', '30m', '1h']),
        ];
    }
}
