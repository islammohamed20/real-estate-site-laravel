<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsEvolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_evolution_settings_are_saved_and_blank_key_preserves_existing_key(): void
    {
        $this->seed(PermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrator');

        $payload = [
            'name' => 'Venecia Developments',
            'maintenance_percent' => 0,
            'trash_retention_days' => 30,
            'evolution_api_url' => 'https://evolution.example.test',
            'evolution_api_key' => 'test-evolution-key',
            'evolution_instance_name' => 'Venecia',
        ];

        $this->actingAs($admin)
            ->put(route('dashboard.settings.update'), $payload)
            ->assertRedirect();

        $profile = CompanyProfile::query()->firstOrFail();
        $this->assertSame('https://evolution.example.test', $profile->evolution_api_url);
        $this->assertSame('test-evolution-key', $profile->evolution_api_key);
        $this->assertSame('Venecia', $profile->evolution_instance_name);

        $this->put(route('dashboard.settings.update'), [...$payload, 'evolution_api_key' => ''])
            ->assertRedirect();

        $this->assertSame('test-evolution-key', $profile->fresh()->evolution_api_key);
    }
}
