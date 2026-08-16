<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadInquiryRequest;
use App\Services\CRM\LeadCreationService;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    public function store(LeadInquiryRequest $request, LeadCreationService $leadCreationService): JsonResponse
    {
        $lead = $leadCreationService->createFromInquiry($request->validated());

        return response()->json([
            'message' => 'Lead created successfully.',
            'data' => $lead->load(['customer', 'assignedSales', 'interestedProjects']),
        ], 201);
    }
}
