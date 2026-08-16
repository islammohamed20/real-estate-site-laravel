<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\InstallmentFrequency;
use App\Enums\UnitStatus;
use App\Models\Building;
use App\Models\Floor;
use App\Models\InstallmentTemplate;
use App\Models\Phase;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        Project::query()->forceDelete();

        // ── Installment templates (extra demo plans) ──────────────────────────
        InstallmentTemplate::query()->updateOrCreate(
            ['code' => 'demo-15-down-24-m'],
            [
                'name' => '15% Down Payment / 24 Monthly Installments',
                'description' => 'Flexible monthly plan with 15% down payment spread over 24 months.',
                'down_payment_percent' => 15,
                'installment_count' => 24,
                'installment_frequency' => InstallmentFrequency::Monthly,
                'maintenance_percent' => 7,
                'discount_percent' => 0,
                'first_installment_offset_months' => 1,
                'is_default' => false,
                'is_active' => true,
                'defaults_json' => ['installment_type' => InstallmentFrequency::Monthly->value],
            ]
        );

        InstallmentTemplate::query()->updateOrCreate(
            ['code' => 'demo-20-down-8-sa'],
            [
                'name' => '20% Down Payment / 8 Semi-Annual Installments',
                'description' => 'Low installment count plan with 20% down payment and 8 semi-annual installments.',
                'down_payment_percent' => 20,
                'installment_count' => 8,
                'installment_frequency' => InstallmentFrequency::SemiAnnual,
                'maintenance_percent' => 7,
                'discount_percent' => 0,
                'first_installment_offset_months' => 0,
                'is_default' => false,
                'is_active' => true,
                'defaults_json' => ['installment_type' => InstallmentFrequency::SemiAnnual->value],
            ]
        );

        // ── Projects ──────────────────────────────────────────────────────────
        $vienna = Project::query()->create([
            'name' => 'Vienna Residence',
            'slug' => 'vienna-residence',
            'code' => 'VIE-2026',
            'description' => 'A master-planned residential community in the heart of New Cairo. Vienna Residence blends contemporary architecture with lush landscaped gardens, offering apartments, townhouses, and penthouses designed for modern living.',
            'location' => 'New Cairo, Cairo',
            'city' => 'Cairo',
            'country' => 'Egypt',
            'status' => 'active',
            'featured' => true,
            'sort_order' => 1,
            'published_at' => now()->subDays(30),
        ]);

        $azure = Project::query()->create([
            'name' => 'Azure Hills',
            'slug' => 'azure-hills',
            'code' => 'AZR-2026',
            'description' => 'Escape to elevated living at Azure Hills, Sheikh Zayed. Premium villas and townhouses set among rolling green hills with panoramic views and resort-style amenities.',
            'location' => 'Sheikh Zayed, Giza',
            'city' => 'Giza',
            'country' => 'Egypt',
            'status' => 'active',
            'featured' => true,
            'sort_order' => 2,
            'published_at' => now()->subDays(14),
        ]);

        $emerald = Project::query()->create([
            'name' => 'Emerald Gate',
            'slug' => 'emerald-gate',
            'code' => 'EMG-2026',
            'description' => 'An exclusive gated enclave on Egypt\'s North Coast. Emerald Gate delivers boutique apartments and penthouses steps from pristine beaches and world-class leisure.',
            'location' => 'North Coast, Alexandria',
            'city' => 'Alexandria',
            'country' => 'Egypt',
            'status' => 'launching',
            'featured' => false,
            'sort_order' => 3,
            'published_at' => null,
        ]);

        // ── Phases ────────────────────────────────────────────────────────────
        $gardenDistrict = Phase::query()->create([
            'project_id' => $vienna->id, 'name' => 'Garden District', 'slug' => 'garden-district',
            'description' => 'Low-rise residences surrounded by landscaped gardens.',
            'status' => 'active', 'sort_order' => 1,
        ]);
        $skyline = Phase::query()->create([
            'project_id' => $vienna->id, 'name' => 'Skyline Towers', 'slug' => 'skyline-towers',
            'description' => 'Mid-rise towers with panoramic city views.',
            'status' => 'active', 'sort_order' => 2,
        ]);
        $meadows = Phase::query()->create([
            'project_id' => $azure->id, 'name' => 'The Meadows', 'slug' => 'the-meadows',
            'description' => 'Villa enclave set across rolling green hills.',
            'status' => 'active', 'sort_order' => 1,
        ]);
        $court = Phase::query()->create([
            'project_id' => $emerald->id, 'name' => 'The Court', 'slug' => 'the-court',
            'description' => 'Boutique residences at the heart of the enclave.',
            'status' => 'active', 'sort_order' => 1,
        ]);

        // ── Buildings & floors ────────────────────────────────────────────────
        $viennaA = $this->makeBuilding($vienna, $gardenDistrict, 'Vienna A', 'VIE-A', 1);
        $viennaB = $this->makeBuilding($vienna, $gardenDistrict, 'Vienna B', 'VIE-B', 2);
        $skylineOne = $this->makeBuilding($vienna, $skyline, 'Skyline One', 'VIE-S1', 1);
        $skylineTwo = $this->makeBuilding($vienna, $skyline, 'Skyline Two', 'VIE-S2', 2);
        $azureOne = $this->makeBuilding($azure, $meadows, 'Azure One', 'AZR-1', 1);
        $azureTwo = $this->makeBuilding($azure, $meadows, 'Azure Two', 'AZR-2', 2);
        $emeraldOne = $this->makeBuilding($emerald, $court, 'Emerald One', 'EMG-1', 1);

        // ── Units ─────────────────────────────────────────────────────────────
        $units = [
            // Vienna Residence — Garden District
            ['building' => $viennaA, 'no' => 101, 'type' => 'Apartment', 'beds' => 2, 'baths' => 2, 'area' => 118, 'ppm' => 950, 'status' => UnitStatus::Available, 'featured' => true],
            ['building' => $viennaA, 'no' => 102, 'type' => 'Apartment', 'beds' => 2, 'baths' => 2, 'area' => 126, 'ppm' => 950, 'status' => UnitStatus::Available],
            ['building' => $viennaA, 'no' => 201, 'type' => 'Apartment', 'beds' => 3, 'baths' => 2, 'area' => 148, 'ppm' => 940, 'status' => UnitStatus::Available],
            ['building' => $viennaA, 'no' => 202, 'type' => 'Apartment', 'beds' => 3, 'baths' => 2, 'area' => 152, 'ppm' => 940, 'status' => UnitStatus::Reserved],
            ['building' => $viennaB, 'no' => 101, 'type' => 'Townhouse', 'beds' => 3, 'baths' => 3, 'area' => 172, 'ppm' => 780, 'garden' => 40, 'gardenPrice' => 18000, 'status' => UnitStatus::Available],
            ['building' => $viennaB, 'no' => 102, 'type' => 'Townhouse', 'beds' => 3, 'baths' => 3, 'area' => 180, 'ppm' => 780, 'status' => UnitStatus::Available],
            // Vienna Residence — Skyline Towers
            ['building' => $skylineOne, 'no' => 201, 'type' => 'Apartment', 'beds' => 3, 'baths' => 3, 'area' => 165, 'ppm' => 1050, 'status' => UnitStatus::Available, 'featured' => true],
            ['building' => $skylineOne, 'no' => 202, 'type' => 'Apartment', 'beds' => 3, 'baths' => 3, 'area' => 170, 'ppm' => 1050, 'status' => UnitStatus::Available],
            ['building' => $skylineOne, 'no' => 301, 'type' => 'Penthouse', 'beds' => 4, 'baths' => 3, 'area' => 210, 'ppm' => 1200, 'roof' => 60, 'roofPrice' => 24000, 'status' => UnitStatus::Available, 'featured' => true],
            ['building' => $skylineTwo, 'no' => 201, 'type' => 'Apartment', 'beds' => 3, 'baths' => 2, 'area' => 142, 'ppm' => 1020, 'status' => UnitStatus::Reserved],
            ['building' => $skylineTwo, 'no' => 202, 'type' => 'Apartment', 'beds' => 2, 'baths' => 2, 'area' => 128, 'ppm' => 1020, 'status' => UnitStatus::Sold],
            // Azure Hills — The Meadows
            ['building' => $azureOne, 'no' => 101, 'type' => 'Villa', 'beds' => 4, 'baths' => 4, 'area' => 285, 'ppm' => 1150, 'garden' => 120, 'gardenPrice' => 42000, 'status' => UnitStatus::Available, 'featured' => true],
            ['building' => $azureOne, 'no' => 102, 'type' => 'Villa', 'beds' => 4, 'baths' => 4, 'area' => 300, 'ppm' => 1100, 'garden' => 150, 'gardenPrice' => 52000, 'status' => UnitStatus::Available],
            ['building' => $azureOne, 'no' => 201, 'type' => 'Duplex', 'beds' => 3, 'baths' => 3, 'area' => 220, 'ppm' => 1080, 'status' => UnitStatus::Available],
            ['building' => $azureTwo, 'no' => 101, 'type' => 'Townhouse', 'beds' => 3, 'baths' => 3, 'area' => 176, 'ppm' => 920, 'garden' => 30, 'gardenPrice' => 12000, 'status' => UnitStatus::Available],
            ['building' => $azureTwo, 'no' => 102, 'type' => 'Townhouse', 'beds' => 3, 'baths' => 3, 'area' => 184, 'ppm' => 920, 'status' => UnitStatus::Reserved],
            ['building' => $azureTwo, 'no' => 201, 'type' => 'Apartment', 'beds' => 3, 'baths' => 2, 'area' => 138, 'ppm' => 980, 'status' => UnitStatus::Available],
            // Emerald Gate — The Court
            ['building' => $emeraldOne, 'no' => 101, 'type' => 'Apartment', 'beds' => 2, 'baths' => 2, 'area' => 112, 'ppm' => 1250, 'status' => UnitStatus::Available],
            ['building' => $emeraldOne, 'no' => 102, 'type' => 'Apartment', 'beds' => 2, 'baths' => 2, 'area' => 120, 'ppm' => 1250, 'status' => UnitStatus::Available],
            ['building' => $emeraldOne, 'no' => 201, 'type' => 'Apartment', 'beds' => 3, 'baths' => 2, 'area' => 135, 'ppm' => 1220, 'status' => UnitStatus::Available],
            ['building' => $emeraldOne, 'no' => 202, 'type' => 'Penthouse', 'beds' => 4, 'baths' => 3, 'area' => 205, 'ppm' => 1350, 'roof' => 55, 'roofPrice' => 20000, 'status' => UnitStatus::Sold],
        ];

        foreach ($units as $index => $unit) {
            $this->makeUnit($unit, $index + 1);
        }

        $this->command?->info('Demo catalog seeded: 3 projects, '.Phase::count().' phases, '.Building::count().' buildings, '.Floor::count().' floors, '.Unit::count().' units.');
    }

    private function makeBuilding(Project $project, Phase $phase, string $name, string $code, int $sortOrder): Building
    {
        $building = Building::query()->create([
            'project_id' => $project->id,
            'phase_id' => $phase->id,
            'name' => $name,
            'code' => $code,
            'status' => 'active',
            'sort_order' => $sortOrder,
        ]);

        foreach ([0, 1, 2, 3] as $floorNumber) {
            Floor::query()->create([
                'project_id' => $project->id,
                'phase_id' => $phase->id,
                'building_id' => $building->id,
                'number' => $floorNumber,
                'name' => $floorNumber === 0 ? 'Ground' : 'Floor '.$floorNumber,
                'sort_order' => $floorNumber,
            ]);
        }

        return $building;
    }

    private function makeUnit(array $unit, int $sortOrder): void
    {
        $building = $unit['building'];
        $floorNumber = intdiv((int) $unit['no'], 100);
        $floor = $building->floors()->where('number', $floorNumber)->firstOrFail();
        $area = (float) $unit['area'];
        $pricePerMeter = (float) $unit['ppm'];
        $gardenPrice = (float) ($unit['gardenPrice'] ?? 0);
        $roofPrice = (float) ($unit['roofPrice'] ?? 0);
        $currentPrice = ($area * $pricePerMeter) + $gardenPrice + $roofPrice;

        Unit::query()->create([
            'project_id' => $building->project_id,
            'phase_id' => $building->phase_id,
            'building_id' => $building->id,
            'floor_id' => $floor->id,
            'unit_number' => $building->code.'-'.$unit['no'],
            'unit_type' => $unit['type'],
            'bedrooms' => $unit['beds'],
            'bathrooms' => $unit['baths'],
            'area' => $area,
            'garden_area' => $unit['garden'] ?? 0,
            'roof_area' => $unit['roof'] ?? 0,
            'balcony_area' => $unit['balcony'] ?? 0,
            'price_per_meter' => $pricePerMeter,
            'garden_price' => $gardenPrice,
            'roof_price' => $roofPrice,
            'current_price' => $currentPrice,
            'status' => $unit['status'],
            'featured' => $unit['featured'] ?? false,
            'hidden_from_website' => false,
            'sort_order' => $sortOrder,
        ]);
    }
}
