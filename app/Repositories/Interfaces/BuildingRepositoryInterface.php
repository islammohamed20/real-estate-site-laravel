<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Building;
use Illuminate\Support\Collection;

interface BuildingRepositoryInterface
{
    public function forProject(int $projectId): Collection;

    public function find(int $id): ?Building;
}
