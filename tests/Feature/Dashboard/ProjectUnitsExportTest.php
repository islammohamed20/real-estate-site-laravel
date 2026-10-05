<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectUnitsExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_excludes_hidden_buildings_by_default_and_can_include_them(): void
    {
        $role = Role::findOrCreate('Administrator', 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole($role);

        $project = Project::factory()->create(['name' => 'Venecia Export']);
        $visibleBuilding = Building::factory()->create([
            'project_id' => $project->id,
            'name' => 'Visible Building',
            'hidden_from_website' => false,
        ]);
        $hiddenBuilding = Building::factory()->create([
            'project_id' => $project->id,
            'name' => 'Hidden Building',
            'hidden_from_website' => true,
        ]);
        $visibleFloor = Floor::factory()->create(['project_id' => $project->id, 'building_id' => $visibleBuilding->id]);
        $hiddenFloor = Floor::factory()->create(['project_id' => $project->id, 'building_id' => $hiddenBuilding->id]);
        Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $visibleBuilding->id,
            'floor_id' => $visibleFloor->id,
            'unit_number' => 'VISIBLE-101',
            'status' => 'available',
        ]);
        Unit::factory()->create([
            'project_id' => $project->id,
            'building_id' => $hiddenBuilding->id,
            'floor_id' => $hiddenFloor->id,
            'unit_number' => 'HIDDEN-201',
            'status' => 'sold',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.projects.index'))
            ->assertOk()
            ->assertSee('name="columns[]"', false)
            ->assertSee(__('Include hidden buildings'));

        $visibleResponse = $this->actingAs($admin)
            ->get(route('dashboard.projects.units-report', $project))
            ->assertOk()
            ->assertDownload('venecia-export-units-'.now()->format('Y-m-d').'.xlsm');
        $visibleRows = $this->spreadsheetRows($visibleResponse->baseResponse->getFile()->getPathname());
        $this->assertStringContainsString('VISIBLE-101', json_encode($visibleRows));
        $this->assertStringNotContainsString('HIDDEN-201', json_encode($visibleRows));

        $allResponse = $this->get(route('dashboard.projects.units-report', [$project, 'include_hidden' => 1]))
            ->assertOk();
        $allRows = $this->spreadsheetRows($allResponse->baseResponse->getFile()->getPathname());
        $this->assertStringContainsString('VISIBLE-101', json_encode($allRows));
        $this->assertStringContainsString('HIDDEN-201', json_encode($allRows));

        $selectedResponse = $this->get(route('dashboard.projects.units-report', [
            $project,
            'columns' => ['building', 'status'],
        ]))->assertOk();
        $selectedRows = $this->spreadsheetRows($selectedResponse->baseResponse->getFile()->getPathname());
        $this->assertSame([__('Building'), __('Status')], $selectedRows[3]);
        $this->assertCount(2, $selectedRows[4]);
    }

    private function spreadsheetRows(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray();
        $spreadsheet->disconnectWorksheets();

        return $rows;
    }
}
