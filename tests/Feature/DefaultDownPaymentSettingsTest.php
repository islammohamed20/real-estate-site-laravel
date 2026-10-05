<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UnitStatus;
use App\Models\Building;
use App\Models\Floor;
use App\Models\InstallmentTemplate;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultDownPaymentSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_down_payment_is_editable_and_applies_to_public_calculators(): void
    {
        $this->seed(PermissionSeeder::class);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrator');
        $template = InstallmentTemplate::factory()->create([
            'is_default' => true,
            'is_active' => true,
            'down_payment_percent' => 10,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.settings.index'))
            ->assertOk()
            ->assertSee('name="default_down_payment_percent"', false)
            ->assertSee('value="10.00"', false);

        $this->actingAs($admin)
            ->put(route('dashboard.settings.update'), [
                'name' => 'Venecia Developments',
                'maintenance_percent' => 7,
                'trash_retention_days' => 30,
                'default_down_payment_percent' => 25,
            ])
            ->assertSessionHas('status');

        $this->assertSame('25.00', $template->fresh()->down_payment_percent);

        $this->get(route('installments.index'))
            ->assertOk()
            ->assertViewHas('calcDefaults', fn (array $defaults): bool => (float) $defaults['down_payment_percent'] === 25.0);

        $project = Project::factory()->create();
        $building = Building::factory()->create(['project_id' => $project->id]);
        $floor = Floor::factory()->create(['project_id' => $project->id, 'building_id' => $building->id]);
        $unit = Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'status' => UnitStatus::Available,
            'hidden_from_website' => false,
            'current_price' => 1_000_000,
        ]);

        $this->get(route('public.units.show', $unit->id))
            ->assertOk()
            ->assertViewHas('downPaymentPercent', 25.0)
            ->assertSee('Down Payment (25%)');
    }
}
