<?php

declare(strict_types=1);

namespace App\Services\Reporting;

use App\Enums\UnitStatus;
use App\Models\Crm\CrmActivity;
use App\Models\Crm\CrmDeal;
use App\Models\Crm\CrmStage;
use App\Models\Customer;
use App\Models\InstallmentPlan;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\VisitorLog;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Carbon;

class DashboardStatisticsService
{
    public function overview(): array
    {
        $totalUnits = Unit::query()->count();
        $availableUnits = Unit::query()->where('status', UnitStatus::Available->value)->count();
        $reservedUnits = Unit::query()->where('status', UnitStatus::Reserved->value)->count();
        $soldUnits = Unit::query()->where('status', UnitStatus::Sold->value)->count();

        $totalPortfolioValue = (float) Unit::query()->sum('current_price');
        $availablePortfolioValue = (float) Unit::query()->where('status', UnitStatus::Available->value)->sum('current_price');
        $soldPortfolioValue = (float) Unit::query()->where('status', UnitStatus::Sold->value)->sum('current_price');

        $openDealsCount = CrmDeal::query()->where('status', 'open')->count();
        $openDealsValue = (float) CrmDeal::query()->where('status', 'open')->sum('value');
        $wonDealsValue = (float) CrmDeal::query()->where('status', 'won')->sum('value');

        $todayLeads = Lead::query()->whereDate('created_at', Carbon::today())->count();
        $weekLeads = Lead::query()->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay())->count();
        $totalLeads = Lead::query()->count();

        $totalCustomers = Customer::query()->count();
        $todayOffers = Offer::query()->whereDate('created_at', Carbon::today())->count();
        $totalOffers = Offer::query()->count();
        $totalOffersValue = (float) Offer::query()->sum('total_amount');

        $activeReservations = Reservation::query()->where('status', 'active')->count();
        $totalDeposits = (float) Reservation::query()->where('status', 'active')->sum('deposit_amount');

        $whatsappConversations = WhatsAppConversation::query()->count();
        $whatsappUnread = (int) WhatsAppConversation::query()->sum('unread_count');

        $todayVisits = VisitorLog::query()->whereDate('visited_at', Carbon::today())->count();
        $totalVisits = VisitorLog::query()->count();

        return [
            'projects' => Project::query()->count(),
            'active_projects' => Project::query()->where('status', 'active')->count(),
            'units' => $totalUnits,
            'available_units' => $availableUnits,
            'reserved_units' => $reservedUnits,
            'sold_units' => $soldUnits,
            'total_portfolio_value' => $totalPortfolioValue,
            'available_portfolio_value' => $availablePortfolioValue,
            'sold_portfolio_value' => $soldPortfolioValue,
            'total_leads' => $totalLeads,
            'today_leads' => $todayLeads,
            'week_leads' => $weekLeads,
            'total_customers' => $totalCustomers,
            'open_deals_count' => $openDealsCount,
            'open_deals_value' => $openDealsValue,
            'won_deals_value' => $wonDealsValue,
            'total_offers' => $totalOffers,
            'today_offers' => $todayOffers,
            'total_offers_value' => $totalOffersValue,
            'active_reservations' => $activeReservations,
            'total_deposits' => $totalDeposits,
            'whatsapp_conversations' => $whatsappConversations,
            'whatsapp_unread' => $whatsappUnread,
            'today_visits' => $todayVisits,
            'total_visits' => $totalVisits,
            'active_plans_count' => InstallmentPlan::query()->where('status', 'active')->count(),
            'active_plans_value' => (float) InstallmentPlan::query()->where('status', 'active')->sum('final_price'),
        ];
    }

    public function chartData(): array
    {
        $days = collect(range(6, 0))->map(function (int $daysAgo): array {
            $date = Carbon::today()->subDays($daysAgo);
            $isArabic = app()->getLocale() === 'ar';

            return [
                'date' => $date->toDateString(),
                'label' => $isArabic
                    ? $date->translatedFormat('D j M')
                    : $date->format('D, M j'),
                'short' => $isArabic
                    ? $date->translatedFormat('D')
                    : $date->format('D'),
            ];
        });

        $startDate = Carbon::today()->subDays(6)->startOfDay();

        $leadsByDate = Lead::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date');

        $offersByDate = Offer::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date');

        $dealsByDate = CrmDeal::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $leadsData = [];
        $offersData = [];
        $dealsData = [];

        foreach ($days as $day) {
            $labels[] = $day['label'];
            $leadsData[] = (int) ($leadsByDate[$day['date']] ?? 0);
            $offersData[] = (int) ($offersByDate[$day['date']] ?? 0);
            $dealsData[] = (int) ($dealsByDate[$day['date']] ?? 0);
        }

        $projects = Project::query()
            ->withCount([
                'units',
                'units as available_units_count' => fn ($q) => $q->where('status', UnitStatus::Available->value),
                'units as reserved_units_count' => fn ($q) => $q->where('status', UnitStatus::Reserved->value),
                'units as sold_units_count' => fn ($q) => $q->where('status', UnitStatus::Sold->value),
            ])
            ->withSum('units as total_inventory_value', 'current_price')
            ->latest('updated_at')
            ->limit(6)
            ->get();

        $dealStages = CrmStage::query()
            ->withCount('deals')
            ->withSum('deals as total_value', 'value')
            ->orderBy('sort_order')
            ->get();

        $recentDeals = CrmDeal::query()
            ->with(['stage', 'assignedUser', 'contact', 'customer', 'project'])
            ->latest()
            ->limit(5)
            ->get();

        $recentLeads = Lead::query()
            ->with(['assignedSales', 'customer', 'leadSource', 'tags'])
            ->latest()
            ->limit(5)
            ->get();

        $recentConversations = WhatsAppConversation::query()
            ->with(['assignedTo', 'linkedCustomer', 'linkedLead'])
            ->latest('last_message_at')
            ->limit(4)
            ->get();

        $recentActivities = CrmActivity::query()
            ->with(['creator', 'deal', 'contact'])
            ->latest()
            ->limit(5)
            ->get();

        $reservations = Reservation::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get();

        return [
            'sales' => [
                'labels' => $labels,
                'leads' => $leadsData,
                'offers' => $offersData,
                'deals' => $dealsData,
            ],
            'projects' => $projects,
            'deal_stages' => $dealStages,
            'recent_deals' => $recentDeals,
            'recent_leads' => $recentLeads,
            'recent_conversations' => $recentConversations,
            'recent_activities' => $recentActivities,
            'reservations' => $reservations,
        ];
    }
}
