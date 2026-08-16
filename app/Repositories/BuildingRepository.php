<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Building;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\BuildingRepositoryInterface;
use Illuminate\Support\Collection;

class BuildingRepository extends BaseRepository implements BuildingRepositoryInterface
{
    public function __construct(Building $model)
    {
        parent::__construct($model);
    }

    public function forProject(int $projectId): Collection
    {
        return $this->query()->where('project_id', $projectId)->orderBy('sort_order')->get();
    }
}
