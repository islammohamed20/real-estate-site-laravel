<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\SalesTeam;
use App\Models\User;
use App\Support\SalesTeamAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesTeamController extends Controller
{
    public function index(): View
    {
        $teams = SalesTeam::query()
            ->with(['manager', 'members'])
            ->when(! SalesTeamAccess::isGlobal(), fn ($query) => $query->where('manager_id', auth()->id()))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15);

        $this->attachLeadCounts($teams);

        return view('dashboard.sales-teams.index', [
            'teams' => $teams,
        ]);
    }

    public function create(): View
    {
        return view('dashboard.sales-teams.form', [
            'team' => null,
            'users' => $this->candidateUsers(),
            'isGlobal' => SalesTeamAccess::isGlobal(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTeam($request);

        // Isolation: a Sales Manager can only create a team they manage.
        $managerId = SalesTeamAccess::isGlobal() ? ($validated['manager_id'] ?? null) : auth()->id();

        $team = SalesTeam::query()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'manager_id' => $managerId,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $team->members()->sync($validated['members'] ?? []);

        return redirect()->route('dashboard.sales-teams.index')
            ->with('status', __('Sales team created successfully.'));
    }

    public function edit(SalesTeam $team): View
    {
        abort_unless(SalesTeamAccess::canManageTeam($team), 403);

        return view('dashboard.sales-teams.form', [
            'team' => $team->load(['manager', 'members']),
            'users' => $this->candidateUsers(),
            'isGlobal' => SalesTeamAccess::isGlobal(),
        ]);
    }

    public function update(Request $request, SalesTeam $team): RedirectResponse
    {
        abort_unless(SalesTeamAccess::canManageTeam($team), 403);

        $validated = $this->validateTeam($request);

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            // Managers keep themselves as the team manager (isolation).
            'manager_id' => SalesTeamAccess::isGlobal() ? ($validated['manager_id'] ?? null) : auth()->id(),
            'is_active' => $validated['is_active'] ?? false,
        ]);

        $team->members()->sync($validated['members'] ?? []);

        return redirect()->route('dashboard.sales-teams.index')
            ->with('status', __('Sales team updated successfully.'));
    }

    public function toggle(SalesTeam $team): RedirectResponse
    {
        abort_unless(SalesTeamAccess::canManageTeam($team), 403);

        $team->update([
            'is_active' => ! $team->is_active,
        ]);

        return back()->with('status', $team->is_active
            ? __('Sales team activated.')
            : __('Sales team deactivated.'));
    }

    public function destroy(SalesTeam $team): RedirectResponse
    {
        abort_unless(SalesTeamAccess::canManageTeam($team), 403);

        $team->delete();

        return redirect()->route('dashboard.sales-teams.index')
            ->with('status', __('Sales team deleted.'));
    }

    /**
     * @return array{name: list<string>, description: list<string>, manager_id: list<string>, is_active: list<string>, members: list<string>, members.*: list<string>}
     */
    private function validateTeam(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'is_active' => ['boolean'],
            'members' => ['nullable', 'array'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);
    }

    /**
     * Active users that can be a team member (sales roles first).
     * Isolation: a Sales Manager can only pick their own members or
     * executives that are not assigned to any other team yet.
     */
    private function candidateUsers()
    {
        $query = User::query()->active()->with('roles')->orderBy('name');

        if (! SalesTeamAccess::isGlobal()) {
            $managedIds = SalesTeamAccess::managedTeamIds();
            $ownMemberIds = $managedIds !== []
                ? SalesTeam::query()->whereIn('id', $managedIds)->with('members:id')->get()
                    ->flatMap->members->pluck('id')
                : collect();

            $freeIds = User::query()
                ->active()
                ->whereDoesntHave('salesTeams')
                ->pluck('id');

            $query->whereIn('id', $ownMemberIds->merge($freeIds)->push(auth()->id())->unique());
        }

        return $query
            ->get(['id', 'name', 'job_title'])
            ->sortBy(fn (User $user) => $this->userPriority($user))
            ->values();
    }

    private function userPriority(User $user): int
    {
        if ($user->hasRole('Sales Manager')) {
            return 0;
        }
        if ($user->hasRole('Sales Executive')) {
            return 1;
        }

        return 2;
    }

    /**
     * Attach a single-query `active_leads_count` attribute to every team
     * (leads currently assigned to any of its members).
     */
    private function attachLeadCounts($teams): void
    {
        $memberIds = $teams->getCollection()
            ->flatMap(fn (SalesTeam $team) => $team->members->pluck('id'))
            ->unique()
            ->values();

        $counts = [];
        if ($memberIds->isNotEmpty()) {
            $counts = Lead::query()
                ->whereIn('assigned_sales_id', $memberIds)
                ->selectRaw('assigned_sales_id, COUNT(*) as total')
                ->groupBy('assigned_sales_id')
                ->pluck('total', 'assigned_sales_id')
                ->all();
        }

        $teams->getCollection()->each(function (SalesTeam $team) use ($counts): void {
            $team->active_leads_count = collect($team->members)
                ->sum(fn (User $member) => (int) ($counts[$member->id] ?? 0));
        });
    }
}
