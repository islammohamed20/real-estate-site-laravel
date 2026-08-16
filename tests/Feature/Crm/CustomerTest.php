<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Enums\LeadStage;
use App\Models\Customer;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
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

    public function test_customers_index_can_be_rendered(): void
    {
        Customer::factory()->count(3)->create();

        $this->actingAs($this->user)
            ->get(route('dashboard.crm.customers.index'))
            ->assertOk()
            ->assertViewIs('crm.customers.index');
    }

    public function test_customer_can_be_created(): void
    {
        $this->actingAs($this->user)
            ->post(route('dashboard.crm.customers.store'), [
                'name' => 'Karim Customer',
                'phone' => '01001234567',
                'email' => 'karim@test.com',
                'budget_min' => 1000000,
                'budget_max' => 2000000,
                'source' => 'Website',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'name' => 'Karim Customer',
            'phone' => '01001234567',
            'budget_min' => 1000000,
            'budget_max' => 2000000,
        ]);
    }

    public function test_customer_detail_can_be_rendered(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->user)
            ->get(route('dashboard.crm.customers.show', $customer))
            ->assertOk()
            ->assertViewIs('crm.customers.show');
    }

    public function test_customer_can_be_updated(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->user)
            ->put(route('dashboard.crm.customers.update', $customer), [
                'name' => 'Updated Customer',
                'phone' => $customer->phone,
                'email' => $customer->email,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Customer',
        ]);
    }

    public function test_customer_tags_can_be_synced(): void
    {
        $customer = Customer::factory()->create();
        $tag = Tag::factory()->create();

        $this->actingAs($this->user)
            ->put(route('dashboard.crm.customers.update', $customer), [
                'name' => $customer->name,
                'phone' => $customer->phone,
                'tags' => [(string) $tag->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_type' => Customer::class,
            'taggable_id' => $customer->id,
        ]);
    }

    public function test_customer_timeline_contains_lead_events(): void
    {
        $customer = Customer::factory()->create();
        $customer->leads()->create([
            'name' => 'Timeline lead',
            'phone' => '01001112223',
            'stage' => LeadStage::New,
            'priority' => 'normal',
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard.crm.customers.show', $customer))
            ->assertOk()
            ->assertViewHas('timeline');
    }
}
