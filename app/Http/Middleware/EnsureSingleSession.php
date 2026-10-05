<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleSession
{
    /**
     * Allow only the latest session for each authenticated account.
     */
    public function handle(Request $request, Closure $next): Response
    {
        foreach (['web', 'customer'] as $guard) {
            $user = Auth::guard($guard)->user();

            if ($user === null) {
                continue;
            }

            $sessionId = $request->session()->getId();

            // Accounts created before this feature may not have a recorded session yet.
            // They become protected on their next successful login.
            if ($user->active_session_id === null || hash_equals((string) $user->active_session_id, $sessionId)) {
                continue;
            }

            Auth::guard($guard)->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $guard === 'customer'
                ? redirect()->route('customer.login')->withErrors([
                    'login' => __('This account was signed in from another device. Please log in again.'),
                ])
                : redirect()->route('login')->withErrors([
                    'email' => __('This account was signed in from another device. Please log in again.'),
                ]);
        }

        return $next($request);
    }
}
