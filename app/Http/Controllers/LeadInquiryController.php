<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LeadInquiryRequest;
use App\Services\CRM\LeadCreationService;
use Illuminate\Http\RedirectResponse;

class LeadInquiryController extends Controller
{
    public function store(LeadInquiryRequest $request, LeadCreationService $leadCreationService): RedirectResponse
    {
        $leadCreationService->createFromInquiry($request->validated());

        return back()->with('status', __('Your inquiry has been submitted successfully.'));
    }
}
