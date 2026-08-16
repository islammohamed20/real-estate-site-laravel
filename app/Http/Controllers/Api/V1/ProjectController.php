<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function index(ProjectRepositoryInterface $projects): JsonResponse
    {
        return response()->json([
            'data' => $projects->all(),
        ]);
    }

    public function show(string $slug, ProjectRepositoryInterface $projects): JsonResponse
    {
        $project = $projects->findBySlug($slug);

        abort_if($project === null, 404);

        return response()->json([
            'data' => $project->load(['phases', 'buildings', 'units']),
        ]);
    }
}
