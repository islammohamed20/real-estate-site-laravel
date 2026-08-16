<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Floor;
use Illuminate\Support\Collection;

interface FloorRepositoryInterface
{
    public function forBuilding(int $buildingId): Collection;

    public function find(int $id): ?Floor;
}
