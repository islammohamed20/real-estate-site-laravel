<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityPageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->admin = User::factory()->create([
            'email' => 'admin@venecia-dev.com',
            'is_active' => true,
        ]);
        $this->admin->assignRole('Administrator');
    }

    public function test_security_page_displays_passkey_section(): void
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard.security'))
            ->assertOk()
            ->assertViewIs('settings.security')
            ->assertSee(__('Passkeys'))
            ->assertSee(__('Register a new passkey'));
    }

    public function test_security_page_shows_no_passkey_message_when_none_registered(): void
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard.security'))
            ->assertOk()
            ->assertSee(__('No passkey registered'));
    }

    public function test_security_page_shows_passkey_list_when_registered(): void
    {
        $this->admin->passkeys()->create([
            'name' => 'MacBook Pro',
            'credential_id' => 'test-credential-id',
            'credential' => ['publicKey' => 'test-data'],
        ]);

        $this->actingAs($this->admin)
            ->withSession(['2fa:verified' => true])
            ->get(route('dashboard.security'))
            ->assertOk()
            ->assertSee('MacBook Pro')
            ->assertSee(__('Passkey is active'));
    }

    public function test_security_page_shows_register_passkey_button(): void
    {
        $this->actingAs($this->admin)
            ->get(route('dashboard.security'))
            ->assertOk()
            ->assertSee('id="register-passkey"', false);
    }
}
