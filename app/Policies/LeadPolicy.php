<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->hasAnyPermission([
            'view all leads',
            'view team leads',
            'view own leads',
            'manage crm',
        ]);
    }

    public function view(User $user, mixed $model = null): bool
    {
        if (! $model instanceof Lead) {
            return false;
        }

        if (! $user->is_active) {
            return false;
        }

        if ($user->hasAnyPermission(['view all leads', 'manage crm'])) {
            return true;
        }

        if ($user->hasPermissionTo('view team leads')) {
            return $model->assigned_sales_id === $user->id;
        }

        return $user->hasPermissionTo('view own leads')
            && $model->assigned_sales_id === $user->id;
    }

    public function create(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasAnyPermission(['create leads', 'manage crm']);
    }

    public function update(User $user, mixed $model = null): bool
    {
        if (! $model instanceof Lead) {
            return false;
        }

        if (! $user->is_active) {
            return false;
        }

        if ($user->hasAnyPermission(['edit all leads', 'manage crm'])) {
            return true;
        }

        return $user->hasPermissionTo('edit own leads')
            && $model->assigned_sales_id === $user->id;
    }

    public function delete(User $user, mixed $model = null): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->hasAnyPermission(['delete leads', 'manage crm']);
    }

    public function assign(User $user): bool
    {
        return $user->is_active && $user->hasAnyPermission(['assign leads', 'manage crm']);
    }
}
