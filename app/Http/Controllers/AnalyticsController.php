<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LoginHistory;
use App\Models\OtpLog;
use App\Models\VisitorLog;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $visitorStats = [
            'total_visits' => VisitorLog::query()->count(),
            'unique_ips' => VisitorLog::query()->distinct('ip_address')->count('ip_address'),
            'today_visits' => VisitorLog::query()->whereDate('visited_at', today())->count(),
        ];

        $dailyVisits = VisitorLog::query()
            ->selectRaw('DATE(visited_at) as date, COUNT(*) as total')
            ->where('visited_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $byCountry = VisitorLog::query()
            ->selectRaw("COALESCE(NULLIF(TRIM(country), ''), 'Unknown') as country, COUNT(*) as total")
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $byDevice = VisitorLog::query()
            ->selectRaw("COALESCE(NULLIF(TRIM(device_type), ''), 'unknown') as device_type, COUNT(*) as total")
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->get();

        $topIps = VisitorLog::query()
            ->selectRaw('ip_address, MAX(country) as country, COUNT(*) as total')
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $otpDaily = OtpLog::query()
            ->selectRaw('DATE(sent_at) as date, COUNT(*) as total')
            ->where('sent_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $otpTotals = [
            'sent' => OtpLog::query()->count(),
            'failed' => OtpLog::query()->where('status', '!=', 'sent')->where('status', '!=', 'delivered')->count(),
        ];

        $loginDaily = LoginHistory::query()
            ->selectRaw(
                'DATE(logged_in_at) as date, '
                .'SUM(CASE WHEN is_successful = 1 THEN 1 ELSE 0 END) as successful, '
                .'SUM(CASE WHEN is_successful = 1 THEN 0 ELSE 1 END) as failed'
            )
            ->where('logged_in_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $loginTotals = [
            'successful' => LoginHistory::query()->where('is_successful', true)->count(),
            'failed' => LoginHistory::query()->where('is_successful', false)->count(),
        ];

        $failedByIp = LoginHistory::query()
            ->selectRaw('ip_address, COUNT(*) as total')
            ->where('is_successful', false)
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $userLogins = LoginHistory::query()
            ->with('user')
            ->where('is_successful', true)
            ->whereNotNull('user_id')
            ->selectRaw('user_id, COUNT(*) as total, MAX(logged_in_at) as last_login')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        $auditLogs = AuditLog::query()
            ->with('user')
            ->latest()
            ->limit(60)
            ->get();

        return view('analytics.index', [
            'visitorStats' => $visitorStats,
            'dailyVisits' => $dailyVisits,
            'byCountry' => $byCountry,
            'byDevice' => $byDevice,
            'topIps' => $topIps,
            'otpDaily' => $otpDaily,
            'otpTotals' => $otpTotals,
            'loginDaily' => $loginDaily,
            'loginTotals' => $loginTotals,
            'failedByIp' => $failedByIp,
            'userLogins' => $userLogins,
            'auditLogs' => $auditLogs,
        ]);
    }
}
