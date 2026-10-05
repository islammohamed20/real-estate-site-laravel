<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitMapVisibleTest extends TestCase
{
    use RefreshDatabase;

    public function test_unit_edit_form_shows_map_fields_and_iframe(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrator');

        $project = Project::factory()->create();
        $building = Building::factory()->create(['project_id' => $project->id]);
        $floor = Floor::factory()->create(['building_id' => $building->id, 'project_id' => $project->id]);
        $unit = Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'map_lat' => '30.0444',
            'map_lng' => '31.2357',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.projects.units.edit', [$project, $unit]))
            ->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('id="map_lat"', $html);
        $this->assertStringContainsString('id="map_lng"', $html);
        $this->assertStringContainsString('id="unit-map-iframe"', $html);
        $this->assertMatchesRegularExpression('/(google\.com\/maps|openstreetmap\.org)/', $html);
    }
}
