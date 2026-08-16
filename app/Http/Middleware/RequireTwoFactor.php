<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Block authenticated users who have 2FA enabled until they pass the
 * one-time challenge for the current session.
 */
class RequireTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->two_factor_enabled && ! $request->session()->get('2fa:verified')) {
            $request->session()->put('2fa:user:id', $user->id);

            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }
}
