<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Redirect already-authenticated users away from guest pages.
     * Admins (web guard) go to the dashboard; customers go to their account.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? ['web'] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect()->intended(
                    $guard === 'customer' ? route('customer.account') : route('dashboard.home')
                );
            }
        }

        return $next($request);
    }
}
