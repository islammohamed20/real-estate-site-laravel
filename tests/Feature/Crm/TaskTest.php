<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Customer;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
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

    public function test_tasks_index_can_be_rendered(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard.crm.tasks.index'))
            ->assertOk()
            ->assertViewIs('crm.tasks.index');
    }

    public function test_task_can_be_created(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.tasks.store'), [
                'title' => 'Follow-up call',
                'description' => 'Call to confirm interest',
                'priority' => 'high',
                'due_at' => now()->addDay()->format('Y-m-d H:i:s'),
                'related_type' => 'customer',
                'related_id' => $customer->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Follow-up call',
            'taskable_type' => Customer::class,
            'taskable_id' => $customer->id,
        ]);
    }

    public function test_task_status_can_be_updated(): void
    {
        $customer = Customer::factory()->create();
        $task = $customer->tasks()->create([
            'title' => 'Task',
            'created_by' => $this->user->id,
            'priority' => 'normal',
            'status' => 'open',
        ]);

        $this->actingAs($this->user)
            ->put(route('dashboard.crm.tasks.update', $task), ['status' => 'completed'])
            ->assertRedirect();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    public function test_task_can_be_deleted(): void
    {
        $customer = Customer::factory()->create();
        $task = $customer->tasks()->create([
            'title' => 'Task',
            'created_by' => $this->user->id,
            'priority' => 'normal',
            'status' => 'open',
        ]);

        $this->actingAs($this->user)
            ->delete(route('dashboard.crm.tasks.destroy', $task))
            ->assertRedirect();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_task_can_be_completed_via_api(): void
    {
        $customer = Customer::factory()->create();
        $task = $customer->tasks()->create([
            'title' => 'Task',
            'created_by' => $this->user->id,
            'priority' => 'normal',
            'status' => 'open',
        ]);

        $this->actingAs($this->user)
            ->patchJson(route('dashboard.crm.tasks.complete', $task))
            ->assertOk();

        $this->assertEquals('completed', $task->fresh()->status);
    }
}
