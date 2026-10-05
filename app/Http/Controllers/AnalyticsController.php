<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\GeminiUsageLog;
use App\Models\LoginHistory;
use App\Models\OtpLog;
use App\Models\VisitorLog;
use Illuminate\Support\Facades\DB;
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

        // ===== Gemini AI usage (WhatsApp agent) =====
        $aiSince = now()->subDays(13)->startOfDay();
        $aiTotals = [
            'requests' => GeminiUsageLog::query()->where('created_at', '>=', $aiSince)->count(),
            'successful' => GeminiUsageLog::query()->where('created_at', '>=', $aiSince)->where('success', true)->count(),
            'failed' => GeminiUsageLog::query()->where('created_at', '>=', $aiSince)->where('success', false)->count(),
            'prompt_tokens' => (int) GeminiUsageLog::query()->where('created_at', '>=', $aiSince)->sum('prompt_tokens'),
            'output_tokens' => (int) GeminiUsageLog::query()->where('created_at', '>=', $aiSince)->sum('output_tokens'),
            'thoughts_tokens' => (int) GeminiUsageLog::query()->where('created_at', '>=', $aiSince)->sum('thoughts_tokens'),
            'total_tokens' => (int) GeminiUsageLog::query()->where('created_at', '>=', $aiSince)->sum('total_tokens'),
            'avg_latency' => (int) GeminiUsageLog::query()->where('created_at', '>=', $aiSince)->whereNotNull('latency_ms')->avg('latency_ms'),
            'unique_customers' => GeminiUsageLog::query()->where('created_at', '>=', $aiSince)->whereNotNull('phone')->distinct()->count('phone'),
        ];

        $aiDaily = GeminiUsageLog::query()
            ->where('created_at', '>=', $aiSince)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as requests'),
                DB::raw('SUM(total_tokens) as total_tokens'),
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->pluck('requests', 'date');

        $aiRecentLogs = GeminiUsageLog::query()
            ->where('created_at', '>=', $aiSince)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $aiTopCustomers = GeminiUsageLog::query()
            ->where('created_at', '>=', $aiSince)
            ->whereNotNull('phone')
            ->select('phone', DB::raw('MAX(push_name) as push_name'), DB::raw('COUNT(*) as requests'), DB::raw('SUM(total_tokens) as total_tokens'))
            ->groupBy('phone')
            ->orderByDesc('requests')
            ->limit(5)
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
            'aiTotals' => $aiTotals,
            'aiDaily' => $aiDaily,
            'aiRecentLogs' => $aiRecentLogs,
            'aiTopCustomers' => $aiTopCustomers,
        ]);
    }
}
