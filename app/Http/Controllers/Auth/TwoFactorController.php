<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Security\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor)
    {
    }

    /**
     * One-time code entry shown right after the password login.
     */
    public function showChallenge(): View
    {
        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the TOTP code (or a one-time recovery code) for the session.
     */
    public function verifyChallenge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $code = (string) $validated['code'];

        $ok = $user->two_factor_secret !== null && $this->twoFactor->verify($user->two_factor_secret, $code);

        if (! $ok) {
            $ok = $this->twoFactor->verifyRecoveryCode($user, $code);
        }

        if (! $ok) {
            return back()->withErrors(['code' => __('The provided code is invalid.')]);
        }

        $request->session()->put('2fa:verified', true);
        $request->session()->forget('2fa:user:id');

        return redirect()->intended(route('dashboard.home'));
    }

    /**
     * Show the QR code + secret + confirmation form (enable flow).
     */
    public function showSecurityPage(): View
    {
        /** @var User $user */
        $user = auth()->user();

        return view('settings.security', [
            'user' => $user,
            'histories' => $user?->loginHistories()->latest('logged_in_at')->limit(10)->get() ?? collect(),
        ]);
    }

    public function showEnableForm(): View
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->two_factor_enabled) {
            return redirect()->route('dashboard.security');
        }

        $secret = session('2fa:pending:secret');

        if (! is_string($secret) || $secret === '') {
            $secret = $this->twoFactor->generateSecret();
            session(['2fa:pending:secret' => $secret]);
        }

        $provisioningUri = $this->twoFactor->provisioningUri($user, $secret);

        return view('settings.two-factor-enable', [
            'secret' => $secret,
            'qrCode' => $this->twoFactor->qrCodeDataUri($provisioningUri),
        ]);
    }

    /**
     * Confirm the scanned secret with a live code, then enable 2FA and
     * hand out the one-time recovery codes.
     */
    public function enable(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        /** @var User $user */
        $user = auth()->user();
        $secret = session('2fa:pending:secret');

        if (! is_string($secret) || $secret === '' || ! $this->twoFactor->verify($secret, (string) $validated['code'])) {
            return back()->withErrors(['code' => __('The provided code is invalid.')]);
        }

        $codes = $this->twoFactor->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => array_map(
                fn (string $code): string => $this->twoFactor->hashRecoveryCode($code),
                $codes
            ),
            'two_factor_enabled' => true,
        ])->save();

        $request->session()->forget('2fa:pending:secret');
        $request->session()->put('2fa:verified', true);
        $request->session()->flash('2fa:recovery_codes', $codes);

        return redirect()->route('dashboard.settings.index')->with('status', __('Two-factor authentication enabled.'));
    }

    /**
     * Disable 2FA — requires a valid TOTP or recovery code first.
     */
    public function disable(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        /** @var User $user */
        $user = auth()->user();
        $code = (string) $validated['code'];

        $ok = $user->two_factor_secret !== null && $this->twoFactor->verify($user->two_factor_secret, $code);

        if (! $ok) {
            $ok = $this->twoFactor->verifyRecoveryCode($user, $code);
        }

        if (! $ok) {
            return back()->withErrors(['2fa_disable' => __('The provided code is invalid.')]);
        }

        $user->forceFill([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        $request->session()->forget('2fa:verified');

        return back()->with('status', __('Two-factor authentication disabled.'));
    }

    /**
     * Replace the current recovery codes with a fresh set.
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->two_factor_enabled) {
            return back()->withErrors(['2fa' => __('Two-factor authentication is not enabled.')]);
        }

        $codes = $this->twoFactor->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => array_map(
                fn (string $code): string => $this->twoFactor->hashRecoveryCode($code),
                $codes
            ),
        ])->save();

        $request->session()->flash('2fa:recovery_codes', $codes);

        return back()->with('status', __('Recovery codes regenerated.'));
    }
}
