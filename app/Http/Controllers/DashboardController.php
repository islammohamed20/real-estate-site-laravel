<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Models\InstallmentPlan;
use App\Services\Reporting\DashboardStatisticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(DashboardStatisticsService $statisticsService): View
    {
        $profile = CompanyProfile::query()->find(1);

        return view('dashboard.index', [
            'stats' => $statisticsService->overview(),
            'chartData' => $statisticsService->chartData(),
            'trashedPlansCount' => InstallmentPlan::query()->onlyTrashed()->count(),
            'trashRetentionDays' => (int) ($profile?->trash_retention_days ?? 30),
            'autoCleanupEnabled' => (bool) ($profile?->auto_purge_enabled ?? true),
            'lastAutoCleanup' => $this->lastAutomaticCleanup(),
        ]);
    }

    public function runCleanup(Request $request): RedirectResponse
    {
        $countBefore = InstallmentPlan::query()->onlyTrashed()->count();

        Artisan::call('crm:purge-trashed-plans', ['--days' => 0]);
        $output = trim((string) Artisan::output());

        if (preg_match('/Permanently deleted (\d+) installment plan\(s\)/', $output, $matches)) {
            $message = __('Trash cleanup finished. :count plan(s) permanently deleted.', ['count' => $matches[1]]);
        } elseif ($countBefore === 0) {
            $message = __('The trash is already empty.');
        } else {
            $message = __('Trash cleanup finished. Nothing to purge.');
        }

        return back()->with('status', $message);
    }

    /**
     * Find the most recent automatic cleanup run from the daily log files
     * (storage/logs/laravel-*.log). Returns null when no automatic run was recorded.
     *
     * @return array{time: Carbon|null, deleted_count: int|null, summary: string}|null
     */
    private function lastAutomaticCleanup(): ?array
    {
        $files = glob(storage_path('logs/laravel-*.log'));

        if ($files === false || $files === []) {
            return null;
        }

        // Daily log filenames are date-stamped (laravel-YYYY-MM-DD.log), so reverse sort = newest first.
        rsort($files);

        foreach ($files as $file) {
            $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            if ($lines === false) {
                continue;
            }

            for ($i = count($lines) - 1; $i >= 0; $i--) {
                $line = $lines[$i];

                if (! str_contains($line, 'Trash cleanup (automatic)')) {
                    continue;
                }

                $time = null;
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    try {
                        $time = Carbon::parse($matches[1]);
                    } catch (\Throwable) {
                        $time = null;
                    }
                }

                $deletedCount = null;
                if (preg_match('/permanently deleted (\d+) installment plan\(s\)/', $line, $countMatches)) {
                    $deletedCount = (int) $countMatches[1];
                }

                return [
                    'time' => $time,
                    'deleted_count' => $deletedCount,
                    'summary' => trim($line),
                ];
            }
        }

        return null;
    }
}
