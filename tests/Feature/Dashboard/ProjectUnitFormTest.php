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

class ProjectUnitFormTest extends TestCase
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

    public function test_unit_create_form_renders_without_error(): void
    {
        $project = Project::factory()->create();
        $building = Building::factory()->create(['project_id' => $project->id]);
        Floor::factory()->create(['building_id' => $building->id]);

        $this->actingAs($this->user)
            ->get(route('dashboard.projects.units.create', $project))
            ->assertOk()
            ->assertViewIs('dashboard.projects.units.form');
    }

    public function test_unit_edit_form_renders_without_error(): void
    {
        $project = Project::factory()->create();
        $building = Building::factory()->create(['project_id' => $project->id]);
        $floor = Floor::factory()->create(['building_id' => $building->id]);
        $unit = Unit::factory()->create(['project_id' => $project->id, 'building_id' => $building->id, 'floor_id' => $floor->id]);

        $this->actingAs($this->user)
            ->get(route('dashboard.projects.units.edit', [$project, $unit]))
            ->assertOk()
            ->assertViewIs('dashboard.projects.units.form');
    }
}
