<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\Reporting\CrmReportingService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(CrmReportingService $reporting): View
    {
        $this->authorize('viewAny', Lead::class);

        return view('crm.reports.index', [
            'overview' => $reporting->overview(),
            'leadsByStage' => $reporting->leadsByStage(),
            'funnel' => $reporting->conversionFunnel(),
            'topPerformers' => $reporting->topPerformers(),
            'dailyActivity' => $reporting->dailyActivity(),
        ]);
    }
}
