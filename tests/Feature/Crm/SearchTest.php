<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Customer;
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
}
