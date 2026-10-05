<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Building;
use App\Models\CompanyProfile;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\InstallmentTemplate;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_and_footer_render_with_social_links(): void
    {
        CompanyProfile::query()->create([
            'id' => 1,
            'name' => 'Venecia Developments',
            'phone' => '201000000000',
            'email' => 'info@venecia-dev.com',
            'website' => 'https://venecia-dev.com',
            'facebook_url' => 'https://facebook.com/venecia',
            'messenger_url' => 'https://m.me/venecia',
            'instagram_url' => 'https://instagram.com/venecia',
        ]);

        $response = $this->get(route('customer.register'));

        $response->assertOk();
        $response->assertSee('facebook.com/venecia', false);
        $response->assertSee('instagram.com/venecia', false);
        $response->assertSee('m.me/venecia', false);
        $response->assertSee('wa.me', false);
    }

    public function test_settings_page_renders_social_link_fields(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Administrator');

        $this->actingAs($user)
            ->get(route('dashboard.settings.index'))
            ->assertOk()
            ->assertSee('name="facebook_url"', false)
            ->assertSee('name="messenger_url"', false)
            ->assertSee('name="instagram_url"', false)
            ->assertSee('name="seo_title"', false)
            ->assertSee('name="seo_description"', false)
            ->assertSee('name="seo_image"', false);
    }

    public function test_settings_update_saves_seo_fields(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Administrator');

        $this->actingAs($user)->put(route('dashboard.settings.update'), [
            'name' => 'Venecia Developments',
            'maintenance_percent' => 7,
            'trash_retention_days' => 30,
            'seo_title' => 'Venecia Developments — Luxury Apartments',
            'seo_description' => 'Premium real estate developments with flexible installment plans.',
            'auto_purge_enabled' => 1,
        ])->assertSessionHas('status');

        $profile = CompanyProfile::query()->firstOrFail();

        $this->assertSame('Venecia Developments — Luxury Apartments', $profile->seo_title);
        $this->assertSame('Premium real estate developments with flexible installment plans.', $profile->seo_description);
    }

    public function test_public_pages_render_social_sharing_meta_tags(): void
    {
        CompanyProfile::query()->create([
            'id' => 1,
            'name' => 'Venecia Developments',
            'website' => 'https://venecia-dev.com',
            'seo_title' => 'Venecia Developments — Luxury Apartments',
            'seo_description' => 'Premium real estate developments with flexible installment plans.',
            'seo_image_path' => 'https://venecia-dev.com/storage/seo/preview.png',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('og:title', false);
        $response->assertSee('Venecia Developments — Luxury Apartments', false);
        $response->assertSee('og:description', false);
        $response->assertSee('Premium real estate developments with flexible installment plans.', false);
        $response->assertSee('og:image', false);
        $response->assertSee('https://venecia-dev.com/storage/seo/preview.png', false);
        $response->assertSee('twitter:card', false);
        $response->assertSee('name="description"', false);
        $response->assertSee('rel="canonical"', false);
    }

    public function test_settings_update_saves_social_and_phone_fields(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Administrator');

        $this->actingAs($user)->put(route('dashboard.settings.update'), [
            'name' => 'Venecia Developments',
            'maintenance_percent' => 7,
            'trash_retention_days' => 30,
            'phone' => '201000000000',
            'email' => 'info@venecia-dev.com',
            'facebook_url' => 'https://facebook.com/venecia',
            'instagram_url' => 'https://instagram.com/venecia',
            'auto_purge_enabled' => 1,
        ])->assertSessionHas('status');

        $profile = CompanyProfile::query()->firstOrFail();

        $this->assertSame('201000000000', $profile->phone);
        $this->assertSame('https://facebook.com/venecia', $profile->facebook_url);
        $this->assertSame('https://instagram.com/venecia', $profile->instagram_url);
    }

    public function test_verify_email_page_renders_for_pending_registration(): void
    {
        $customer = Customer::query()->create([
            'name' => 'Ahmed Test',
            'phone' => '01000000001',
            'email' => 'ahmed@example.com',
            'password' => bcrypt('secretpass123'),
        ]);

        $this->withSession(['customer_pending_verification_id' => $customer->id])
            ->get(route('customer.verify.show'))
            ->assertOk()
            ->assertSee('ahmed@example.com');
    }

    public function test_unit_details_use_the_unit_id_when_unit_numbers_repeat(): void
    {
        InstallmentTemplate::factory()->create([
            'is_default' => true,
            'is_active' => true,
            'down_payment_percent' => 10,
        ]);

        $project = Project::factory()->create(['max_installment_years' => 5]);
        $building = Building::factory()->create(['project_id' => $project->id]);
        $floor = Floor::factory()->create(['project_id' => $project->id, 'building_id' => $building->id]);
        $firstUnit = Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'unit_number' => 'شقة 1',
            'area' => 165,
        ]);
        $secondUnit = Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'unit_number' => 'شقة 1',
            'area' => 125,
            'current_price' => 1000000,
        ]);

        $this->get(route('public.units.show', $secondUnit->id))
            ->assertOk()
            ->assertSee('45,000')
            ->assertSee('125')
            ->assertDontSee('165 m²');

        $this->assertNotSame($firstUnit->id, $secondUnit->id);
    }

    public function test_public_project_building_picker_shows_buildings_with_visible_sold_units(): void
    {
        $project = Project::factory()->create(['status' => 'active']);
        $availableBuilding = Building::factory()->create(['project_id' => $project->id, 'name' => 'Available Building']);
        $soldBuilding = Building::factory()->create(['project_id' => $project->id, 'name' => 'Sold Building']);
        $availableFloor = Floor::factory()->create(['project_id' => $project->id, 'building_id' => $availableBuilding->id]);
        $soldFloor = Floor::factory()->create(['project_id' => $project->id, 'building_id' => $soldBuilding->id]);

        Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $availableBuilding->id,
            'floor_id' => $availableFloor->id,
            'status' => 'available',
        ]);
        Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $soldBuilding->id,
            'floor_id' => $soldFloor->id,
            'status' => 'sold',
        ]);

        $this->get(route('public.projects.index', ['project' => $project->id]))
            ->assertOk()
            ->assertSee('Available Building')
            ->assertSee('Sold Building');
    }

    public function test_unit_details_show_project_based_plans_and_building_name(): void
    {
        $project = Project::factory()->create(['max_installment_years' => 7]);
        $building = Building::factory()->create(['project_id' => $project->id, 'name' => 'برج النخيل']);
        $floor = Floor::factory()->create(['project_id' => $project->id, 'building_id' => $building->id]);
        $unit = Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'current_price' => 1000000,
        ]);

        // 10% down = 100,000 ; remaining = 900,000
        // 7-year quarterly: 900,000 / 28 = 32,142.857...
        // 7-year monthly: 900,000 / 84 = 10,714.285...
        $this->get(route('public.units.show', $unit->id))
            ->assertOk()
            ->assertSee('برج النخيل')
            ->assertDontSee('خطة 4 سنوات أقساط ربع سنوية')
            ->assertDontSee('خطة 48 شهر أقساط شهرية');
    }
}
