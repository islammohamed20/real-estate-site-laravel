<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasAnyPermission(['view reports', 'create reservations', 'manage crm']);
    }

    public function view(User $user, mixed $model = null): bool
    {
        if (! $model instanceof Reservation || ! $user->is_active) {
            return false;
        }

        return $this->canViewAll($user) || $this->owns($user, $model);
    }

    public function create(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasAnyPermission(['create reservations', 'manage crm']);
    }

    public function update(User $user, mixed $model = null): bool
    {
        if (! $model instanceof Reservation || ! $user->is_active) {
            return false;
        }

        return $user->hasAnyPermission(['manage crm', 'view reports'])
            || ($user->hasPermissionTo('create reservations') && $this->owns($user, $model));
    }

    public function delete(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasPermissionTo('manage crm');
    }

    private function canViewAll(User $user): bool
    {
        return $user->hasAnyPermission(['view reports', 'manage crm']);
    }

    private function owns(User $user, Reservation $reservation): bool
    {
        return $reservation->sales_id === $user->id
            || $reservation->lead()->where('assigned_sales_id', $user->id)->exists()
            || $reservation->customer()->whereHas('leads', fn ($lead) => $lead->where('assigned_sales_id', $user->id))->exists();
    }
}
