<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Document;
use App\Models\Offer;
use App\Models\Reservation;
use App\Services\EvolutionApiService;
use App\Services\Security\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerAccountController extends Controller
{
    private const WHATSAPP_2FA_SESSION = 'customer.whatsapp_2fa_pending';

    private const PASSWORD_OTP_SESSION = 'customer.password_otp_pending';

    public function index(): View
    {
        $customer = Auth::guard('customer')->user()->load([
            'reservations.unit.project',
            'reservations.documents',
            'offers.project',
            'offers.unit',
            'offers.documents',
            'deals',
            'documents',
            'plans.unit',
            'plans.project',
        ]);

        $customer->setRelation(
            'offers',
            $customer->offers->whereIn('status', ['sent', 'accepted', 'rejected', 'expired'])->values(),
        );

        $documents = $customer->documents
            ->concat($customer->reservations->flatMap->documents)
            ->concat($customer->offers->flatMap->documents)
            ->sortByDesc('created_at')
            ->values();

        $customer->setRelation('documents', $documents);

        return view('account.index', [
            'customer' => $customer,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $customer->update($data);

        return back()->with('status', __('Profile updated successfully.'));
    }

    public function requestWhatsappTwoFactor(Request $request, EvolutionApiService $evolution): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $number = $customer->whatsapp ?: $customer->phone;

        if (empty($number)) {
            return back()->withErrors(['two_factor' => __('Add a WhatsApp number before enabling WhatsApp verification.')]);
        }

        $pending = $request->session()->get(self::WHATSAPP_2FA_SESSION);
        if (is_array($pending) && isset($pending['sent_at']) && $pending['sent_at'] > now()->subSeconds(60)->timestamp) {
            return back()->with('status', __('A verification code was already sent. Please check WhatsApp.'));
        }

        $code = (string) random_int(100000, 999999);
        $sent = $evolution->sendMessage($number, __('Your Venecia security code is :code. It expires in 10 minutes.', ['code' => $code]));

        if (! $sent) {
            return back()->withErrors(['two_factor' => __('We could not send the WhatsApp code. Please try again later.')]);
        }

        $request->session()->put(self::WHATSAPP_2FA_SESSION, [
            'hash' => hash('sha256', $code),
            'expires_at' => now()->addMinutes(10)->timestamp,
            'sent_at' => now()->timestamp,
            'attempts' => 0,
        ]);

        return back()->with('status', __('A verification code was sent to your WhatsApp number.'));
    }

    public function enableWhatsappTwoFactor(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $pending = $request->session()->get(self::WHATSAPP_2FA_SESSION);

        if (! $this->validOtp($pending, $data['code'])) {
            $this->recordOtpFailure($request, self::WHATSAPP_2FA_SESSION, $pending);
            return back()->withErrors(['two_factor_code' => $this->otpError($pending)]);
        }

        $customer->forceFill(['whatsapp_two_factor_enabled' => true])->save();
        $request->session()->forget(self::WHATSAPP_2FA_SESSION);

        return back()->with('status', __('WhatsApp two-factor authentication enabled.'));
    }

    public function showAuthenticatorSetup(Request $request, TwoFactorService $twoFactor): View|RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        if ($customer->authenticator_two_factor_enabled) {
            return redirect()->route('customer.account');
        }

        $secret = $request->session()->get('customer.authenticator_secret');
        if (! is_string($secret) || $secret === '') {
            $secret = $twoFactor->generateSecret();
            $request->session()->put('customer.authenticator_secret', $secret);
        }

        return view('account.authenticator', [
            'secret' => $secret,
            'qrCode' => $twoFactor->qrCodeDataUri($twoFactor->provisioningUri($customer, $secret)),
        ]);
    }

    public function enableAuthenticator(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $secret = $request->session()->get('customer.authenticator_secret');

        if (! is_string($secret) || ! $twoFactor->verify($secret, $data['code'])) {
            return back()->withErrors(['code' => __('The provided code is invalid.')]);
        }

        $codes = $twoFactor->generateRecoveryCodes();
        $customer->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => array_map(fn (string $code): string => $twoFactor->hashRecoveryCode($code), $codes),
            'authenticator_two_factor_enabled' => true,
        ])->save();

        $request->session()->forget('customer.authenticator_secret');
        $request->session()->flash('customer.recovery_codes', $codes);

        return redirect()->route('customer.account')->with('status', __('Google Authenticator enabled. Save your recovery codes securely.'));
    }

    public function disableWhatsappTwoFactor(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $request->validate(['current_password' => ['required', 'current_password:customer']]);
        $customer->forceFill(['whatsapp_two_factor_enabled' => false])->save();

        return back()->with('status', __('WhatsApp two-factor authentication disabled.'));
    }

    public function disableAuthenticator(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $data = $request->validate(['code' => ['required', 'string', 'max:255']]);
        $code = (string) $data['code'];
        $valid = is_string($customer->two_factor_secret)
            && $twoFactor->verify($customer->two_factor_secret, $code);

        if (! $valid) {
            $valid = $twoFactor->verifyRecoveryCode($customer, $code);
        }

        if (! $valid) {
            return back()->withErrors(['authenticator_code' => __('The provided code is invalid.')]);
        }

        $customer->forceFill([
            'authenticator_two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        return back()->with('status', __('Google Authenticator disabled.'));
    }

    public function updatePassword(Request $request, EvolutionApiService $evolution): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $data = $request->validate([
            'current_password' => ['required', 'current_password:customer'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! $customer->whatsapp_two_factor_enabled) {
            return $this->savePassword($request, $data['password']);
        }

        $number = $customer->whatsapp ?: $customer->phone;
        $code = (string) random_int(100000, 999999);
        if (empty($number) || ! $evolution->sendMessage($number, __('Your password change code is :code. It expires in 10 minutes.', ['code' => $code]))) {
            return back()->withErrors(['password' => __('We could not send the WhatsApp code. Your password was not changed.')]);
        }

        $request->session()->put(self::PASSWORD_OTP_SESSION, [
            'password' => Crypt::encryptString($data['password']),
            'hash' => hash('sha256', $code),
            'expires_at' => now()->addMinutes(10)->timestamp,
            'attempts' => 0,
        ]);

        return back()->with('status', __('A WhatsApp code was sent. Enter it to confirm your password change.'));
    }

    public function verifyPasswordChange(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $pending = $request->session()->get(self::PASSWORD_OTP_SESSION);

        if (! $this->validOtp($pending, $data['code'])) {
            $this->recordOtpFailure($request, self::PASSWORD_OTP_SESSION, $pending);
            return back()->withErrors(['password_code' => $this->otpError($pending)]);
        }

        try {
            $password = Crypt::decryptString((string) $pending['password']);
        } catch (\Throwable) {
            $request->session()->forget(self::PASSWORD_OTP_SESSION);
            return back()->withErrors(['password_code' => __('This password change request has expired. Please try again.')]);
        }

        return $this->savePassword($request, $password);
    }

    private function savePassword(Request $request, string $password): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $customer->update(['password' => $password]);
        Auth::guard('customer')->logoutOtherDevices($password);
        $request->session()->forget(self::PASSWORD_OTP_SESSION);

        return back()->with('status', __('Password updated successfully.'));
    }

    private function validOtp(mixed $pending, string $code): bool
    {
        if (! is_array($pending) || ($pending['expires_at'] ?? 0) < now()->timestamp || ($pending['attempts'] ?? 0) >= 5) {
            return false;
        }

        if (! hash_equals((string) ($pending['hash'] ?? ''), hash('sha256', $code))) {
            return false;
        }

        return true;
    }

    private function recordOtpFailure(Request $request, string $key, mixed $pending): void
    {
        if (! is_array($pending)) {
            return;
        }

        $pending['attempts'] = ((int) ($pending['attempts'] ?? 0)) + 1;
        $request->session()->put($key, $pending);
    }

    private function otpError(mixed $pending): string
    {
        if (! is_array($pending) || ($pending['expires_at'] ?? 0) < now()->timestamp) {
            return __('This code has expired. Please request a new one.');
        }

        return ($pending['attempts'] ?? 0) >= 5
            ? __('Too many failed attempts. Please request a new code.')
            : __('The code you entered is incorrect.');
    }

    public function downloadDocument(Document $document): mixed
    {
        $customer = Auth::guard('customer')->user();

        $belongsToCustomer = match ($document->documentable_type) {
            Customer::class => (int) $document->documentable_id === (int) $customer->id,
            Reservation::class => $customer->reservations()->whereKey($document->documentable_id)->exists(),
            Offer::class => $customer->offers()
                ->whereIn('status', ['sent', 'accepted', 'rejected', 'expired'])
                ->whereKey($document->documentable_id)
                ->exists(),
            default => false,
        };

        abort_unless($belongsToCustomer, 403);

        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download($document->file_path, $document->name);
    }
}
