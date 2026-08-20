<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppConversation;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\InstallmentPlan;
use App\Models\Unit;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LiveStatusController extends Controller
{
    /**
     * Lightweight poll endpoint — returns ONLY counts & latest timestamps.
     * Designed to be called every 15–60s with minimal DB load.
     * Single query per section, indexed columns only.
     */
    public function poll(): JsonResponse
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first();

        $data = [];

        // ── 1. Notifications (every 60s) ──────────────────────────────
        $data['notifications'] = [
            'unread' => DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->whereNull('read_at')
                ->count(),
            'latest' => DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->latest('created_at')
                ->value('created_at'),
        ];

        // ── 2. WhatsApp (every 30s) ───────────────────────────────────
        if ($this->canView($role, ['Administrator', 'Owner', 'Sales Manager'])) {
            $data['whatsapp'] = [
                'unread' => WhatsAppConversation::whereNull('assigned_to')
                    ->where('status', 'active')
                    ->where('last_message_at', '>=', now()->subHour())
                    ->count(),
                'total_active' => WhatsAppConversation::where('status', 'active')->count(),
                'latest' => WhatsAppConversation::max('last_message_at'),
            ];
        }

        // ── 3. CRM Leads (every 120s) ─────────────────────────────────
        if ($this->canView($role, ['Administrator', 'Owner', 'Sales Manager', 'Sales Executive'])) {
            $data['leads'] = [
                'new_today' => Lead::whereDate('created_at', today())->count(),
                'total_active' => Lead::whereNotIn('status', ['converted', 'lost'])->count(),
                'unassigned' => Lead::whereNull('assigned_sales_id')->count(),
                'latest' => Lead::max('updated_at'),
            ];
        }

        // ── 4. Dashboard summary (every 180s) ─────────────────────────
        $data['summary'] = [
            'customers' => Customer::count(),
            'units' => Unit::count(),
            'active_plans' => InstallmentPlan::whereNull('deleted_at')->count(),
            'projects' => Project::count(),
        ];

        return response()->json([
            'ok' => true,
            'ts' => now()->timestamp,
            'data' => $data,
        ], 200, ['Cache-Control' => 'no-cache, no-store']);
    }

    /**
     * Minimal health check — returns server time only.
     */
    public function ping(): JsonResponse
    {
        return response()->json(['ts' => now()->timestamp]);
    }

    private function canView(string $role, array $allowed): bool
    {
        return in_array($role, $allowed, true);
    }
}
