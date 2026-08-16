<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

abstract class BasePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Administrator')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, mixed $model = null): bool
    {
        return $user->is_active;
    }

    public function create(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasAnyRole(['Administrator', 'Sales Manager', 'Sales Executive']);
    }

    public function update(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasAnyRole(['Administrator', 'Sales Manager']);
    }

    public function delete(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasAnyRole(['Administrator', 'Sales Manager']);
    }
}
