<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\SalesTeam;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Services\Reporting\SalesPerformanceService;
use App\Support\SalesTeamAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Combined sales performance leaderboard: WhatsApp activity + CRM results,
 * aggregated per salesperson and per team.
 */
class SalesPerformanceController extends Controller
{
    public function __construct(private readonly SalesPerformanceService $performance)
    {
    }

    public function index(Request $request): View
    {
        $days = max(1, min(365, (int) $request->integer('days', 30)));
        $since = CarbonImmutable::now()->subDays($days);
        $teamId = $request->integer('team_id');

        $teams = SalesTeam::query()
            ->with(['manager', 'members:id,name'])
            ->when(! SalesTeamAccess::isGlobal(), fn ($query) => $query->where('manager_id', auth()->id()))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $repIds = $this->repUserIds($teams, $teamId);
        $repMetrics = $repIds->isEmpty()
            ? collect()
            : $this->performance->metricsForUsers($repIds, $since);

        // Sort by composite score, best first.
        $repMetrics = $repMetrics->sortByDesc('score')->values();

        // Aggregate per team.
        $teamMetrics = $teams->map(function (SalesTeam $team) use ($repMetrics, $teamId) {
            if ($teamId !== 0 && $team->id !== $teamId) {
                return null;
            }
            $memberIds = $team->members->pluck('id')->all();
            $rows = $repMetrics->whereIn('user_id', $memberIds);

            return [
                'team' => $team,
                ...$this->performance->sumMetrics($rows),
            ];
        })->filter();

        $totals = $this->performance->sumMetrics($repMetrics);

        return view('dashboard.sales-performance.index', [
            'teams' => $teams,
            'teamId' => $teamId,
            'days' => $days,
            'repMetrics' => $repMetrics,
            'teamMetrics' => $teamMetrics->sortByDesc('deal_value')->values(),
            'totals' => $totals,
            'maxScore' => max(1, (int) $repMetrics->max('score')),
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, int>
     */
    private function repUserIds($teams, int $teamId)
    {
        $activeTeamMembers = $teams->flatMap->members->pluck('id')->unique();

        if ($teamId !== 0) {
            return $teams->firstWhere('id', $teamId)?->members->pluck('id') ?? collect();
        }

        // Isolation: a Sales Manager only sees their own team members (+ self).
        if (! SalesTeamAccess::isGlobal()) {
            return $activeTeamMembers->push(auth()->id())->unique()->filter()->values();
        }

        $salesUsers = User::query()
            ->role(['Sales Executive', 'Sales Manager'])
            ->pluck('id');

        $conversationUsers = WhatsAppConversation::query()
            ->where('created_at', '>=', CarbonImmutable::now()->subDays(365))
            ->whereNotNull('assigned_to')
            ->distinct()
            ->pluck('assigned_to');

        $leadUsers = Lead::query()
            ->where('created_at', '>=', CarbonImmutable::now()->subDays(365))
            ->whereNotNull('assigned_sales_id')
            ->distinct()
            ->pluck('assigned_sales_id');

        return $salesUsers
            ->merge($activeTeamMembers)
            ->merge($conversationUsers)
            ->merge($leadUsers)
            ->unique()
            ->filter()
            ->values();
    }
}
