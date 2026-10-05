<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

class OfferPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasAnyPermission(['view reports', 'create offers', 'manage crm']);
    }

    public function view(User $user, mixed $model = null): bool
    {
        if (! $model instanceof Offer || ! $user->is_active) {
            return false;
        }

        return $this->canViewAll($user) || $this->owns($user, $model);
    }

    public function create(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasAnyPermission(['create offers', 'manage crm']);
    }

    public function update(User $user, mixed $model = null): bool
    {
        if (! $model instanceof Offer || ! $user->is_active) {
            return false;
        }

        return $user->hasAnyPermission(['manage crm', 'view reports'])
            || ($user->hasPermissionTo('create offers') && $this->owns($user, $model));
    }

    public function delete(User $user, mixed $model = null): bool
    {
        return $user->is_active && $user->hasPermissionTo('manage crm');
    }

    private function canViewAll(User $user): bool
    {
        return $user->hasAnyPermission(['view reports', 'manage crm']);
    }

    private function owns(User $user, Offer $offer): bool
    {
        return $offer->sales_id === $user->id
            || $offer->lead()->where('assigned_sales_id', $user->id)->exists()
            || $offer->customer()->whereHas('leads', fn ($lead) => $lead->where('assigned_sales_id', $user->id))->exists();
    }
}
