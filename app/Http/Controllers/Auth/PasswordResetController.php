<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Show the "forgot password" form.
     */
    public function showForgot(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send the reset link (generic response — no account enumeration).
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email', 'max:255']]);

        $status = Password::broker('users')->sendResetLink($request->only('email'));

        // Always show the same friendly message so an attacker cannot tell
        // whether an account exists for that email.
        return back()->with('status', __($status === Password::RESET_LINK_SENT
            ? 'password.sent'
            : 'password.reset_link_sent_anyway'));
    }

    /**
     * Show the reset form with the signed token.
     */
    public function showReset(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Apply the new password.
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password): void {
                // The User model casts 'password' => 'hashed', so we pass the
                // plain password and let the cast hash it exactly once.
                $user->forceFill(['password' => $password])->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
