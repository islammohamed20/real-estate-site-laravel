<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasskeyConfirmed
{
    public function handle(Request $request, Closure $next): Response
    {
        $confirmedAt = $request->session()->get('passkey_confirmed_at');

        if (! $confirmedAt || now()->diffInMinutes($confirmedAt) > 5) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => __('Passkey confirmation required.')], 403);
            }

            return redirect()->back()->with('status', __('Passkey confirmation required. Please confirm the action with your passkey.'));
        }

        return $next($request);
    }
}
