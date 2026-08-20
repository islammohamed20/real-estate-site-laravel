<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadEditRenderTest extends TestCase
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

    public function test_edit_page_renders_with_save_button_for_website_lead(): void
    {
        $lead = Lead::query()->create([
            'name' => 'Lead From website db6334',
            'phone' => '010101000334',
            'stage' => 'reserved',
            'status' => 'active',
            'priority' => 'high',
            'source' => 'website',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard.crm.leads.edit', $lead));

        $response->assertOk();
        $response->assertSee('Save Changes');
    }

    public function test_edit_page_renders_when_lead_has_no_stage(): void
    {
        $lead = Lead::query()->create([
            'name' => 'No stage lead',
            'phone' => '01099999999',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard.crm.leads.edit', $lead));

        $response->assertOk();
        $response->assertSee('Save Changes');
    }
}
