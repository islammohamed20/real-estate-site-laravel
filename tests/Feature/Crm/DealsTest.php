<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmDeal;
use App\Models\Crm\CrmOrganization;
use App\Models\Crm\CrmPipeline;
use App\Models\Crm\CrmStage;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private CrmPipeline $pipeline;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->user = User::factory()->create(['is_active' => true]);
        $this->user->assignRole('Administrator');

        $this->pipeline = CrmPipeline::factory()->create(['is_default' => true]);
        CrmStage::factory()->count(4)->create(['pipeline_id' => $this->pipeline->id]);
    }

    public function test_deals_index_can_be_rendered(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard.crm.deals.index', ['pipeline' => $this->pipeline->id]))
            ->assertOk()
            ->assertViewIs('crm.deals.index');
    }

    public function test_deal_can_be_created(): void
    {
        $stage = $this->pipeline->stages->first();
        $organization = CrmOrganization::factory()->create();
        $contact = CrmContact::factory()->create(['organization_id' => $organization->id]);

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.deals.store'), [
                'title' => 'Test deal',
                'pipeline_id' => $this->pipeline->id,
                'stage_id' => $stage->id,
                'organization_id' => $organization->id,
                'contact_id' => $contact->id,
                'value' => 250000,
                'priority' => 'high',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('crm_deals', [
            'title' => 'Test deal',
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $stage->id,
            'value' => 250000,
        ]);
    }

    public function test_deal_can_be_moved_to_another_stage(): void
    {
        $stages = $this->pipeline->stages;
        $deal = CrmDeal::factory()->create([
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $stages[0]->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->patchJson(route('dashboard.crm.deals.stage.update', $deal), [
                'stage_id' => $stages[1]->id,
            ])
            ->assertOk();

        $this->assertDatabaseHas('crm_deals', [
            'id' => $deal->id,
            'stage_id' => $stages[1]->id,
        ]);

        $this->assertDatabaseHas('crm_deal_stage_histories', [
            'deal_id' => $deal->id,
            'from_stage_id' => $stages[0]->id,
            'to_stage_id' => $stages[1]->id,
        ]);
    }

    public function test_activity_can_be_added_to_deal(): void
    {
        $stage = $this->pipeline->stages->first();
        $deal = CrmDeal::factory()->create([
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $stage->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.deals.activities.store', $deal), [
                'type' => 'call',
                'subject' => 'Follow-up call',
                'body' => 'Called to discuss pricing.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('crm_activities', [
            'deal_id' => $deal->id,
            'type' => 'call',
            'subject' => 'Follow-up call',
        ]);
    }

    public function test_deal_detail_can_be_rendered(): void
    {
        $stage = $this->pipeline->stages->first();
        $deal = CrmDeal::factory()->create([
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $stage->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard.crm.deals.show', $deal))
            ->assertOk()
            ->assertViewIs('crm.deals.show');
    }

    public function test_winning_deal_converts_linked_lead_to_customer(): void
    {
        $stage = $this->pipeline->stages->first();
        $lead = Lead::factory()->create(['phone' => '01005552345']);

        $deal = CrmDeal::factory()->create([
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $stage->id,
            'lead_id' => $lead->id,
            'customer_id' => null,
            'created_by' => $this->user->id,
            'status' => 'open',
        ]);

        $wonStage = CrmStage::factory()->create([
            'pipeline_id' => $this->pipeline->id,
            'name' => 'Won',
        ]);

        $this->actingAs($this->user)
            ->patchJson(route('dashboard.crm.deals.stage.update', $deal), [
                'stage_id' => $wonStage->id,
            ])
            ->assertOk();

        $lead->refresh();

        $this->assertInstanceOf(Customer::class, $lead->customer);
        $this->assertDatabaseHas('customers', [
            'phone' => '01005552345',
            'name' => $lead->name,
        ]);
    }
}
