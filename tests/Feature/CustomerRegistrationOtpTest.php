<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\CustomerOtpMail;
use App\Models\Customer;
use App\Models\Document;
use App\Services\EvolutionApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerRegistrationOtpTest extends TestCase
{
    use RefreshDatabase;

    private function validRegistrationData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ahmed Test',
            'occupation' => 'Engineer',
            'phone' => '01000000001',
            'email' => 'ahmed@example.com',
            'password' => 'secretpass123',
            'password_confirmation' => 'secretpass123',
        ], $overrides);
    }

    public function test_new_registration_sends_otp_and_requires_verification_before_login(): void
    {
        Mail::fake();

        $this->post(route('customer.register.store'), $this->validRegistrationData())
            ->assertRedirect(route('customer.verify.show'));

        $customer = Customer::query()->where('email', 'ahmed@example.com')->firstOrFail();

        $this->assertNull($customer->email_verified_at);
        $this->assertNotNull($customer->otp_code_hash);
        $this->assertNotNull($customer->otp_expires_at);
        $this->assertGuest('customer');

        Mail::assertSent(CustomerOtpMail::class, fn (CustomerOtpMail $mail) => $mail->hasTo('ahmed@example.com'));

        // Wrong code is rejected and does not log the customer in.
        $this->post(route('customer.verify.store'), ['code' => '000000'])
            ->assertSessionHasErrors('code');
        $this->assertGuest('customer');

        // The code from the e-mail completes verification and logs the customer in.
        $otpCode = Mail::sent(CustomerOtpMail::class)->first()->otpCode;

        $this->post(route('customer.verify.store'), ['code' => $otpCode])
            ->assertRedirect(route('customer.account'));

        $customer->refresh();
        $this->assertNotNull($customer->email_verified_at);
        $this->assertNull($customer->otp_code_hash);
        $this->assertAuthenticatedAs($customer, 'customer');
    }

    public function test_email_is_required_for_registration(): void
    {
        Mail::fake();

        $this->post(route('customer.register.store'), $this->validRegistrationData(['email' => '']))
            ->assertSessionHasErrors('email');

        $this->assertSame(0, Customer::query()->count());
    }

    public function test_expired_otp_is_rejected(): void
    {
        Mail::fake();

        $this->post(route('customer.register.store'), $this->validRegistrationData());

        $customer = Customer::query()->firstOrFail();
        $customer->forceFill(['otp_expires_at' => now()->subMinute()])->save();

        $otpCode = Mail::sent(CustomerOtpMail::class)->first()->otpCode;

        $this->post(route('customer.verify.store'), ['code' => $otpCode])
            ->assertSessionHasErrors('code');

        $this->assertGuest('customer');
        $this->assertNull($customer->refresh()->email_verified_at);
    }

    public function test_resend_issues_a_fresh_code(): void
    {
        Mail::fake();

        $this->post(route('customer.register.store'), $this->validRegistrationData());

        $customer = Customer::query()->firstOrFail();
        $customer->forceFill(['otp_sent_at' => now()->subMinutes(5)])->save();

        $this->post(route('customer.verify.resend'))
            ->assertSessionHas('status');

        $this->assertSame(2, Mail::sent(CustomerOtpMail::class)->count());
        $this->assertNotNull($customer->refresh()->otp_code_hash);
    }

    public function test_existing_crm_customer_is_linked_and_still_gets_otp(): void
    {
        Mail::fake();

        Customer::query()->create([
            'name' => 'CRM Ahmed',
            'phone' => '01000000001',
            'source' => 'crm',
        ]);

        $this->post(route('customer.register.store'), $this->validRegistrationData())
            ->assertRedirect(route('customer.verify.show'));

        $customer = Customer::query()->where('phone', '01000000001')->firstOrFail();

        $this->assertNull($customer->email_verified_at);
        $this->assertSame('Ahmed Test', $customer->name);
        $this->assertNotNull($customer->otp_code_hash);
        $this->assertGuest('customer');

        Mail::assertSent(CustomerOtpMail::class, fn (CustomerOtpMail $mail) => $mail->hasTo('ahmed@example.com'));
    }

    public function test_admin_verified_crm_customer_registers_without_otp(): void
    {
        Mail::fake();

        Customer::query()->create([
            'name' => 'CRM Verified',
            'phone' => '01000000002',
            'email' => 'verified@example.com',
            'source' => 'crm',
        ])->forceFill(['email_verified_at' => now()])->save();

        $this->post(route('customer.register.store'), $this->validRegistrationData([
            'phone' => '01000000002',
            'email' => 'verified@example.com',
        ]))->assertRedirect(route('customer.account'));

        Mail::assertNothingSent();

        $this->assertAuthenticatedAs(Customer::query()->where('email', 'verified@example.com')->firstOrFail(), 'customer');
    }

    public function test_unverified_account_cannot_login_directly(): void
    {
        Mail::fake();

        $this->post(route('customer.register.store'), $this->validRegistrationData());

        $customer = Customer::query()->where('email', 'ahmed@example.com')->firstOrFail();
        $this->assertGuest('customer');

        // Simulate the customer leaving and coming back after the resend cooldown.
        $customer->forceFill(['otp_sent_at' => now()->subMinutes(5)])->save();

        // Signing out of the pending flow and trying to log in still forces verification.
        $this->post(route('customer.login.store'), [
            'login' => 'ahmed@example.com',
            'password' => 'secretpass123',
        ])->assertRedirect(route('customer.verify.show'));

        $this->assertGuest('customer');

        // A fresh code is issued for the blocked login attempt.
        $this->assertSame(2, Mail::sent(CustomerOtpMail::class)->count());
        $this->assertNotNull($customer->refresh()->otp_code_hash);
    }

    public function test_otp_page_is_unreachable_without_pending_registration(): void
    {
        $this->get(route('customer.verify.show'))->assertRedirect(route('customer.login'));
    }

    public function test_customer_can_update_profile_and_password(): void
    {
        $customer = Customer::factory()->create([
            'password' => Hash::make('old-password-123'),
            'email_verified_at' => now(),
        ]);

        $this->actingAs($customer, 'customer')
            ->get(route('customer.account'))
            ->assertOk()
            ->assertSee('Welcome');

        $this->put(route('customer.profile.update'), [
                'name' => 'Updated Customer',
                'occupation' => 'Architect',
                'address' => 'New address',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Updated Customer',
            'occupation' => 'Architect',
            'address' => 'New address',
        ]);

        $this->put(route('customer.password.update'), [
            'current_password' => 'old-password-123',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('new-password-123', $customer->refresh()->password));
    }

    public function test_customer_can_download_only_their_own_documents(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/customer.pdf', 'customer document');
        Storage::disk('public')->put('documents/other.pdf', 'other document');

        $customer = Customer::factory()->create([
            'password' => Hash::make('secret-password-123'),
            'email_verified_at' => now(),
        ]);
        $otherCustomer = Customer::factory()->create();

        $document = Document::query()->create([
            'documentable_type' => Customer::class,
            'documentable_id' => $customer->id,
            'name' => 'customer.pdf',
            'file_path' => 'documents/customer.pdf',
            'mime_type' => 'application/pdf',
            'size' => 16,
        ]);
        $otherDocument = Document::query()->create([
            'documentable_type' => Customer::class,
            'documentable_id' => $otherCustomer->id,
            'name' => 'other.pdf',
            'file_path' => 'documents/other.pdf',
            'mime_type' => 'application/pdf',
            'size' => 14,
        ]);

        $this->actingAs($customer, 'customer')
            ->get(route('customer.documents.download', $document))
            ->assertDownload('customer.pdf');

        $this->get(route('customer.documents.download', $otherDocument))
            ->assertForbidden();
    }

    public function test_customer_can_enable_whatsapp_two_factor_and_confirm_password_change(): void
    {
        $customer = Customer::factory()->create([
            'phone' => '01000000009',
            'password' => Hash::make('old-password-123'),
            'email_verified_at' => now(),
        ]);
        $codes = [];

        $this->mock(EvolutionApiService::class, function ($mock) use (&$codes): void {
            $mock->shouldReceive('sendMessage')->twice()->andReturnUsing(function (string $number, string $message) use (&$codes): bool {
                preg_match('/\\b(\\d{6})\\b/', $message, $matches);
                $codes[] = $matches[1] ?? null;
                return true;
            });
        });

        $this->actingAs($customer, 'customer')
            ->post(route('customer.2fa.whatsapp.request'))
            ->assertRedirect();

        $this->post(route('customer.2fa.whatsapp.enable'), ['code' => $codes[0]])
            ->assertRedirect();
        $this->assertTrue((bool) $customer->refresh()->whatsapp_two_factor_enabled);

        $this->put(route('customer.password.update'), [
            'current_password' => 'old-password-123',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect();

        $this->post(route('customer.password.verify'), ['code' => $codes[1]])
            ->assertRedirect();
        $this->assertTrue(Hash::check('new-password-123', $customer->refresh()->password));
    }

    public function test_customer_can_open_authenticator_setup(): void
    {
        $customer = Customer::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($customer, 'customer')
            ->get(route('customer.2fa.authenticator'))
            ->assertOk()
            ->assertSee('QR Code')
            ->assertSessionHas('customer.authenticator_secret');
    }

    public function test_customer_account_requires_authentication(): void
    {
        $this->get(route('customer.account'))->assertRedirect(route('customer.login'));
    }
}
