<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
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

    public function test_reservations_index_can_be_rendered(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard.crm.reservations.index'))
            ->assertOk()
            ->assertViewIs('crm.reservations.index');
    }

    public function test_reservation_can_be_created(): void
    {
        $customer = Customer::factory()->create();
        $unit = Unit::factory()->create();

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.reservations.store'), [
                'customer_id' => $customer->id,
                'unit_id' => $unit->id,
                'deposit_amount' => 50000,
                'reserved_at' => now()->format('Y-m-d H:i:s'),
                'status' => 'pending',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'customer_id' => $customer->id,
            'unit_id' => $unit->id,
            'deposit_amount' => 50000,
        ]);
    }

    public function test_reservation_detail_can_be_rendered(): void
    {
        $reservation = Reservation::factory()->create();

        $this->actingAs($this->user)
            ->get(route('dashboard.crm.reservations.show', $reservation))
            ->assertOk()
            ->assertViewIs('crm.reservations.show');
    }

    public function test_reservation_can_be_updated(): void
    {
        $reservation = Reservation::factory()->create();

        $this->actingAs($this->user)
            ->put(route('dashboard.crm.reservations.update', $reservation), [
                'unit_id' => $reservation->unit_id,
                'customer_id' => $reservation->customer_id,
                'deposit_amount' => 75000,
                'status' => 'paid',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'deposit_amount' => 75000,
            'status' => 'paid',
        ]);
    }

    public function test_reservation_can_be_deleted(): void
    {
        $reservation = Reservation::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('dashboard.crm.reservations.destroy', $reservation))
            ->assertRedirect();

        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    public function test_converted_reservation_converts_linked_lead_to_customer(): void
    {
        $lead = Lead::factory()->create(['phone' => '01005554567']);
        $unit = Unit::factory()->create();

        $this->actingAs($this->user)
            ->post(route('dashboard.crm.reservations.store'), [
                'lead_id' => $lead->id,
                'unit_id' => $unit->id,
                'deposit_amount' => 50000,
                'reserved_at' => now()->format('Y-m-d H:i:s'),
                'status' => 'converted',
            ])
            ->assertRedirect();

        $lead->refresh();

        $this->assertInstanceOf(Customer::class, $lead->customer);
        $this->assertDatabaseHas('customers', [
            'phone' => '01005554567',
            'name' => $lead->name,
        ]);
    }
}
