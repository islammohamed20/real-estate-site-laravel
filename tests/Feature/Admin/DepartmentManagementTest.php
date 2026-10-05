<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('Administrator');
    }

    public function test_administrator_can_manage_departments(): void
    {
        $this->actingAs($this->admin)
            ->post(route('dashboard.departments.store'), [
                'name' => 'Legal',
                'description' => 'Contracts department',
                'is_active' => 1,
            ])->assertRedirect();

        $department = Department::query()->where('name', 'Legal')->firstOrFail();
        $this->put(route('dashboard.departments.update', $department), [
            'name' => 'Legal Affairs',
            'description' => 'Contracts and legal affairs',
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('departments', ['id' => $department->id, 'name' => 'Legal Affairs']);
        $this->delete(route('dashboard.departments.destroy', $department))->assertRedirect();
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_department_can_be_assigned_when_creating_user_and_cannot_be_deleted_while_used(): void
    {
        $department = Department::query()->create(['name' => 'Operations', 'is_active' => true]);

        $this->actingAs($this->admin)->post(route('dashboard.users.store'), [
            'name' => 'Operations User',
            'email' => 'operations@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Sales Executive',
            'department_id' => $department->id,
            'is_active' => 1,
            'permissions' => [],
            'dashboard_sections' => ['dashboard'],
        ])->assertRedirect(route('dashboard.users.index'));

        $user = User::query()->where('email', 'operations@example.com')->firstOrFail();
        $this->assertSame($department->id, $user->department_id);
        $this->assertSame('Operations', $user->department);

        $this->delete(route('dashboard.departments.destroy', $department))
            ->assertSessionHasErrors('department');
        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }
}
