<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesTarget;
use App\Models\SalesTeam;
use App\Models\User;
use App\Services\Reporting\SalesPerformanceService;
use App\Support\SalesTeamAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SalesTargetController extends Controller
{
    public function __construct(private readonly SalesPerformanceService $performance)
    {
    }

    public function index(Request $request): View
    {
        $period = $this->normalizePeriod($request->input('period', now()->format('Y-m')));
        $since = CarbonImmutable::createFromFormat('Y-m-d', $period.'-01')->startOfMonth();
        $until = $since->addMonth();

        $targets = SalesTarget::query()
            ->with(['user', 'team.members'])
            ->where('period', $period)
            ->when(! SalesTeamAccess::isGlobal(), function ($query) {
                $query->where(function ($inner) {
                    $inner->whereIn('sales_team_id', SalesTeamAccess::managedTeamIds())
                        ->orWhere('user_id', auth()->id());
                });
            })
            ->orderBy('sales_team_id')
            ->orderBy('user_id')
            ->get();

        // Actuals per target entity within the month.
        $targets->each(function (SalesTarget $target) use ($since, $until): void {
            $actuals = $target->user_id
                ? $this->performance->metricsForUsers(collect([$target->user_id]), $since)->first()
                : $this->performance->metricsForTeam($target->team, $since);

            $target->actuals = $actuals ?? $this->performance->sumMetrics(collect());
        });

        return view('dashboard.sales-targets.index', [
            'targets' => $targets,
            'period' => $period,
            'prevPeriod' => CarbonImmutable::createFromFormat('Y-m', $period)->subMonth()->format('Y-m'),
            'nextPeriod' => CarbonImmutable::createFromFormat('Y-m', $period)->addMonth()->format('Y-m'),
            'isCurrentMonth' => $period === now()->format('Y-m'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('dashboard.sales-targets.form', [
            'target' => null,
            'period' => $this->normalizePeriod($request->input('period', now()->format('Y-m'))),
            'teams' => $this->managedTeams(),
            'users' => $this->candidateUsers(),
            'isGlobal' => SalesTeamAccess::isGlobal(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTarget($request);

        $exists = SalesTarget::query()
            ->where('period', $validated['period'])
            ->where('user_id', $validated['user_id'] ?? null)
            ->where('sales_team_id', $validated['sales_team_id'] ?? null)
            ->exists();

        if ($exists) {
            return back()->withErrors(['target' => __('A target already exists for this entity and period.')]);
        }

        SalesTarget::query()->create($validated);

        return redirect()->route('dashboard.sales-targets.index', ['period' => $validated['period']])
            ->with('status', __('Sales target created successfully.'));
    }

    public function edit(SalesTarget $target): View
    {
        return view('dashboard.sales-targets.form', [
            'target' => $target->load(['user', 'team']),
            'period' => $target->period,
            'teams' => $this->managedTeams(),
            'users' => $this->candidateUsers(),
            'isGlobal' => SalesTeamAccess::isGlobal(),
        ]);
    }

    public function update(Request $request, SalesTarget $target): RedirectResponse
    {
        abort_unless($this->canManageTarget($target), 403);

        $validated = $this->validateTarget($request);

        $conflict = SalesTarget::query()
            ->where('id', '!=', $target->id)
            ->where('period', $validated['period'])
            ->where('user_id', $validated['user_id'] ?? null)
            ->where('sales_team_id', $validated['sales_team_id'] ?? null)
            ->exists();

        if ($conflict) {
            return back()->withErrors(['target' => __('A target already exists for this entity and period.')]);
        }

        $target->update($validated);

        return redirect()->route('dashboard.sales-targets.index', ['period' => $validated['period']])
            ->with('status', __('Sales target updated successfully.'));
    }

    public function destroy(SalesTarget $target): RedirectResponse
    {
        abort_unless($this->canManageTarget($target), 403);

        $target->delete();

        return redirect()->route('dashboard.sales-targets.index', ['period' => $target->period])
            ->with('status', __('Sales target deleted.'));
    }

    /**
     * @return array{user_id: list<string>, sales_team_id: list<string>, period: list<string>, leads_target: list<string>, offers_target: list<string>, reservations_target: list<string>, deals_target: list<string>, deal_value_target: list<string>}
     */
    private function validateTarget(Request $request): array
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'sales_team_id' => ['nullable', 'integer', 'exists:sales_teams,id'],
            'period' => ['required', 'date_format:Y-m'],
            'leads_target' => ['nullable', 'integer', 'min:0'],
            'offers_target' => ['nullable', 'integer', 'min:0'],
            'reservations_target' => ['nullable', 'integer', 'min:0'],
            'deals_target' => ['nullable', 'integer', 'min:0'],
            'deal_value_target' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Exactly one entity must be selected (user XOR team).
        if ((int) ($validated['user_id'] ?? 0) === 0 && (int) ($validated['sales_team_id'] ?? 0) === 0) {
            abort(422, __('Select a salesperson or a team.'));
        }

        return [
            'user_id' => $validated['user_id'] ?? null,
            'sales_team_id' => $validated['sales_team_id'] ?? null,
            'period' => $validated['period'],
            'leads_target' => $validated['leads_target'] ?? 0,
            'offers_target' => $validated['offers_target'] ?? 0,
            'reservations_target' => $validated['reservations_target'] ?? 0,
            'deals_target' => $validated['deals_target'] ?? 0,
            'deal_value_target' => $validated['deal_value_target'] ?? 0,
        ];
    }

    private function normalizePeriod(string $period): string
    {
        $parsed = CarbonImmutable::createFromFormat('Y-m', $period);

        return $parsed ? $parsed->format('Y-m') : now()->format('Y-m');
    }

    /**
     * Teams the current user may target: all for global users, own teams only
     * for Sales Managers (complete isolation).
     */
    private function managedTeams(): Collection
    {
        $query = SalesTeam::query()->orderBy('name');

        if (! SalesTeamAccess::isGlobal()) {
            $query->whereIn('id', SalesTeamAccess::managedTeamIds());
        }

        return $query->get();
    }

    /**
     * Whether the current user may manage a target (own team / own record).
     */
    private function canManageTarget(SalesTarget $target): bool
    {
        if (SalesTeamAccess::isGlobal()) {
            return true;
        }

        if ($target->user_id === auth()->id()) {
            return true;
        }

        return $target->sales_team_id !== null
            && in_array($target->sales_team_id, SalesTeamAccess::managedTeamIds(), true);
    }

    /**
     * Active users that can receive individual targets (sales roles first).
     * Isolation: managers can only target their own team members + themselves.
     */
    private function candidateUsers(): Collection
    {
        $query = User::query()->active()->with('roles')->orderBy('name');

        if (! SalesTeamAccess::isGlobal()) {
            $query->whereIn('id', SalesTeamAccess::manageableUserIds());
        }

        return $query
            ->get(['id', 'name', 'job_title'])
            ->sortBy(fn (User $user) => $user->hasRole('Sales Manager') ? 0 : ($user->hasRole('Sales Executive') ? 1 : 2))
            ->values();
    }
}
