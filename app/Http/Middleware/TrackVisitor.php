<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\VisitorLog;
use App\Services\Security\DeviceDetector;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($request->isMethod('GET') && ! $request->ajax() && ! $request->wantsJson()) {
                $userAgent = (string) $request->userAgent();
                $detector = app(DeviceDetector::class);

                VisitorLog::create([
                    'ip_address' => $request->ip(),
                    'user_agent' => $userAgent,
                    'device_name' => $detector->deviceName($userAgent),
                    'device_type' => $detector->deviceType($userAgent),
                    'country' => $this->resolveCountry($request->ip()),
                    'page_url' => $request->path(),
                    'referrer' => $request->headers->get('referer'),
                    'visited_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Visitor tracking must never break the public site.
        }

        return $response;
    }

    private function resolveCountry(?string $ip): ?string
    {
        if (! $ip || in_array($ip, ['127.0.0.1', '::1', 'localhost'], true)) {
            return null;
        }

        $cacheKey = 'visitor_country_'.md5($ip);

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($ip): ?string {
            try {
                $response = Http::timeout(3)->get('http://ip-api.com/json/'.$ip);

                if ($response->ok() && $response->json('status') === 'success') {
                    return $response->json('country') ?: null;
                }
            } catch (\Throwable $e) {
                // Offline or unreachable geo service: leave country unknown.
            }

            return null;
        });
    }
}
