<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstallmentCalculatorRequest;
use App\Services\Installments\InstallmentCalculatorService;
use Illuminate\Http\JsonResponse;

class InstallmentController extends Controller
{
    public function calculate(InstallmentCalculatorRequest $request, InstallmentCalculatorService $calculator): JsonResponse
    {
        return response()->json([
            'data' => $calculator->calculate($request->validated()),
        ]);
    }
}
