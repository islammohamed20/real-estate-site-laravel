<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::findOrCreate('receive notification.followups', 'web');
        $role = Role::query()->where('name', 'Sales Executive')->where('guard_name', 'web')->first();

        $role?->givePermissionTo($permission);
    }

    public function down(): void
    {
        $permission = Permission::query()
            ->where('name', 'receive notification.followups')
            ->where('guard_name', 'web')
            ->first();
        $role = Role::query()->where('name', 'Sales Executive')->where('guard_name', 'web')->first();

        if ($permission && $role) {
            $role->revokePermissionTo($permission);
        }
    }
};
