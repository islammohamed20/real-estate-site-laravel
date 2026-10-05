<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Block the desktop-only project layout page from running on mobile devices.
 *
 * The layout page relies on a wide visual canvas (buildings × floors × units)
 * and complex Alpine interactions that are unusable on small screens. Instead
 * of rendering a broken UI we redirect mobile visitors back to the project
 * show page with an informational flash message.
 */
class BlockMobileLayout
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isMobile($request)) {
            $project = $request->route('project');

            return redirect()
                ->route('dashboard.projects.edit', $project)
                ->with('status', __('The 3D layout is optimized for desktop screens and is not available on mobile devices.'));
        }

        return $next($request);
    }

    private function isMobile(Request $request): bool
    {
        $ua = (string) $request->userAgent();

        if (preg_match('/(android|iphone|ipod|ipad|windows phone|blackberry|symbian|mobile|opera mini|opera mobi|webos|kindle|silk)/i', $ua)) {
            return true;
        }

        // Respect the viewport hint sent by some browsers.
        if ($request->headers->has('Cloudfront-Is-Mobile-Viewer') && $request->headers->get('Cloudfront-Is-Mobile-Viewer') === 'true') {
            return true;
        }

        return false;
    }
}
