<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicUnitGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_image_gallery_hides_inactive_carousel_controls(): void
    {
        $unit = Unit::factory()->create([
            'images' => ["units/gallery/o'neil-plan.png"],
            'thumbnail' => null,
            'floor_plan_path' => 'units/gallery/floor-plan.jpg',
        ]);

        $this->get(route('public.units.show', $unit->id))
            ->assertOk()
            ->assertDontSee('galleryImages.indexOf(activeImage) + 1', false)
            ->assertDontSee('Thumbnail 1', false)
            ->assertSee(__('Floor Plan'))
            ->assertSee('units/gallery/floor-plan.jpg', false);
    }

    public function test_multiple_image_gallery_selects_thumbnails_by_safe_array_index(): void
    {
        $unit = Unit::factory()->create([
            'images' => ['units/gallery/front.png', 'units/gallery/living-room.png'],
            'thumbnail' => null,
        ]);

        $this->get(route('public.units.show', $unit->id))
            ->assertOk()
            ->assertSee('galleryImages.indexOf(activeImage) + 1', false)
            ->assertSee('activeImage = galleryImages[0]', false)
            ->assertSee('activeImage = galleryImages[1]', false)
            ->assertSee('Thumbnail 2', false);
    }
}
