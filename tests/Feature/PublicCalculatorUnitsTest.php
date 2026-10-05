<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UnitStatus;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCalculatorUnitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_calculator_only_lists_visible_available_units(): void
    {
        $availableUnit = Unit::factory()->create([
            'status' => UnitStatus::Available,
            'hidden_from_website' => false,
        ]);
        $availableUnit->project->update(['status' => 'active']);

        $soldUnit = Unit::factory()->create([
            'status' => UnitStatus::Sold,
            'hidden_from_website' => false,
        ]);
        $soldUnit->project->update(['status' => 'active']);

        $hiddenUnit = Unit::factory()->create([
            'status' => UnitStatus::Available,
            'hidden_from_website' => true,
        ]);
        $hiddenUnit->project->update(['status' => 'active']);

        $response = $this->get(route('installments.index'));
        $response->assertOk();
        $response->assertViewHas('units', function ($units) use ($availableUnit, $soldUnit, $hiddenUnit): bool {
            return $units->contains('id', $availableUnit->id)
                && ! $units->contains('id', $soldUnit->id)
                && ! $units->contains('id', $hiddenUnit->id);
        });
    }

    public function test_public_calculator_does_not_preselect_unavailable_unit_from_query_string(): void
    {
        $unit = Unit::factory()->create([
            'status' => UnitStatus::Sold,
            'hidden_from_website' => true,
        ]);
        $unit->project->update(['status' => 'active']);

        $response = $this->get(route('installments.index', ['unit_id' => $unit->id]));

        $response->assertOk()
            ->assertViewHas('preselectedUnit', null)
            ->assertViewHas('selectedUnitId', '')
            ->assertViewHas('requestedUnitUnavailable', true)
            ->assertSee(__('The selected unit is no longer available. Please choose an available unit.'));
    }

    public function test_public_calculator_can_preselect_visible_available_unit(): void
    {
        $unit = Unit::factory()->create([
            'status' => UnitStatus::Available,
            'hidden_from_website' => false,
        ]);
        $unit->project->update(['status' => 'active']);

        $this->get(route('installments.index', ['unit_id' => $unit->id]))
            ->assertOk()
            ->assertViewHas('preselectedUnit', fn ($selected) => $selected?->is($unit) === true)
            ->assertViewHas('selectedUnitId', $unit->id)
            ->assertViewHas('requestedUnitUnavailable', false);
    }
}
