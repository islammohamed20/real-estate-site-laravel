<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\CrmActivityNotification;
use App\Support\NotificationRegistry;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationPreferenceTest extends TestCase
{
    use RefreshDatabase;

    private function seedPermissions(): void
    {
        $this->seed(PermissionSeeder::class);
    }

    public function test_recipients_are_gated_by_permission(): void
    {
        $this->seedPermissions();

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrator');

        $manager = User::factory()->create(['is_active' => true]);
        $manager->assignRole('Sales Manager');

        $executive = User::factory()->create(['is_active' => true]);
        $executive->assignRole('Sales Executive');

        // A user with only the customer-notification permission.
        $custom = User::factory()->create(['is_active' => true]);
        $custom->givePermissionTo('receive notification.customer');

        $recipients = NotificationRegistry::recipients('customer');

        $this->assertTrue($recipients->contains('id', $admin->id), 'Administrator should receive');
        $this->assertTrue($recipients->contains('id', $manager->id), 'Sales Manager should receive');
        $this->assertTrue($recipients->contains('id', $custom->id), 'Custom user with permission should receive');
        $this->assertFalse($recipients->contains('id', $executive->id), 'Sales Executive has no notification permission');

        // A type nobody outside managers is granted: security alerts.
        $securityRecipients = NotificationRegistry::recipients('login_failed');

        $this->assertTrue($securityRecipients->contains('id', $admin->id));
        $this->assertFalse($securityRecipients->contains('id', $custom->id), 'Custom user lacks security permission');
    }

    public function test_per_user_opt_out_excludes_that_type_only(): void
    {
        $this->seedPermissions();

        $manager = User::factory()->create(['is_active' => true]);
        $manager->assignRole('Sales Manager');
        $manager->forceFill(['notification_preferences' => ['customer']])->save();

        $recipients = NotificationRegistry::recipients('customer');
        $this->assertFalse($recipients->contains('id', $manager->id), 'Opted-out user must not get customer alerts');

        $recipients = NotificationRegistry::recipients('offer');
        $this->assertTrue($recipients->contains('id', $manager->id), 'Other types still delivered');
    }

    public function test_wildcard_all_permission_receives_every_type(): void
    {
        $this->seedPermissions();

        $wildcard = User::factory()->create(['is_active' => true]);
        $wildcard->givePermissionTo('receive notification.all');

        $this->assertTrue(NotificationRegistry::recipients('login_failed')->contains('id', $wildcard->id));
        $this->assertTrue(NotificationRegistry::recipients('trash_warning')->contains('id', $wildcard->id));
    }

    public function test_notify_managers_honors_permission_and_preferences(): void
    {
        $this->seedPermissions();
        Notification::fake();

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrator');

        $optedOut = User::factory()->create(['is_active' => true]);
        $optedOut->assignRole('Sales Manager');
        $optedOut->forceFill(['notification_preferences' => ['offer']])->save();

        CrmActivityNotification::notifyManagers(new CrmActivityNotification('offer', [
            'offer_number' => 'OF-100',
            'customer_name' => 'Ahmed',
            'action_url' => '#',
        ]));

        Notification::assertSentTo($admin, CrmActivityNotification::class);
        Notification::assertNotSentTo($optedOut, CrmActivityNotification::class);
    }

    public function test_fallback_to_roles_when_permissions_not_seeded(): void
    {
        // No PermissionSeeder — the notification permissions do not exist yet.
        $adminRole = Role::findOrCreate('Administrator', 'web');
        $managerRole = Role::findOrCreate('Sales Manager', 'web');

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole($adminRole);

        $manager = User::factory()->create(['is_active' => true]);
        $manager->assignRole($managerRole);

        $other = User::factory()->create(['is_active' => true]);
        $other->assignRole(Role::findOrCreate('Viewer', 'web'));

        $recipients = NotificationRegistry::recipients('customer');

        $this->assertTrue($recipients->contains('id', $admin->id));
        $this->assertTrue($recipients->contains('id', $manager->id));
        $this->assertFalse($recipients->contains('id', $other->id));
    }

    public function test_settings_page_saves_personal_preferences(): void
    {
        $this->seedPermissions();

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Administrator');

        $this->actingAs($user)
            ->put(route('dashboard.settings.notifications.update'), [
                'disabled_notifications' => ['customer', 'login_failed'],
            ])
            ->assertSessionHas('status');

        $this->assertSame(['customer', 'login_failed'], $user->refresh()->notification_preferences);

        // Invalid type keys are rejected.
        $this->actingAs($user)
            ->put(route('dashboard.settings.notifications.update'), [
                'disabled_notifications' => ['not-a-real-type'],
            ])
            ->assertSessionHasErrors('disabled_notifications.0');
    }

    public function test_settings_page_renders_preference_toggles(): void
    {
        $this->seedPermissions();

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Administrator');

        $this->actingAs($user)
            ->get(route('dashboard.settings.index'))
            ->assertOk()
            ->assertSee('disabled_notifications[]', false)
            ->assertSee('receive notification.customer', false);
    }

    public function test_existing_permission_is_available_for_admin_management(): void
    {
        $this->seedPermissions();

        $this->assertTrue(Permission::query()->where('name', 'receive notification.whatsapp')->exists());
    }
}
