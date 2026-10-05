<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
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

    public function test_global_search_returns_matching_lead(): void
    {
        $customer = Customer::factory()->create(['name' => 'Searchable Customer']);

        $this->actingAs($this->user)
            ->get(route('dashboard.crm.search', ['q' => 'Searchable']))
            ->assertOk()
            ->assertViewIs('crm.search.index')
            ->assertSee('Searchable Customer');
    }

    public function test_limited_search_is_scoped_to_assigned_records(): void
    {
        $actor = User::factory()->create(['is_active' => true]);
        $actor->givePermissionTo(['view crm dashboard', 'view own leads', 'view own customers']);
        $ownLead = Lead::factory()->create([
            'name' => 'Scoped Search Match',
            'assigned_sales_id' => $actor->id,
        ]);
        Customer::factory()->create(['name' => 'Scoped Search Customer']);
        Lead::factory()->create([
            'name' => 'Scoped Search Match',
            'assigned_sales_id' => $this->user->id,
        ]);

        $this->actingAs($actor)
            ->get(route('dashboard.crm.search', ['q' => 'Scoped Search Match']))
            ->assertOk()
            ->assertSee($ownLead->name)
            ->assertDontSee('Scoped Search Customer');
    }
}
