<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectInstallmentYearsTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculator_receives_each_project_installment_limit(): void
    {
        $first = Project::factory()->create(['max_installment_years' => 3]);
        $second = Project::factory()->create(['max_installment_years' => 8]);

        $this->get(route('installments.index'))
            ->assertOk()
            ->assertViewHas('projects', fn ($projects) => $projects->find($first->id)->max_installment_years === 3
                && $projects->find($second->id)->max_installment_years === 8);
    }

    public function test_calculator_rejects_a_period_longer_than_the_selected_project_allows(): void
    {
        $project = Project::factory()->create(['max_installment_years' => 3]);

        $this->post(route('installments.calculate'), [
            'project_id' => $project->id,
            'price_per_meter' => 10000,
            'area' => 100,
            'down_payment_percent' => 10,
            'maintenance_percent' => 7,
            'installment_years' => 4,
            'installment_type' => 'quarterly',
            'payment_method' => 'installments',
            'first_installment_date' => now()->toDateString(),
        ])->assertSessionHasErrors('installment_years');
    }
}
