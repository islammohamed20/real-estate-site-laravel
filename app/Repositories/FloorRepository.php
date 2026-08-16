<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Floor;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\FloorRepositoryInterface;
use Illuminate\Support\Collection;

class FloorRepository extends BaseRepository implements FloorRepositoryInterface
{
    public function __construct(Floor $model)
    {
        parent::__construct($model);
    }

    public function forBuilding(int $buildingId): Collection
    {
        return $this->query()->where('building_id', $buildingId)->orderBy('sort_order')->get();
    }
}
