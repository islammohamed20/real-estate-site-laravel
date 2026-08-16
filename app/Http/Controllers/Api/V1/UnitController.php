<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\UnitRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request, UnitRepositoryInterface $units): JsonResponse
    {
        return response()->json([
            'data' => $units->paginate(15, [
                'project_id' => $request->integer('project_id') ?: null,
                'status' => $request->input('status'),
                'search' => $request->string('search')->toString(),
            ]),
        ]);
    }
}
