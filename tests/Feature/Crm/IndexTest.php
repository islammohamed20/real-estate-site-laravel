<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Crm\CrmOrganization;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
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

    public function test_crm_index_can_be_rendered(): void
    {
        Customer::factory()->count(3)->create();
        Lead::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->get(route('dashboard.crm.index'))
            ->assertOk()
            ->assertViewIs('crm.index');
    }

    public function test_lead_can_be_created_from_quick_form(): void
    {
        $this->actingAs($this->user)
            ->post(route('dashboard.crm.leads.store'), [
                'name' => 'Ahmed Test',
                'phone' => '01001234567',
                'email' => 'ahmed@test.com',
                'message' => 'Looking for a villa',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'name' => 'Ahmed Test',
            'phone' => '01001234567',
        ]);

        $this->assertDatabaseMissing('customers', [
            'phone' => '01001234567',
        ]);
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

    public function test_note_can_be_created(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.notes.store'), [
                'body' => 'Called the customer',
                'type' => 'call',
                'related_type' => 'customer',
                'related_id' => $customer->id,
                'noted_at' => now()->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('notes', [
            'body' => 'Called the customer',
            'noteable_type' => Customer::class,
            'noteable_id' => $customer->id,
        ]);
    }

    public function test_note_can_be_deleted(): void
    {
        $customer = Customer::factory()->create();
        $note = $customer->recordedNotes()->create([
            'user_id' => $this->user->id,
            'body' => 'Note body',
            'type' => 'note',
        ]);

        $this->actingAs($this->user)
            ->delete(route('dashboard.crm.notes.destroy', $note))
            ->assertRedirect();

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_organization_can_be_created(): void
    {
        $this->actingAs($this->user)
            ->post(route('dashboard.crm.organizations.store'), [
                'name' => 'Acme Real Estate',
                'industry' => 'Real Estate',
                'phone' => '0212345678',
                'email' => 'info@acme.test',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('crm_organizations', [
            'name' => 'Acme Real Estate',
            'industry' => 'Real Estate',
            'phone' => '0212345678',
            'email' => 'info@acme.test',
        ]);
    }

    public function test_contact_can_be_created(): void
    {
        $organization = CrmOrganization::factory()->create();

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.contacts.store'), [
                'organization_id' => $organization->id,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@acme.test',
                'phone' => '01234567890',
                'job_title' => 'Sales Manager',
                'is_primary' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('crm_contacts', [
            'organization_id' => $organization->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@acme.test',
            'job_title' => 'Sales Manager',
            'is_primary' => true,
        ]);
    }
}
