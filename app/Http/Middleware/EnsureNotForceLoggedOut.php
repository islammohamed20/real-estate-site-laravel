<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotForceLoggedOut
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->force_logout_at === null) {
            return $next($request);
        }

        $sessionStartedAt = $request->session()->get('auth_initiated_at');

        $forced = match (true) {
            is_int($sessionStartedAt) => $user->force_logout_at->getTimestamp() > $sessionStartedAt,
            $user->last_login_at !== null => $user->force_logout_at->greaterThan($user->last_login_at),
            default => true,
        };

        if ($forced) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => __('Your session has been terminated by an administrator. Please log in again.'),
            ]);
        }

        return $next($request);
    }
}
