<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_edit_user_details(): void
    {
        $this->seed(PermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrator');
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'is_active' => true,
        ]);
        $user->assignRole('Sales Executive');

        $this->actingAs($admin)
            ->get(route('dashboard.users.edit', $user))
            ->assertOk()
            ->assertViewIs('users.form')
            ->assertSee('Old Name');

        $this->put(route('dashboard.users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '01000000000',
            'job_title' => 'Senior Sales Executive',
            'department' => 'Sales',
            'role' => 'Sales Manager',
            'is_active' => 1,
            'permissions' => [],
            'dashboard_sections' => ['dashboard', 'projects', 'tools'],
        ])->assertRedirect(route('dashboard.users.index'));

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertSame('01000000000', $user->phone);
        $this->assertSame('Senior Sales Executive', $user->job_title);
        $this->assertSame('Sales', $user->department);
        $this->assertTrue($user->hasRole('Sales Manager'));
        $this->assertSame(['dashboard', 'projects', 'tools'], $user->dashboard_sections);
        $this->assertTrue($user->hasDashboardSection('projects'));
        $this->assertFalse($user->hasDashboardSection('crm'));
    }

    public function test_administrator_can_create_user_with_selected_dashboard_sections(): void
    {
        $this->seed(PermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrator');

        $this->actingAs($admin)->post(route('dashboard.users.store'), [
            'name' => 'Section User',
            'email' => 'sections@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Sales Executive',
            'is_active' => 1,
            'permissions' => [],
            'dashboard_sections' => ['dashboard', 'crm'],
        ])->assertRedirect(route('dashboard.users.index'));

        $this->assertSame(
            ['dashboard', 'crm'],
            User::query()->where('email', 'sections@example.com')->firstOrFail()->dashboard_sections
        );
    }

    public function test_invalid_dashboard_section_is_rejected(): void
    {
        $this->seed(PermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrator');
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Sales Executive');

        $this->actingAs($admin)->put(route('dashboard.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'Sales Executive',
            'is_active' => 1,
            'permissions' => [],
            'dashboard_sections' => ['dashboard', 'invalid-section'],
        ])->assertSessionHasErrors('dashboard_sections.1');
    }
}
