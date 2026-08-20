<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\SalesTeam;
use App\Models\User;

/**
 * Team-level access rules for the sales modules.
 *
 * - Administrator / Owner: full visibility across every team.
 * - Sales Manager: complete isolation — only the team(s) they manage.
 */
class SalesTeamAccess
{
    /**
     * Whether the current user manages the whole sales organization.
     */
    public static function isGlobal(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->hasRole(['Administrator', 'Owner']);
    }

    /**
     * IDs of the teams the current user manages (empty for global users,
     * who are not restricted).
     *
     * @return list<int>
     */
    public static function managedTeamIds(): array
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return [];
        }

        return $user->managedTeams()->pluck('id')->all();
    }

    /**
     * Whether the current user may manage the given team.
     */
    public static function canManageTeam(SalesTeam $team): bool
    {
        return self::isGlobal() || $team->manager_id === auth()->id();
    }

    /**
     * Active users the current user may manage in team scopes:
     * global users → everyone; managers → their own team members + themselves.
     */
    public static function manageableUserIds(): array
    {
        if (self::isGlobal()) {
            return User::query()->active()->pluck('id')->all();
        }

        return self::managedTeamIds() !== []
            ? \App\Models\SalesTeam::query()
                ->whereIn('id', self::managedTeamIds())
                ->with('members:id')
                ->get()
                ->flatMap(fn ($team) => $team->members->pluck('id'))
                ->push(auth()->id())
                ->unique()
                ->values()
                ->all()
            : [auth()->id()];
    }
}
