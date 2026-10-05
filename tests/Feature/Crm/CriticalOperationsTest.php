<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\Customer;
use App\Models\InstallmentPlan;
use App\Models\InstallmentPlanItem;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CriticalOperationsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->admin = User::factory()->create(['is_active' => true]);
        $this->admin->assignRole('Administrator');
    }

    public function test_leads_csv_can_be_imported_and_updated_by_phone(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'leads.csv',
            "name,phone,email\nFirst Lead,01000000001,first@example.com\n"
        );

        $this->actingAs($this->admin)
            ->post(route('dashboard.crm.data-transfer.import'), [
                'type' => 'leads',
                'columns' => ['name', 'phone', 'email'],
                'file' => $file,
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('leads', [
            'phone' => '01000000001',
            'name' => 'First Lead',
            'source' => 'import',
        ]);

        $updated = UploadedFile::fake()->createWithContent(
            'leads.csv',
            "name,phone,email\nUpdated Lead,01000000001,updated@example.com\n"
        );

        $this->actingAs($this->admin)
            ->post(route('dashboard.crm.data-transfer.import'), [
                'type' => 'leads',
                'columns' => ['name', 'phone', 'email'],
                'file' => $updated,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(1, Lead::query()->where('phone', '01000000001')->count());
        $this->assertDatabaseHas('leads', ['phone' => '01000000001', 'name' => 'Updated Lead']);
    }

    public function test_trashed_customer_can_be_restored(): void
    {
        $customer = Customer::factory()->create();
        $customer->delete();

        $this->actingAs($this->admin)
            ->post(route('dashboard.crm.trash.customers.restore', $customer))
            ->assertRedirect();

        $this->assertFalse($customer->fresh()->trashed());
        $this->assertDatabaseHas('audit_logs', [
            'auditable_type' => Customer::class,
            'auditable_id' => $customer->id,
            'event' => 'customer.restored',
        ]);
    }

    public function test_installment_payment_is_recorded_and_cannot_exceed_amount(): void
    {
        $plan = InstallmentPlan::factory()->create();
        $item = InstallmentPlanItem::query()->create([
            'installment_plan_id' => $plan->id,
            'installment_number' => 1,
            'due_date' => now()->addMonth(),
            'amount' => 10000,
            'paid_amount' => 0,
            'balance_after' => 0,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('dashboard.crm.plans.items.update', [$plan, $item]), [
                'paid_amount' => 10000,
                'payment_method' => 'bank_transfer',
            ])
            ->assertRedirect();

        $item->refresh();
        $this->assertSame(10000.0, (float) $item->paid_amount);
        $this->assertNotNull($item->paid_at);
        $this->assertSame('bank_transfer', $item->payment_method);

        $this->actingAs($this->admin)
            ->patch(route('dashboard.crm.plans.items.update', [$plan, $item]), [
                'paid_amount' => 10001,
            ])
            ->assertSessionHasErrors('paid_amount');
    }

    public function test_installment_item_from_another_plan_is_rejected(): void
    {
        $plan = InstallmentPlan::factory()->create();
        $other = InstallmentPlan::factory()->create();
        $item = InstallmentPlanItem::query()->create([
            'installment_plan_id' => $other->id,
            'installment_number' => 1,
            'due_date' => now()->addMonth(),
            'amount' => 10000,
            'paid_amount' => 0,
            'balance_after' => 0,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('dashboard.crm.plans.items.update', [$plan, $item]), [
                'paid_amount' => 1000,
            ])
            ->assertNotFound();
    }
}
