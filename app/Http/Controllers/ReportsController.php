<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Reporting\DashboardStatisticsService;
use Illuminate\View\View;

class ReportsController extends Controller
{
    public function index(DashboardStatisticsService $statisticsService): View
    {
        return view('reports.index', [
            'stats' => $statisticsService->overview(),
            'chartData' => $statisticsService->chartData(),
        ]);
    }
}
