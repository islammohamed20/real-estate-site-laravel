<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UnitStatus;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSoldUnitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_visible_sold_units_appear_on_public_pages_without_calculator_links(): void
    {
        [$project, $unit] = $this->createSoldUnit();
        $detailsUrl = route('public.units.show', $unit->id);
        $calculatorUrl = route('installments.index', ['unit_id' => $unit->id]);

        $this->get(route('public.projects.index', ['project' => $project->id, 'building' => $unit->building_id]))
            ->assertOk()
            ->assertSee($unit->unit_number)
            ->assertSee($detailsUrl)
            ->assertDontSee($calculatorUrl);

        $this->get($detailsUrl)
            ->assertOk()
            ->assertViewHas('unit', fn ($publicUnit) => $publicUnit->status?->value === UnitStatus::Sold->value)
            ->assertSee($unit->unit_number)
            ->assertSee('bg-slate-950/85', false)
            ->assertSee('font-extrabold text-white', false)
            ->assertDontSee('color: #000000 !important', false)
            ->assertDontSee($calculatorUrl)
            ->assertDontSee(__('طلب تواصل وحجز الوحدة'))
            ->assertDontSee(__('هل ترغب في حجز هذه الوحدة أو استفسار؟'))
            ->assertDontSee(route('public.inquiries.store'), false);
    }

    public function test_available_unit_details_keep_calculator_and_sales_actions(): void
    {
        [, $unit] = $this->createSoldUnit();
        $unit->update(['status' => UnitStatus::Available]);

        $this->get(route('public.units.show', $unit->id))
            ->assertOk()
            ->assertSee(route('installments.index', ['unit_id' => $unit->id]))
            ->assertSee(__('طلب تواصل وحجز الوحدة'))
            ->assertSee(__('هل ترغب في حجز هذه الوحدة أو استفسار؟'))
            ->assertSee(route('public.inquiries.store'), false);
    }

    public function test_project_building_explorer_shows_sold_units_without_calculator_action(): void
    {
        [$project, $unit] = $this->createSoldUnit();

        $this->get(route('public.projects.show', $project->slug))
            ->assertOk()
            ->assertSee($unit->unit_number)
            ->assertDontSee(route('installments.index', ['unit_id' => $unit->id]));
    }

    private function createSoldUnit(): array
    {
        $project = Project::factory()->create(['status' => 'active']);
        $building = Building::factory()->create([
            'project_id' => $project->id,
            'hidden_from_website' => false,
        ]);
        $floor = Floor::factory()->create([
            'project_id' => $project->id,
            'building_id' => $building->id,
            'number' => 2,
        ]);
        $unit = Unit::factory()->create([
            'project_id' => $project->id,
            'phase_id' => null,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'unit_number' => 'SOLD-A1-02',
            'status' => UnitStatus::Sold,
            'hidden_from_website' => false,
        ]);

        return [$project, $unit];
    }
}
