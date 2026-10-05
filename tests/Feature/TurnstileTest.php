<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TurnstileTest extends TestCase
{
    use RefreshDatabase;

    private function enableTurnstile(): void
    {
        config([
            'services.turnstile.enabled' => true,
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
        ]);
    }

    public function test_widget_is_rendered_when_enabled(): void
    {
        $this->enableTurnstile();

        $this->get(route('customer.login'))
            ->assertOk()
            ->assertSee('cf-turnstile', false)
            ->assertSee('data-sitekey="test-site-key"', false)
            ->assertSee('data-response-field-name="turnstile"', false);
    }

    public function test_widget_is_not_rendered_when_disabled(): void
    {
        config(['services.turnstile.enabled' => false]);

        $this->get(route('customer.login'))
            ->assertOk()
            ->assertDontSee('cf-turnstile', false);
    }

    public function test_customer_login_fails_without_turnstile_token_when_enabled(): void
    {
        $this->enableTurnstile();

        Customer::query()->create([
            'name' => 'Test Customer',
            'phone' => '01000000001',
            'email' => 'test@example.com',
            'password' => bcrypt('secretpass123'),
        ])->forceFill(['email_verified_at' => now()])->save();

        $this->post(route('customer.login.store'), [
            'login' => 'test@example.com',
            'password' => 'secretpass123',
        ])->assertSessionHasErrors('turnstile');

        $this->assertGuest('customer');
    }

    public function test_customer_login_succeeds_with_valid_turnstile_token(): void
    {
        $this->enableTurnstile();

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true]),
        ]);

        Customer::query()->create([
            'name' => 'Test Customer',
            'phone' => '01000000001',
            'email' => 'test@example.com',
            'password' => bcrypt('secretpass123'),
        ])->forceFill(['email_verified_at' => now()])->save();

        $this->post(route('customer.login.store'), [
            'login' => 'test@example.com',
            'password' => 'secretpass123',
            'turnstile' => 'valid-token',
        ])->assertRedirect(route('customer.account'));

        $this->assertAuthenticated('customer');
    }

    public function test_customer_registration_is_blocked_by_invalid_turnstile_token(): void
    {
        $this->enableTurnstile();

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => false]),
        ]);

        Mail::fake();

        $this->post(route('customer.register.store'), [
            'name' => 'Test Customer',
            'occupation' => 'Engineer',
            'phone' => '01000000001',
            'email' => 'test@example.com',
            'password' => 'secretpass123',
            'password_confirmation' => 'secretpass123',
            'turnstile' => 'invalid-token',
        ])->assertSessionHasErrors('turnstile');

        $this->assertSame(0, Customer::query()->count());
    }

    public function test_public_inquiry_requires_turnstile_token_when_enabled(): void
    {
        $this->enableTurnstile();

        $this->post(route('public.inquiries.store'), [
            'name' => 'Test Customer',
            'phone' => '01000000001',
            'email' => 'test@example.com',
            'message' => 'I want to book a unit.',
        ])->assertSessionHasErrors('turnstile');
    }

    public function test_public_inquiry_succeeds_with_valid_turnstile_token(): void
    {
        $this->enableTurnstile();

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true]),
        ]);

        CompanyProfile::query()->create([
            'name' => 'Venecia Developments',
            'phone' => '201000000000',
            'email' => 'info@venecia-dev.com',
        ]);

        $this->post(route('public.inquiries.store'), [
            'name' => 'Test Customer',
            'phone' => '01000000001',
            'email' => 'test@example.com',
            'message' => 'I want to book a unit.',
            'turnstile' => 'valid-token',
        ])->assertSessionHas('status');
    }
}
