<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->hasAnyPermission([
            'view all customers',
            'view own customers',
            'manage crm',
        ]);
    }

    public function view(User $user, mixed $model = null): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->hasAnyPermission(['view all customers', 'manage crm'])) {
            return true;
        }

        return $user->hasPermissionTo('view own customers');
    }

    public function create(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasAnyPermission(['create customers', 'manage crm']);
    }

    public function update(User $user, mixed $model = null): bool
    {
        if (! $model instanceof Customer) {
            return false;
        }

        if (! $user->is_active) {
            return false;
        }

        if ($user->hasAnyPermission(['edit all customers', 'manage crm'])) {
            return true;
        }

        return $user->hasPermissionTo('edit own customers');
    }

    public function delete(User $user, mixed $model = null): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->hasAnyPermission(['delete customers', 'manage crm']);
    }
}
