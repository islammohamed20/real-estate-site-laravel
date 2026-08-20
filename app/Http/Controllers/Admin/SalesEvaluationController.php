<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesEvaluation;
use App\Support\SalesTeamAccess;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesEvaluationController extends Controller
{
    public function index(Request $request): View
    {
        $period = $request->input('period', now()->format('Y-m'));
        $parsed = CarbonImmutable::createFromFormat('Y-m', $period);
        $period = $parsed ? $parsed->format('Y-m') : now()->format('Y-m');

        $evaluations = SalesEvaluation::query()
            ->with('user:id,name,job_title')
            ->where('period', $period)
            ->when(! SalesTeamAccess::isGlobal(), fn ($query) => $query->whereIn('user_id', SalesTeamAccess::manageableUserIds()))
            ->orderByDesc('score')
            ->get();

        $counts = [
            'A' => $evaluations->where('grade', 'A')->count(),
            'B' => $evaluations->where('grade', 'B')->count(),
            'C' => $evaluations->where('grade', 'C')->count(),
            'D' => $evaluations->where('grade', 'D')->count(),
        ];

        return view('dashboard.sales-evaluations.index', [
            'evaluations' => $evaluations,
            'period' => $period,
            'prevPeriod' => $parsed?->subMonth()->format('Y-m'),
            'nextPeriod' => $parsed?->addMonth()->format('Y-m'),
            'counts' => $counts,
            'isCurrentMonth' => $period === now()->format('Y-m'),
        ]);
    }
}
