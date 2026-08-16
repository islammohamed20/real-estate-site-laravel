<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Customer;
use App\Models\Offer;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferTest extends TestCase
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

    public function test_offers_index_can_be_rendered(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard.crm.offers.index'))
            ->assertOk()
            ->assertViewIs('crm.offers.index');
    }

    public function test_offer_can_be_created(): void
    {
        $customer = Customer::factory()->create();
        $unit = Unit::factory()->create();

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.offers.store'), [
                'customer_id' => $customer->id,
                'unit_id' => $unit->id,
                'subtotal' => 1000000,
                'total_amount' => 1000000,
                'status' => 'draft',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('offers', [
            'customer_id' => $customer->id,
            'unit_id' => $unit->id,
            'subtotal' => 1000000,
            'total_amount' => 1000000,
        ]);
    }

    public function test_offer_detail_can_be_rendered(): void
    {
        $offer = Offer::factory()->create();

        $this->actingAs($this->user)
            ->get(route('dashboard.crm.offers.show', $offer))
            ->assertOk()
            ->assertViewIs('crm.offers.show');
    }

    public function test_offer_can_be_updated(): void
    {
        $offer = Offer::factory()->create();

        $this->actingAs($this->user)
            ->put(route('dashboard.crm.offers.update', $offer), [
                'unit_id' => $offer->unit_id,
                'customer_id' => $offer->customer_id,
                'subtotal' => 2000000,
                'total_amount' => 2000000,
                'status' => 'sent',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'subtotal' => 2000000,
            'total_amount' => 2000000,
            'status' => 'sent',
        ]);
    }

    public function test_offer_can_be_deleted(): void
    {
        $offer = Offer::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('dashboard.crm.offers.destroy', $offer))
            ->assertRedirect();

        $this->assertDatabaseMissing('offers', ['id' => $offer->id]);
    }
}
