<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_portfolio_page_renders_successfully_with_all_key_information(): void
    {
        $response = $this->get(route('public.portfolio'));

        $response->assertOk();
        $response->assertViewIs('public.portfolio');

        // Alliance details & Date
        $response->assertSee('2024');
        $response->assertSee('تحالف استثماري عقاري رائد');
        $response->assertSee('فينيسيا للتنمية العمرانية');

        // Current flagship projects
        $response->assertSee('المشروع الطبي التخصصي');
        $response->assertSee('2,365');
        $response->assertSee('400');
        $response->assertSee('مول العرب');
        $response->assertSee('70,230');
        $response->assertSee('أسيوط الجديدة');

        // 4 Partners and their companies
        $response->assertSee('شركة الأدهم للاستثمار والتطوير العقاري');
        $response->assertSee('محمد عبد السلام محمد الشافعي');
        $response->assertSee('مول الخان');

        $response->assertSee('شركة الهدى للاستثمار والتطوير العقاري');
        $response->assertSee('أحمد عبد الإله أحمد عبد الإله');
        $response->assertSee('مول الهدى');
        $response->assertSee('مجمع مدارس طيبة');

        $response->assertSee('حسين سمير إسماعيل إبراهيم');
        $response->assertSee('كمبوند فينيسيا');
        $response->assertSee('شركة الرسالة');

        $response->assertSee('شركة رشدي للاستثمار العقاري');
        $response->assertSee('أحمد رشدي توفيق');
        $response->assertSee('زايد لاجونز');
        $response->assertSee('الشيخ زايد');
        $response->assertSee('إليت');
        $response->assertSee('الربوة');
    }

    public function test_portfolio_pdf_download_route_serves_pdf(): void
    {
        $response = $this->get(route('public.portfolio.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_track_record_alias_redirects_to_portfolio(): void
    {
        $response = $this->get('/track-record');

        $response->assertRedirect(route('public.portfolio'));
    }

    public function test_public_pages_include_portfolio_link_in_navigation_and_footer(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('public.portfolio'), false);
    }
}
