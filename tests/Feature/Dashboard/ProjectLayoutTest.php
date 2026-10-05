<?php

namespace Tests\Feature\Dashboard;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_project_layout(): void
    {
        $role = Role::findOrCreate('Administrator', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($role);

        $project = Project::factory()->create(['name' => 'مشروع فينسيا التجريبي']);
        $building = Building::factory()->create(['project_id' => $project->id, 'name' => 'عمارة أ1']);
        $floor = Floor::factory()->create(['project_id' => $project->id, 'building_id' => $building->id, 'number' => 1]);
        Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'unit_number' => '101',
            'status' => 'available',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.projects.layout', $project))
            ->assertOk()
            ->assertSee('مشروع فينسيا التجريبي')
            ->assertSee('مخطط الحصر الشامل')
            ->assertDontSee(__('Apply to all units'))
            ->assertSee(__('Apply to all floor units'))
            ->assertSee(__('Unit status'));
    }

    public function test_mobile_users_are_redirected_from_project_layout(): void
    {
        $role = Role::findOrCreate('Administrator', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($role);

        $project = Project::factory()->create();

        $this->actingAs($admin)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15'])
            ->get(route('dashboard.projects.layout', $project))
            ->assertRedirect(route('dashboard.projects.edit', $project));
    }

    public function test_admin_can_bulk_update_floor_unit_status_and_visibility(): void
    {
        $role = Role::findOrCreate('Administrator', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($role);

        $project = Project::factory()->create();
        $building = Building::factory()->create(['project_id' => $project->id]);
        $floor = Floor::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'number' => 2,
        ]);
        $otherFloor = Floor::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'number' => 1,
        ]);

        $targetUnits = Unit::factory()->count(2)->create([
            'project_id' => $project->id,
            'phase_id' => null,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'status' => 'available',
            'hidden_from_website' => false,
        ]);
        $otherUnit = Unit::factory()->create([
            'project_id' => $project->id,
            'phase_id' => null,
            'building_id' => $building->id,
            'floor_id' => $otherFloor->id,
            'status' => 'available',
            'hidden_from_website' => false,
        ]);

        $this->actingAs($admin)
            ->postJson(route('dashboard.projects.floors.units.status', [$project, $floor]), [
                'status' => 'reserved',
                'hidden_from_website' => 1,
            ])
            ->assertOk()
            ->assertJson(['message' => __('Floor units status updated successfully.')]);

        foreach ($targetUnits as $unit) {
            $this->assertDatabaseHas('units', [
                'id' => $unit->id,
                'status' => 'reserved',
                'hidden_from_website' => true,
            ]);
        }

        $this->assertDatabaseHas('units', [
            'id' => $otherUnit->id,
            'status' => 'available',
            'hidden_from_website' => false,
        ]);
    }

    public function test_sold_units_remain_visible_on_the_public_website(): void
    {
        $role = Role::findOrCreate('Administrator', 'web');
        $admin = User::factory()->create();
        $admin->assignRole($role);

        $project = Project::factory()->create();
        $building = Building::factory()->create(['project_id' => $project->id]);
        $floor = Floor::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'number' => 1,
        ]);
        $unit = Unit::factory()->create([
            'project_id' => $project->id,
            'phase_id' => null,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'status' => 'available',
            'hidden_from_website' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('dashboard.projects.units.status', [$project, $unit]), [
                'status' => 'sold',
                'hidden_from_website' => 1,
            ])
            ->assertRedirect(route('dashboard.projects.layout', $project))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'status' => 'sold',
            'hidden_from_website' => false,
        ]);
    }
}
