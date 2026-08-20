<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view whatsapp',
            'view all whatsapp conversations',
            'reply whatsapp',
            'assign whatsapp',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $grant = static function (string $role, array $perms): void {
            $r = Role::findOrCreate($role, 'web');
            $r->givePermissionTo($perms);
        };

        $grant('Administrator', $permissions);
        $grant('Owner', $permissions);
        $grant('Sales Manager', $permissions);
        $grant('Sales Executive', ['view whatsapp', 'reply whatsapp']);
        $grant('Marketing Manager', ['view whatsapp', 'reply whatsapp']);
        $grant('Receptionist', ['view whatsapp', 'reply whatsapp']);
        $grant('Viewer', ['view whatsapp']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['view whatsapp', 'view all whatsapp conversations', 'reply whatsapp', 'assign whatsapp'] as $permission) {
            Permission::query()->where('name', $permission)->where('guard_name', 'web')->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
