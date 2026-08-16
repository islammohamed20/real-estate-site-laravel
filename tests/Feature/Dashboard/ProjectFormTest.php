<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\Project;
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

        $this->actingAs($this->user)
            ->get(route('dashboard.projects.edit', $project))
            ->assertOk()
            ->assertViewIs('dashboard.projects.form');
    }
}
