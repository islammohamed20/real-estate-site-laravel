<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Search\GlobalSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request, GlobalSearchService $search): JsonResponse
    {
        return response()->json([
            'data' => $search->search($request->string('q')->toString()),
        ]);
    }
}
