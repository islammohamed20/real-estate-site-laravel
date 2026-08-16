<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerAccountController extends Controller
{
    public function index(): View
    {
        $customer = Auth::guard('customer')->user()->load([
            'reservations.unit',
            'offers',
            'deals',
            'documents',
            'plans.unit',
            'plans.project',
        ]);

        return view('account.index', [
            'customer' => $customer,
        ]);
    }
}
