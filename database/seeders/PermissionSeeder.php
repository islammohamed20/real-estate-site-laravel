<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage users',
            'manage teams',
            'manage projects',
            'manage units',
            'manage crm',
            'manage settings',
            'view reports',
            'create offers',
            'create reservations',
            'manage installment templates',
            'manage installment plans',

            // CRM granular permissions
            'view crm dashboard',
            'view all leads',
            'view team leads',
            'view own leads',
            'create leads',
            'edit all leads',
            'edit own leads',
            'assign leads',
            'delete leads',

            'view all customers',
            'view own customers',
            'create customers',
            'edit all customers',
            'edit own customers',
            'delete customers',

            'view all deals',
            'view team deals',
            'view own deals',
            'create deals',
            'edit all deals',
            'edit own deals',
            'delete deals',
            'move deal stage',

            'view all tasks',
            'view own tasks',
            'create tasks',
            'edit all tasks',
            'edit own tasks',
            'delete tasks',

            'view all follow-ups',
            'view own follow-ups',
            'create follow-ups',
            'edit all follow-ups',
            'edit own follow-ups',
            'delete follow-ups',

            'manage crm settings',

            // WhatsApp panel
            'view whatsapp',
            'view all whatsapp conversations',
            'reply whatsapp',
            'assign whatsapp',

            // Own-trash visibility (data entry users can see only what they deleted)
            'view own trash',

            // Own profile editing
            'edit own profile',

            // Notification delivery (who is allowed to receive each type)
            'receive notification.all',
            'receive notification.customer',
            'receive notification.offer',
            'receive notification.plan',
            'receive notification.whatsapp',
            'receive notification.security',
            'receive notification.followups',
            'receive notification.trash',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $administrator = Role::findOrCreate('Administrator', 'web');
        $manager = Role::findOrCreate('Sales Manager', 'web');
        $executive = Role::findOrCreate('Sales Executive', 'web');
        $viewer = Role::findOrCreate('Viewer', 'web');
        $marketing = Role::findOrCreate('Marketing Manager', 'web');
        $accountant = Role::findOrCreate('Accountant', 'web');
        $receptionist = Role::findOrCreate('Receptionist', 'web');
        $dataEntry = Role::findOrCreate('Data Entry', 'web');
        $owner = Role::findOrCreate('Owner', 'web');

        $administrator->syncPermissions(Permission::all());

        $manager->syncPermissions([
            'manage teams',
            'manage projects',
            'manage units',
            'manage crm',
            'view reports',
            'create offers',
            'create reservations',
            'manage installment templates',
            'manage installment plans',

            'view crm dashboard',
            'view all leads',
            'view team leads',
            'create leads',
            'edit all leads',
            'assign leads',
            'delete leads',

            'view all customers',
            'create customers',
            'edit all customers',
            'delete customers',

            'view all deals',
            'view team deals',
            'create deals',
            'edit all deals',
            'delete deals',
            'move deal stage',

            'view all tasks',
            'create tasks',
            'edit all tasks',
            'delete tasks',

            'view all follow-ups',
            'create follow-ups',
            'edit all follow-ups',
            'delete follow-ups',

            'manage crm settings',

            'receive notification.customer',
            'receive notification.offer',
            'receive notification.plan',
            'receive notification.whatsapp',
            'receive notification.security',
            'receive notification.followups',
            'receive notification.trash',
        ]);

        $executive->syncPermissions([
            'manage crm',
            'create offers',
            'create reservations',
            'manage installment plans',

            'view crm dashboard',
            'view own leads',
            'create leads',
            'edit own leads',

            'view own customers',
            'create customers',
            'edit own customers',

            'view own deals',
            'create deals',
            'edit own deals',
            'move deal stage',

            'view own tasks',
            'create tasks',
            'edit own tasks',

            'view own follow-ups',
            'create follow-ups',
            'edit own follow-ups',
        ]);

        $viewer->syncPermissions([
            'view reports',
            'view crm dashboard',
            'view all leads',
            'view all customers',
            'view all deals',
            'view all tasks',
            'view all follow-ups',
        ]);

        $marketing->syncPermissions([
            'manage projects',
            'manage units',
            'manage crm',
            'view reports',
            'manage installment templates',
            'manage installment plans',

            'view crm dashboard',
            'view all leads',
            'create leads',
            'edit all leads',
            'assign leads',

            'view all customers',
            'create customers',
            'edit all customers',

            'view all deals',
            'create deals',
            'edit all deals',
            'move deal stage',

            'view all tasks',
            'create tasks',
            'edit all tasks',

            'view all follow-ups',
            'create follow-ups',
            'edit all follow-ups',
        ]);

        $accountant->syncPermissions([
            'view reports',
            'view crm dashboard',
            'view all leads',
            'view all customers',
            'view all deals',
            'view all tasks',
            'view all follow-ups',
        ]);

        $receptionist->syncPermissions([
            'view crm dashboard',
            'create leads',
            'edit own leads',
            'create customers',
            'edit own customers',
            'create tasks',
            'view own tasks',
            'create follow-ups',
            'view own follow-ups',
        ]);

        $dataEntry->syncPermissions([
            'manage projects',
            'manage units',
            'view own trash',
            'edit own profile',

            'receive notification.trash',
        ]);

        $owner->syncPermissions(Permission::all());
    }
}
