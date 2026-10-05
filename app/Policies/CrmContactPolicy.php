<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Crm\CrmContact;
use App\Models\User;

class CrmContactPolicy extends BasePolicy
{
    public function view(User $user, mixed $model = null): bool
    {
        if (! $model instanceof CrmContact || ! $user->is_active) {
            return false;
        }

        if ($user->hasAnyPermission(['view reports', 'manage crm'])) {
            return true;
        }

        return $model->deals()
            ->where(fn ($q) => $q
                ->where('assigned_to', $user->id)
                ->orWhere('created_by', $user->id))
            ->exists();
    }

    public function update(User $user, mixed $model = null): bool
    {
        return $this->view($user, $model);
    }

    public function delete(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasPermissionTo('manage crm');
    }
}
