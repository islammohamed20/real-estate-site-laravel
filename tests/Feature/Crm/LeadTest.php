<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Enums\LeadStage;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Tag;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
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

    public function test_leads_index_can_be_rendered(): void
    {
        Lead::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->get(route('dashboard.crm.leads.index'))
            ->assertOk()
            ->assertViewIs('crm.leads.index');
    }

    public function test_lead_can_be_created_with_new_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('dashboard.crm.leads.store'), [
                'name' => 'Sara Test',
                'phone' => '01009876543',
                'whatsapp' => '01009876543',
                'email' => 'sara@test.com',
                'budget' => 500000,
                'priority' => 'high',
                'unit_type' => 'Apartment',
                'bedrooms' => 3,
                'required_area' => 120,
                'preferred_payment_plan' => 'Installments 5 years',
                'campaign' => 'Summer 2026',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'name' => 'Sara Test',
            'phone' => '01009876543',
            'whatsapp' => '01009876543',
            'priority' => 'high',
            'unit_type' => 'Apartment',
            'bedrooms' => 3,
            'required_area' => 120.00,
            'campaign' => 'Summer 2026',
        ]);
    }

    public function test_lead_detail_can_be_rendered(): void
    {
        $lead = Lead::factory()->create();

        $this->actingAs($this->user)
            ->get(route('dashboard.crm.leads.show', $lead))
            ->assertOk()
            ->assertViewIs('crm.leads.show');
    }

    public function test_lead_can_be_updated(): void
    {
        $lead = Lead::factory()->create();

        $this->actingAs($this->user)
            ->put(route('dashboard.crm.leads.update', $lead), [
                'name' => 'Updated Name',
                'phone' => $lead->phone,
                'priority' => 'urgent',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'name' => 'Updated Name',
            'priority' => 'urgent',
        ]);
    }

    public function test_lead_can_be_assigned_to_user(): void
    {
        $lead = Lead::factory()->create();
        $sales = User::factory()->create(['is_active' => true]);

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.leads.assign', $lead), [
                'assigned_sales_id' => $sales->id,
                'notes' => 'Handover',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'assigned_sales_id' => $sales->id,
        ]);

        $this->assertDatabaseHas('lead_assignment_histories', [
            'lead_id' => $lead->id,
            'to_user_id' => $sales->id,
            'assigned_by' => $this->user->id,
        ]);
    }

    public function test_lead_stage_can_be_changed(): void
    {
        $lead = Lead::factory()->create(['stage' => LeadStage::New]);

        $this->actingAs($this->user)
            ->patchJson(route('dashboard.crm.leads.stage.update', $lead), [
                'stage' => LeadStage::Contacted->value,
                'notes' => 'Initial contact',
            ])
            ->assertOk();

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'stage' => LeadStage::Contacted->value,
        ]);

        $this->assertDatabaseHas('lead_stage_histories', [
            'lead_id' => $lead->id,
            'stage_from' => LeadStage::New->value,
            'stage_to' => LeadStage::Contacted->value,
        ]);
    }

    public function test_lead_converts_to_customer_when_reaching_contract_stage(): void
    {
        $lead = Lead::factory()->create([
            'phone' => '01005551234',
            'stage' => LeadStage::Negotiation,
        ]);

        $this->assertNull($lead->customer);

        $this->actingAs($this->user)
            ->patchJson(route('dashboard.crm.leads.stage.update', $lead), [
                'stage' => LeadStage::Contract->value,
                'notes' => 'Contract signed',
            ])
            ->assertOk();

        $lead->refresh();

        $this->assertInstanceOf(Customer::class, $lead->customer);
        $this->assertDatabaseHas('customers', [
            'phone' => '01005551234',
            'name' => $lead->name,
        ]);
    }

    public function test_lead_can_be_converted_manually(): void
    {
        $lead = Lead::factory()->create(['phone' => '01005559876']);

        $this->assertNull($lead->customer);

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.leads.convert', $lead))
            ->assertRedirect();

        $lead->refresh();

        $this->assertInstanceOf(Customer::class, $lead->customer);
        $this->assertDatabaseHas('customers', [
            'phone' => '01005559876',
            'name' => $lead->name,
        ]);
    }

    public function test_lead_can_be_deleted(): void
    {
        $lead = Lead::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('dashboard.crm.leads.destroy', $lead))
            ->assertRedirect();

        $this->assertSoftDeleted('leads', ['id' => $lead->id]);
    }

    public function test_lead_tags_can_be_synced(): void
    {
        $lead = Lead::factory()->create();
        $tag = Tag::factory()->create();

        $this->actingAs($this->user)
            ->put(route('dashboard.crm.leads.update', $lead), [
                'name' => $lead->name,
                'phone' => $lead->phone,
                'tags' => [(string) $tag->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_type' => Lead::class,
            'taggable_id' => $lead->id,
        ]);
    }

    public function test_lead_unit_interests_can_be_synced(): void
    {
        $lead = Lead::factory()->create();
        $unit = Unit::factory()->create();

        $this->actingAs($this->user)
            ->put(route('dashboard.crm.leads.update', $lead), [
                'name' => $lead->name,
                'phone' => $lead->phone,
                'interested_unit_ids' => [$unit->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('lead_unit_interests', [
            'lead_id' => $lead->id,
            'unit_id' => $unit->id,
        ]);
    }
}
