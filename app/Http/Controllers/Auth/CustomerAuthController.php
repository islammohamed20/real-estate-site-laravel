<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CustomerOtpMail;
use App\Models\Customer;
use App\Models\OtpLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('public.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // A customer may sign in with either their email or their phone number.
        $customer = Customer::query()
            ->where('email', $data['login'])
            ->orWhere('phone', $data['login'])
            ->first();

        if ($customer === null) {
            return back()->withErrors([
                'login' => __('The provided credentials are incorrect.'),
            ])->onlyInput('login');
        }

        if (empty($customer->password)) {
            return back()->withErrors([
                'login' => __('No password is set for this account yet — please register to create one.'),
            ])->onlyInput('login');
        }

        if (! Hash::check($data['password'], $customer->password)) {
            return back()->withErrors([
                'login' => __('The provided credentials are incorrect.'),
            ])->onlyInput('login');
        }

        // A newly registered account must verify its email (OTP) before signing in.
        if ($customer->email_verified_at === null) {
            $this->issueOtp($customer);

            $request->session()->put('customer_pending_verification_id', $customer->id);

            return redirect()->route('customer.verify.show')
                ->with('status', __('Please verify your email first. We sent a verification code to :email.', ['email' => $customer->email]));
        }

        Auth::guard('customer')->login($customer, $request->boolean('remember'));

        $request->session()->regenerate();

        $redirectUrl = $request->input('redirect') ?? route('customer.account');

        return redirect()->intended($redirectUrl);
    }

    public function showRegister(): View
    {
        return view('public.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[^<>]+$/'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Match an existing CRM record first (by phone, then by email) so a
        // customer already in the CRM is linked instead of being blocked by a
        // unique constraint — they just get a portal password.
        $customer = Customer::query()->where('phone', $data['phone'])->first()
            ?? Customer::query()->where('email', $data['email'])->first();

        if ($customer !== null) {
            if (! empty($customer->password)) {
                return back()->withErrors([
                    'phone' => __('An account already exists for this phone or email — please log in instead.'),
                ])->withInput();
            }

            // Existing CRM customer without a portal password — link the account.
            $customer->update([
                'name' => $data['name'],
                'occupation' => $data['occupation'] ?? $customer->occupation,
                'email' => $data['email'],
                'password' => $data['password'],
            ]);
        } else {
            $customer = Customer::create([
                'name' => $data['name'],
                'occupation' => $data['occupation'] ?? null,
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => $data['password'],
                'source' => 'portal',
            ]);
        }

        // A previously verified portal account signs straight in.
        if ($customer->email_verified_at !== null) {
            Auth::guard('customer')->login($customer);

            $request->session()->regenerate();

            return redirect()->intended($request->input('redirect') ?? route('customer.account'));
        }

        $this->issueOtp($customer);

        $request->session()->put('customer_pending_verification_id', $customer->id);

        return redirect()->route('customer.verify.show')
            ->with('status', __('We sent a verification code to :email — check your inbox.', ['email' => $customer->email]));
    }

    public function showVerifyEmail(Request $request): View|RedirectResponse
    {
        $customer = $this->pendingCustomer($request);

        if ($customer === null) {
            return redirect()->route('customer.login');
        }

        return view('public.auth.verify-email', [
            'customer' => $customer,
        ]);
    }

    public function verifyEmail(Request $request): RedirectResponse
    {
        $customer = $this->pendingCustomer($request);

        if ($customer === null) {
            return redirect()->route('customer.login');
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'digits:6'],
        ]);

        if ($customer->email_verified_at !== null) {
            return $this->completeVerification($request, $customer);
        }

        if ($customer->otp_code_hash === null || $customer->otp_expires_at === null || $customer->otp_expires_at->isPast()) {
            return back()->withErrors([
                'code' => __('This code has expired. Please request a new one.'),
            ]);
        }

        if ($customer->otp_attempts >= 5) {
            return back()->withErrors([
                'code' => __('Too many failed attempts. Please request a new code.'),
            ]);
        }

        if (! hash_equals((string) $customer->otp_code_hash, hash('sha256', $data['code']))) {
            $customer->increment('otp_attempts');

            return back()->withErrors([
                'code' => __('The code you entered is incorrect.'),
            ]);
        }

        return $this->completeVerification($request, $customer);
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $customer = $this->pendingCustomer($request);

        if ($customer === null) {
            return redirect()->route('customer.login');
        }

        if ($customer->email_verified_at !== null) {
            return $this->completeVerification($request, $customer);
        }

        $this->issueOtp($customer);

        return back()->with('status', __('A new verification code has been sent to your email.'));
    }

    /**
     * Generate a fresh 6-digit code, persist its hash and e-mail it to the customer.
     */
    private function issueOtp(Customer $customer): void
    {
        // 60-second cooldown so the previous code keeps working on rapid resend taps.
        if ($customer->otp_sent_at !== null && $customer->otp_sent_at->gt(now()->subSeconds(60))) {
            return;
        }

        $code = (string) random_int(100000, 999999);

        $customer->forceFill([
            'otp_code_hash' => hash('sha256', $code),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_sent_at' => now(),
            'otp_attempts' => 0,
        ])->save();

        Mail::to($customer->email)->send(new CustomerOtpMail($customer->name, $code, 10));

        OtpLog::create([
            'phone' => $customer->phone,
            'purpose' => 'customer_registration',
            'status' => 'sent',
            'channel' => 'email',
            'ip_address' => request()->ip(),
            'sent_at' => now(),
        ]);
    }

    private function completeVerification(Request $request, Customer $customer): RedirectResponse
    {
        $customer->forceFill([
            'email_verified_at' => now(),
            'otp_code_hash' => null,
            'otp_expires_at' => null,
            'otp_sent_at' => null,
            'otp_attempts' => 0,
        ])->save();

        $request->session()->forget('customer_pending_verification_id');

        Auth::guard('customer')->login($customer);

        $request->session()->regenerate();

        return redirect()->intended(route('customer.account'))
            ->with('status', __('Your email has been verified. Welcome!'));
    }

    private function pendingCustomer(Request $request): ?Customer
    {
        $id = $request->session()->get('customer_pending_verification_id');

        if ($id === null) {
            return null;
        }

        return Customer::query()->find($id);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}
