<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUpTest extends TestCase
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

    public function test_follow_ups_index_can_be_rendered(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard.crm.follow_ups.index'))
            ->assertOk()
            ->assertViewIs('crm.follow_ups.index');
    }

    public function test_follow_up_can_be_scheduled_for_lead(): void
    {
        $lead = Lead::factory()->create();

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.follow_ups.store'), [
                'lead_id' => $lead->id,
                'follow_up_at' => now()->addDay()->format('Y-m-d H:i:s'),
                'type' => 'phone_call',
                'channel' => 'phone',
                'notes' => 'Call to confirm interest',
                'priority' => 'high',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('follow_ups', [
            'lead_id' => $lead->id,
            'type' => 'phone_call',
            'channel' => 'phone',
            'priority' => 'high',
        ]);
    }

    public function test_follow_up_can_be_scheduled_for_customer(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.follow_ups.store'), [
                'customer_id' => $customer->id,
                'follow_up_at' => now()->addDay()->format('Y-m-d H:i:s'),
                'type' => 'site_visit',
                'notes' => 'Visit the showroom',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('follow_ups', [
            'customer_id' => $customer->id,
            'type' => 'site_visit',
        ]);
    }

    public function test_follow_up_can_be_completed(): void
    {
        $customer = Customer::factory()->create();
        $followUp = $customer->followUps()->create([
            'created_by' => $this->user->id,
            'follow_up_at' => now()->addDay(),
            'type' => 'phone_call',
            'status' => 'pending',
        ]);

        $this->actingAs($this->user)
            ->patchJson(route('dashboard.crm.follow_ups.complete', $followUp))
            ->assertOk();

        $this->assertEquals('completed', $followUp->fresh()->status);
        $this->assertNotNull($followUp->fresh()->completed_at);
    }

    public function test_follow_up_can_be_deleted(): void
    {
        $customer = Customer::factory()->create();
        $followUp = $customer->followUps()->create([
            'created_by' => $this->user->id,
            'follow_up_at' => now()->addDay(),
            'type' => 'phone_call',
        ]);

        $this->actingAs($this->user)
            ->delete(route('dashboard.crm.follow_ups.destroy', $followUp))
            ->assertRedirect();

        $this->assertDatabaseMissing('follow_ups', ['id' => $followUp->id]);
    }
}
