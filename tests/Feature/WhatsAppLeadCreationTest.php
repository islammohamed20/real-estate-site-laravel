<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use App\Models\WhatsAppConversation;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppLeadCreationTest extends TestCase
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

    private function conversation(string $phone, ?int $assignedTo = null): WhatsAppConversation
    {
        return WhatsAppConversation::query()->create([
            'customer_phone' => $phone,
            'customer_name' => 'Test Customer',
            'status' => WhatsAppConversation::STATUS_NEW,
            'assigned_to' => $assignedTo,
        ]);
    }

    public function test_lead_is_created_and_linked_with_full_fields(): void
    {
        $conv = $this->conversation('01006430355', $this->user->id);
        $conv->messages()->create([
            'direction' => 'incoming',
            'body' => 'Hello, I want to buy an apartment',
            'message_type' => 'text',
        ]);

        $this->actingAs($this->user)
            ->postJson('/real-statement-control/whatsapp/conversations/'.$conv->id.'/lead', [
                'name' => 'Ahmed',
                'budget' => 1500000,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('leads', [
            'name' => 'Ahmed',
            'phone' => '201006430355',
            'whatsapp' => '201006430355',
            'source' => 'WhatsApp',
            'stage' => 'new',
            'status' => 'active',
            'assigned_sales_id' => $this->user->id,
        ]);

        $lead = Lead::query()->firstOrFail();
        $this->assertStringContainsString('I want to buy an apartment', (string) $lead->notes);
        $this->assertSame($lead->id, $conv->fresh()->linked_lead_id);
    }

    public function test_existing_lead_with_same_phone_is_linked_not_duplicated(): void
    {
        $existing = Lead::query()->create([
            'name' => 'Existing',
            'phone' => '201006430355',
            'stage' => 'new',
            'status' => 'active',
            'source' => 'website',
        ]);

        $conv = $this->conversation('01006430355');

        $this->actingAs($this->user)
            ->postJson('/real-statement-control/whatsapp/conversations/'.$conv->id.'/lead', ['name' => 'New Name'])
            ->assertOk()
            ->assertJson(['success' => true, 'lead_id' => $existing->id]);

        $this->assertSame(1, Lead::query()->count());
        $this->assertSame($existing->id, $conv->fresh()->linked_lead_id);
    }

    public function test_existing_customer_is_linked_to_lead(): void
    {
        $customer = Customer::query()->create([
            'name' => 'Customer One',
            'phone' => '201006430355',
        ]);

        $conv = $this->conversation('01006430355');

        $this->actingAs($this->user)
            ->postJson('/real-statement-control/whatsapp/conversations/'.$conv->id.'/lead', [])
            ->assertOk();

        $this->assertDatabaseHas('leads', [
            'customer_id' => $customer->id,
            'phone' => '201006430355',
        ]);
    }

    public function test_conversation_already_linked_returns_existing_lead(): void
    {
        $lead = Lead::query()->create([
            'name' => 'Linked',
            'phone' => '201006430355',
            'stage' => 'new',
            'status' => 'active',
        ]);

        $conv = $this->conversation('01006430355');
        $conv->update(['linked_lead_id' => $lead->id]);

        $this->actingAs($this->user)
            ->postJson('/real-statement-control/whatsapp/conversations/'.$conv->id.'/lead', [])
            ->assertOk()
            ->assertJson(['success' => true, 'lead_id' => $lead->id]);

        $this->assertSame(1, Lead::query()->count());
    }
}
