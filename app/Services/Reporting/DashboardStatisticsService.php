<?php

declare(strict_types=1);

namespace App\Services\Reporting;

use App\Enums\UnitStatus;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;

class DashboardStatisticsService
{
    public function overview(): array
    {
        return [
            'projects' => Project::query()->count(),
            'units' => Unit::query()->count(),
            'available_units' => Unit::query()->where('status', UnitStatus::Available)->count(),
            'reserved_units' => Unit::query()->where('status', UnitStatus::Reserved)->count(),
            'sold_units' => Unit::query()->where('status', UnitStatus::Sold)->count(),
            'today_leads' => Lead::query()->whereDate('created_at', today())->count(),
            'today_offers' => Offer::query()->whereDate('created_at', today())->count(),
            'active_reservations' => Reservation::query()->where('status', 'active')->count(),
        ];
    }

    public function chartData(): array
    {
        return [
            'sales' => [
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'values' => [0, 0, 0, 0, 0, 0, 0],
            ],
            'projects' => Project::query()->withCount('units')
                ->select(['id', 'name', 'slug', 'status', 'featured', 'location', 'updated_at'])
                ->orderByDesc('updated_at')
                ->limit(8)
                ->get(),
            'reservations' => Reservation::query()->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(7)
                ->get(),
        ];
    }
}
