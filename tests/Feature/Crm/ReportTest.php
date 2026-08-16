<?php

declare(strict_types=1);

namespace Tests\Feature\Crm;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_crm_reports_can_be_rendered(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Administrator');

        $this->actingAs($user)
            ->get(route('dashboard.crm.reports.index'))
            ->assertOk()
            ->assertViewIs('crm.reports.index');
    }
}
