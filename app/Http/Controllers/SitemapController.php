<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('public.projects.index'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('public.portfolio'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('public.about'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('public.contact'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('installments.index'), 'changefreq' => 'monthly', 'priority' => '0.6'],
        ];

        Project::query()
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at'])
            ->each(function (Project $project) use (&$urls): void {
                $urls[] = [
                    'loc' => route('public.projects.show', $project->slug),
                    'lastmod' => $project->updated_at?->toW3cString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            });

        Unit::query()
            ->publiclyVisible()
            ->orderByDesc('updated_at')
            ->get(['id', 'updated_at'])
            ->each(function (Unit $unit) use (&$urls): void {
                $urls[] = [
                    'loc' => route('public.units.show', $unit),
                    'lastmod' => $unit->updated_at?->toW3cString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ];
            });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function robots(): Response
    {
        $sitemap = rtrim((string) config('app.url'), '/').'/sitemap.xml';

        $lines = [
            'User-agent: *',
            'Allow: /',
            '',
            'Disallow: /real-statement-control',
            'Disallow: /account',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /verify-email',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /webhook/',
            'Disallow: /api/',
            'Disallow: /livewire/',
            '',
            'Sitemap: '.$sitemap,
            '',
        ];

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
