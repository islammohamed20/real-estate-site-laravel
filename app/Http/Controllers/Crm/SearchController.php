<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = $request->get('q');

        $term = trim((string) $query);

        if ($term === '') {
            return view('crm.search.index', [
                'query' => '',
                'results' => [],
            ]);
        }

        $like = '%'.$term.'%';

        $results = [
            'leads' => Lead::query()
                ->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->limit(10)
                ->get(),
            'customers' => Customer::query()
                ->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->limit(10)
                ->get(),
            'offers' => Offer::query()
                ->where('offer_number', 'like', $like)
                ->limit(10)
                ->get(),
            'reservations' => Reservation::query()
                ->where('reservation_number', 'like', $like)
                ->limit(10)
                ->get(),
            'projects' => Project::query()
                ->where('name', 'like', $like)
                ->orWhere('location', 'like', $like)
                ->limit(10)
                ->get(),
            'units' => Unit::query()
                ->where('unit_number', 'like', $like)
                ->orWhere('unit_type', 'like', $like)
                ->limit(10)
                ->get(),
        ];

        return view('crm.search.index', [
            'query' => $term,
            'results' => $results,
        ]);
    }
}
