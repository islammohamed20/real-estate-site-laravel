<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerAuthenticated
{
    /**
     * Guard for the customer portal — redirects guests to the customer login page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('customer')->check()) {
            return redirect()->guest(route('customer.login'));
        }

        return $next($request);
    }
}
