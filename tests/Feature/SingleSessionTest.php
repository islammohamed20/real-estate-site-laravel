<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SingleSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_internal_user_is_logged_out_when_a_newer_session_exists(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@example.com',
            'password' => Hash::make('password-123'),
            'is_active' => true,
        ]);

        $this->post(route('login.store'), [
            'email' => 'staff@example.com',
            'password' => 'password-123',
        ])->assertRedirect();

        $user->forceFill(['active_session_id' => 'newer-session'])->save();

        $this->get(route('dashboard.home'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_a_customer_is_logged_out_when_a_newer_session_exists(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password-123'),
            'email_verified_at' => now(),
        ]);

        $this->post(route('customer.login.store'), [
            'login' => 'customer@example.com',
            'password' => 'password-123',
        ])->assertRedirect(route('customer.account'));

        $customer->forceFill(['active_session_id' => 'newer-session'])->save();

        $this->get(route('customer.account'))
            ->assertRedirect(route('customer.login'));

        $this->assertGuest('customer');
    }
}
