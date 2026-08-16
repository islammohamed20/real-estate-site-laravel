<?php

declare(strict_types=1);

namespace App\Services\Reporting;

use App\Enums\LeadStage;
use App\Models\Crm\CrmActivity;
use App\Models\Crm\CrmDeal;
use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Reservation;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class CrmReportingService
{
    public function overview(): array
    {
        return [
            'total_leads' => Lead::query()->count(),
            'new_leads_today' => Lead::query()->whereDate('created_at', today())->count(),
            'total_customers' => Customer::query()->count(),
            'new_customers_today' => Customer::query()->whereDate('created_at', today())->count(),
            'active_deals' => CrmDeal::query()->where('status', 'open')->count(),
            'total_deals_value' => (float) CrmDeal::query()->where('status', 'open')->sum('value'),
            'open_offers' => Offer::query()->whereIn('status', ['draft', 'sent'])->count(),
            'offers_value' => (float) Offer::query()->whereIn('status', ['draft', 'sent', 'accepted'])->sum('total_amount'),
            'active_reservations' => Reservation::query()->whereIn('status', ['pending', 'paid'])->count(),
            'overdue_tasks' => Task::query()->whereNull('completed_at')->where('due_at', '<', now())->count(),
            'overdue_follow_ups' => FollowUp::query()->pending()->overdue()->count(),
            'activities_today' => CrmActivity::query()->whereDate('created_at', today())->count(),
        ];
    }

    public function leadsByStage(): Collection
    {
        return collect(LeadStage::cases())->map(fn ($stage) => [
            'stage' => $stage->label(),
            'count' => Lead::query()->where('stage', $stage->value)->count(),
            'value' => (float) Lead::query()->where('stage', $stage->value)->sum('budget'),
        ]);
    }

    public function conversionFunnel(): array
    {
        $leads = Lead::query()->count();
        $offers = Offer::query()->count();
        $reservations = Reservation::query()->count();

        return [
            ['label' => __('Leads'), 'count' => $leads, 'rate' => 100],
            ['label' => __('Offers'), 'count' => $offers, 'rate' => $leads > 0 ? round($offers / $leads * 100, 1) : 0],
            ['label' => __('Reservations'), 'count' => $reservations, 'rate' => $leads > 0 ? round($reservations / $leads * 100, 1) : 0],
        ];
    }

    public function topPerformers(int $limit = 5): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->withCount([
                'offers' => fn ($q) => $q->whereDate('created_at', '>=', now()->subDays(30)),
                'reservations' => fn ($q) => $q->whereDate('created_at', '>=', now()->subDays(30)),
            ])
            ->orderByDesc('offers_count')
            ->limit($limit)
            ->get();
    }

    public function dailyActivity(int $days = 14): Collection
    {
        $start = now()->subDays($days)->startOfDay();
        $end = now()->endOfDay();

        return CrmActivity::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
