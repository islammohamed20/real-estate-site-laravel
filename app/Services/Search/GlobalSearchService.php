<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\Customer;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;

class GlobalSearchService
{
    public function search(string $term): array
    {
        if ($term === '') {
            return [];
        }

        $like = '%'.$term.'%';

        return [
            'customers' => Customer::query()->where('name', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->limit(10)
                ->get(),
            'projects' => Project::query()->where('name', 'like', $like)->orWhere('code', 'like', $like)->limit(10)->get(),
            'units' => Unit::query()->where('unit_number', 'like', $like)->orWhere('unit_type', 'like', $like)->limit(10)->get(),
            'offers' => Offer::query()->where('offer_number', 'like', $like)->limit(10)->get(),
            'reservations' => Reservation::query()->where('reservation_number', 'like', $like)->limit(10)->get(),
        ];
    }
}
