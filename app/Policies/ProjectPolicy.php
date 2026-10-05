<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class ProjectPolicy extends BasePolicy
{
    public function update(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasAnyRole(['Administrator', 'Sales Executive', 'Data Entry', 'Owner', 'Accountant']);
    }
}
