<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SitePlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_plan_uses_architectural_order_and_can_include_hidden_inventory(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Administrator', 'web'));
        $project = Project::factory()->create();

        foreach ([
            ['code' => 'R-403', 'hidden' => true],
            ['code' => 'R-201', 'hidden' => false],
            ['code' => 'R-601', 'hidden' => false],
        ] as $index => $item) {
            $building = Building::factory()->create([
                'project_id' => $project->id,
                'phase_id' => null,
                'code' => $item['code'],
                'hidden_from_website' => $item['hidden'],
            ]);
            $floor = Floor::factory()->create([
                'project_id' => $project->id,
                'building_id' => $building->id,
                'phase_id' => null,
                'number' => 0,
            ]);
            Unit::factory()->create([
                'project_id' => $project->id,
                'phase_id' => null,
                'building_id' => $building->id,
                'floor_id' => $floor->id,
                'unit_number' => 'TEST-'.$index,
                'hidden_from_website' => $item['hidden'],
            ]);
        }

        $this->actingAs($admin)
            ->get(route('dashboard.site-plan', ['project_id' => $project->id]))
            ->assertOk()
            ->assertSeeInOrder(['R-601', 'R-201', 'R-403'])
            ->assertSee('data-visible="0"', false)
            ->assertSee('x-init="$nextTick(() => applyFilters())"', false)
            ->assertSee('data-floor="0"', false)
            ->assertSee('role="group"', false);
    }

    public function test_site_plan_is_restricted_to_project_inventory_roles(): void
    {
        $project = Project::factory()->create();
        $receptionist = User::factory()->create();
        $receptionist->assignRole(Role::findOrCreate('Receptionist', 'web'));

        $this->actingAs($receptionist)
            ->get(route('dashboard.site-plan', ['project_id' => $project->id]))
            ->assertForbidden();

        $this->get(route('dashboard.site-plan.pdf', ['project_id' => $project->id]))
            ->assertForbidden();
    }

    public function test_site_plan_pdf_returns_a_pdf_response(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('Administrator', 'web'));
        $project = Project::factory()->create();
        $building = Building::factory()->create([
            'project_id' => $project->id,
            'phase_id' => null,
            'hidden_from_website' => false,
        ]);
        $floor = Floor::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'phase_id' => null,
            'number' => 0,
        ]);
        Unit::factory()->create([
            'project_id' => $project->id,
            'phase_id' => null,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'hidden_from_website' => false,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.site-plan.pdf', ['project_id' => $project->id]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->assertStringStartsWith('attachment;', (string) $response->headers->get('Content-Disposition'));

        $inlineResponse = $this->get(route('dashboard.site-plan.pdf', [
            'project_id' => $project->id,
            'inline' => 1,
        ]))->assertOk();

        $this->assertStringStartsWith('inline;', (string) $inlineResponse->headers->get('Content-Disposition'));
    }
}
