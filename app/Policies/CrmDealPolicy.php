<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Crm\CrmDeal;
use App\Models\User;

class CrmDealPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->hasAnyPermission([
            'view all deals',
            'view team deals',
            'view own deals',
            'manage crm',
        ]);
    }

    public function view(User $user, mixed $model = null): bool
    {
        if (! $model instanceof CrmDeal) {
            return false;
        }

        if (! $user->is_active) {
            return false;
        }

        if ($user->hasAnyPermission(['view all deals', 'manage crm'])) {
            return true;
        }

        if ($user->hasPermissionTo('view team deals')) {
            return $model->assigned_to === $user->id || $model->created_by === $user->id;
        }

        return $user->hasPermissionTo('view own deals')
            && ($model->assigned_to === $user->id || $model->created_by === $user->id);
    }

    public function create(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasAnyPermission(['create deals', 'manage crm']);
    }

    public function update(User $user, mixed $model = null): bool
    {
        if (! $model instanceof CrmDeal) {
            return false;
        }

        if (! $user->is_active) {
            return false;
        }

        if ($user->hasAnyPermission(['edit all deals', 'manage crm'])) {
            return true;
        }

        return $user->hasPermissionTo('edit own deals')
            && ($model->assigned_to === $user->id || $model->created_by === $user->id);
    }

    public function delete(User $user, mixed $model = null): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->hasAnyPermission(['delete deals', 'manage crm']);
    }

    public function moveStage(User $user, mixed $model = null): bool
    {
        if (! $model instanceof CrmDeal) {
            return false;
        }

        if (! $user->is_active) {
            return false;
        }

        if ($user->hasAnyPermission(['move deal stage', 'edit all deals', 'manage crm'])) {
            return true;
        }

        return $user->hasPermissionTo('edit own deals')
            && ($model->assigned_to === $user->id || $model->created_by === $user->id);
    }
}
