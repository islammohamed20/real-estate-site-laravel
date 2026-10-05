<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectFormTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->user = User::factory()->create(['is_active' => true]);
        $this->user->assignRole('Administrator');
    }

    public function test_project_edit_form_renders_without_error(): void
    {
        $project = Project::factory()->create();
        $building = Building::factory()->create(['project_id' => $project->id]);
        Floor::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'number' => 0,
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard.projects.edit', $project))
            ->assertOk()
            ->assertViewIs('dashboard.projects.form')
            ->assertSee('name="max_installment_years"', false)
            ->assertSee('value="4"', false)
            ->assertSee('id="project_map_lat"', false)
            ->assertSee('name="map_lat"', false)
            ->assertSee('id="project_map_lng"', false)
            ->assertSee('name="map_lng"', false)
            ->assertSee('id="project-map-iframe"', false)
            ->assertDontSee(__('Apply to all units'));
    }

    public function test_project_update_saves_map_coordinates(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->user)
            ->put(route('dashboard.projects.update', $project), [
                'name' => $project->name,
                'slug' => $project->slug,
                'code' => $project->code,
                'price_per_meter' => 10000,
                'max_installment_years' => 4,
                'location' => 'Cairo',
                'city' => 'Cairo',
                'country' => 'Egypt',
                'map_lat' => '31.2357000',
                'map_lng' => '30.0444000',
                'status' => 'active',
                'buildings' => [],
            ])
            ->assertRedirect();

        $project->refresh();

        $this->assertSame('31.2357000', $project->map_lat);
        $this->assertSame('30.0444000', $project->map_lng);
    }

    public function test_administrator_can_open_project_3d_layout(): void
    {
        $project = Project::factory()->create();
        $building = Building::factory()->create(['project_id' => $project->id]);
        $floor = Floor::factory()->create(['project_id' => $project->id, 'building_id' => $building->id]);
        Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard.projects.layout', $project))
            ->assertOk()
            ->assertViewIs('dashboard.projects.layout')
            ->assertSee('Site Plan')
            ->assertSee($building->name)
            ->assertSee('Unit Inspector');
    }

    public function test_sales_manager_can_open_project_3d_layout(): void
    {
        $salesManager = User::factory()->create(['is_active' => true]);
        $salesManager->assignRole('Sales Manager');
        $project = Project::factory()->create();

        $this->actingAs($salesManager)
            ->get(route('dashboard.projects.layout', $project))
            ->assertOk()
            ->assertViewIs('dashboard.projects.layout');
    }

    public function test_accountant_can_open_project_3d_layout(): void
    {
        $accountant = User::factory()->create(['is_active' => true]);
        $accountant->assignRole('Accountant');
        $project = Project::factory()->create();
        $building = Building::factory()->create(['project_id' => $project->id]);
        $floor = Floor::factory()->create(['project_id' => $project->id, 'building_id' => $building->id]);
        Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
        ]);

        $this->actingAs($accountant)
            ->get(route('dashboard.projects.layout', $project))
            ->assertOk()
            ->assertViewIs('dashboard.projects.layout');
    }

    public function test_project_3d_layout_is_not_available_to_viewer(): void
    {
        $viewer = User::factory()->create(['is_active' => true]);
        $viewer->assignRole('Viewer');
        $project = Project::factory()->create();

        $this->actingAs($viewer)
            ->get(route('dashboard.projects.layout', $project))
            ->assertForbidden();
    }

    public function test_owner_can_edit_existing_projects_but_cannot_create_projects(): void
    {
        $owner = User::factory()->create(['is_active' => true]);
        $owner->assignRole('Owner');
        $project = Project::factory()->create();

        $this->actingAs($owner)
            ->get(route('dashboard.projects.edit', $project))
            ->assertOk();

        $this->actingAs($owner)
            ->get(route('dashboard.projects.create'))
            ->assertForbidden();
    }

    public function test_accountant_can_view_project_index_and_edit_project(): void
    {
        $accountant = User::factory()->create(['is_active' => true]);
        $accountant->assignRole('Accountant');
        $project = Project::factory()->create();

        $this->actingAs($accountant)
            ->get(route('dashboard.projects.index'))
            ->assertOk();

        $this->actingAs($accountant)
            ->get(route('dashboard.projects.edit', $project))
            ->assertOk();
    }

    public function test_accountant_cannot_create_or_delete_project(): void
    {
        $accountant = User::factory()->create(['is_active' => true]);
        $accountant->assignRole('Accountant');
        $project = Project::factory()->create();

        $this->actingAs($accountant)
            ->get(route('dashboard.projects.create'))
            ->assertForbidden();

        $this->actingAs($accountant)
            ->delete(route('dashboard.projects.destroy', $project))
            ->assertForbidden();
    }
}
