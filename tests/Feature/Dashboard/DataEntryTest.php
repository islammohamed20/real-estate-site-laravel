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

class DataEntryTest extends TestCase
{
    use RefreshDatabase;

    private User $dataEntry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->dataEntry = User::factory()->create(['is_active' => true]);
        $this->dataEntry->assignRole('Data Entry');
    }

    public function test_data_entry_can_access_project_index(): void
    {
        $this->actingAs($this->dataEntry)
            ->get(route('dashboard.projects.index'))
            ->assertOk();
    }

    public function test_data_entry_can_create_project(): void
    {
        $this->actingAs($this->dataEntry)
            ->post(route('dashboard.projects.store'), [
                'name' => 'Data Entry Project',
                'code' => 'DEP',
                'price_per_meter' => 10000,
                'location' => 'Cairo',
                'city' => 'Cairo',
                'country' => 'Egypt',
                'status' => 'active',
                'buildings' => [],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('projects', ['name' => 'Data Entry Project']);
    }

    public function test_data_entry_can_delete_and_see_own_trashed_project(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->dataEntry)
            ->from(route('dashboard.projects.index'))
            ->delete(route('dashboard.projects.destroy', $project))
            ->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'deleted_by' => $this->dataEntry->id,
        ]);

        $this->actingAs($this->dataEntry)
            ->get(route('dashboard.trash.index'))
            ->assertOk()
            ->assertViewHas('trashedProjects', fn ($projects) => $projects->contains('id', $project->id))
            ->assertSee($project->name, false);
    }

    public function test_data_entry_can_restore_own_trashed_project(): void
    {
        $project = Project::factory()->create();

        $this->actingAs($this->dataEntry)
            ->delete(route('dashboard.projects.destroy', $project));

        $this->actingAs($this->dataEntry)
            ->post(route('dashboard.trash.projects.restore', $project))
            ->assertRedirect();

        $this->assertFalse($project->fresh()->trashed());
    }

    public function test_data_entry_can_delete_and_see_own_trashed_unit(): void
    {
        $project = Project::factory()->create();
        $building = Building::factory()->create(['project_id' => $project->id]);
        $floor = Floor::factory()->create(['building_id' => $building->id, 'project_id' => $project->id]);
        $unit = Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
        ]);

        $this->actingAs($this->dataEntry)
            ->delete(route('dashboard.projects.units.destroy', [$project, $unit]))
            ->assertRedirect();

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'deleted_by' => $this->dataEntry->id,
        ]);

        $this->actingAs($this->dataEntry)
            ->get(route('dashboard.trash.index'))
            ->assertOk()
            ->assertSee((string) $unit->unit_number, false);
    }

    public function test_data_entry_cannot_restore_other_users_trashed_project(): void
    {
        $otherUser = User::factory()->create(['is_active' => true]);
        $otherUser->assignRole('Data Entry');

        $project = Project::factory()->create();

        $this->actingAs($otherUser)
            ->delete(route('dashboard.projects.destroy', $project));

        $this->actingAs($this->dataEntry)
            ->post(route('dashboard.trash.projects.restore', $project))
            ->assertForbidden();
    }

    public function test_data_entry_can_update_own_profile(): void
    {
        $this->actingAs($this->dataEntry)
            ->put(route('dashboard.profile.update'), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'phone' => '01234567890',
                'job_title' => 'Data Entry Specialist',
                'department' => 'Operations',
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $this->dataEntry->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_data_entry_cannot_access_crm_trash(): void
    {
        $this->actingAs($this->dataEntry)
            ->get(route('dashboard.crm.trash.index'))
            ->assertForbidden();
    }

    public function test_data_entry_cannot_access_users_management(): void
    {
        $this->actingAs($this->dataEntry)
            ->get(route('dashboard.users.index'))
            ->assertForbidden();
    }
}
