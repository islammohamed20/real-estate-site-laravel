<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
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

    public function test_activity_can_be_logged_for_lead(): void
    {
        $lead = Lead::factory()->create();

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.activities.store'), [
                'lead_id' => $lead->id,
                'type' => 'call',
                'subject' => 'Follow-up call',
                'body' => 'Discussed pricing and next steps.',
                'duration' => '15m',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('crm_activities', [
            'activityable_type' => Lead::class,
            'activityable_id' => $lead->id,
            'type' => 'call',
            'subject' => 'Follow-up call',
            'duration' => '15m',
            'deal_id' => null,
        ]);
    }

    public function test_activity_can_be_logged_for_customer(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.activities.store'), [
                'customer_id' => $customer->id,
                'type' => 'meeting',
                'subject' => 'Site visit',
                'body' => 'Visited the showroom.',
                'duration' => '1h',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('crm_activities', [
            'activityable_type' => Customer::class,
            'activityable_id' => $customer->id,
            'type' => 'meeting',
            'subject' => 'Site visit',
            'duration' => '1h',
        ]);
    }

    public function test_activity_can_be_completed(): void
    {
        $customer = Customer::factory()->create();
        $activity = $customer->activities()->create([
            'created_by' => $this->user->id,
            'type' => 'call',
            'subject' => 'Call',
            'body' => 'Body',
        ]);

        $this->actingAs($this->user)
            ->patchJson(route('dashboard.crm.activities.complete', $activity))
            ->assertOk();

        $this->assertNotNull($activity->fresh()->completed_at);
    }

    public function test_activity_can_be_deleted(): void
    {
        $customer = Customer::factory()->create();
        $activity = $customer->activities()->create([
            'created_by' => $this->user->id,
            'type' => 'call',
            'subject' => 'Call',
            'body' => 'Body',
        ]);

        $this->actingAs($this->user)
            ->delete(route('dashboard.crm.activities.destroy', $activity))
            ->assertRedirect();

        $this->assertDatabaseMissing('crm_activities', ['id' => $activity->id]);
    }
}
