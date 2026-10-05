<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Services\CRM\CustomerConversionService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PortalLeadConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_registration_creates_a_lead_and_is_hidden_from_customer_index_until_conversion(): void
    {
        $this->seed(PermissionSeeder::class);
        Mail::fake();

        $this->post(route('customer.register.store'), [
            'name' => 'Portal Lead',
            'phone' => '01000000009',
            'email' => 'portal-lead@example.com',
            'password' => 'secretpass123',
            'password_confirmation' => 'secretpass123',
        ])->assertRedirect(route('customer.verify.show'));

        $customer = Customer::query()->where('email', 'portal-lead@example.com')->firstOrFail();
        $lead = Lead::query()->where('customer_id', $customer->id)->firstOrFail();

        $this->assertSame('Portal', $customer->source);
        $this->assertSame('Portal', $lead->source);
        $this->assertSame('Portal', $lead->leadSource?->name);

        $admin = \App\Models\User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrator');

        $this->actingAs($admin)
            ->get(route('dashboard.crm.customers.index'))
            ->assertOk()
            ->assertViewHas('customers', fn ($customers) => $customers->total() === 0);

        app(CustomerConversionService::class)->convertFromLead($lead->fresh());

        $this->assertSame('Website', $customer->refresh()->source);

        $this->actingAs($admin)
            ->get(route('dashboard.crm.customers.index'))
            ->assertOk()
            ->assertViewHas('customers', fn ($customers) => $customers->total() === 1)
            ->assertSee('Portal Lead')
            ->assertSee('Website');
    }
}
