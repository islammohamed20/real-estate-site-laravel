<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_xml_is_served_with_static_public_urls(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/xml; charset=UTF-8');

        $content = (string) $response->getContent();
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString('<loc>'.route('home').'</loc>', $content);
        $this->assertStringContainsString(route('public.projects.index'), $content);
        $this->assertStringContainsString(route('public.portfolio'), $content);
        $this->assertStringContainsString(route('public.about'), $content);
        $this->assertStringContainsString(route('public.contact'), $content);
        $this->assertStringContainsString(route('installments.index'), $content);
        $this->assertStringNotContainsString('real-statement-control', $content);
    }

    public function test_sitemap_includes_projects_and_visible_units_only(): void
    {
        $project = Project::factory()->create();
        $visibleUnit = Unit::factory()->create(['hidden_from_website' => false]);
        $hiddenUnit = Unit::factory()->create(['hidden_from_website' => true]);

        $content = (string) $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString(route('public.projects.show', $project->slug), $content);
        $this->assertStringContainsString(route('public.units.show', $visibleUnit), $content);
        $this->assertStringNotContainsString(route('public.units.show', $hiddenUnit), $content);
    }

    public function test_sitemap_xml_is_valid_xml(): void
    {
        $content = (string) $this->get('/sitemap.xml')->assertOk()->getContent();

        $xml = simplexml_load_string($content);

        $this->assertNotFalse($xml);
        $this->assertSame('urlset', $xml->getName());
        $this->assertGreaterThanOrEqual(6, count($xml->url));
    }

    public function test_robots_txt_allows_public_site_and_points_to_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/plain; charset=UTF-8');

        $content = (string) $response->getContent();
        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Allow: /', $content);
        $this->assertStringContainsString('Disallow: /real-statement-control', $content);
        $this->assertStringContainsString('Sitemap:', $content);
        $this->assertStringContainsString('sitemap.xml', $content);
    }
}
