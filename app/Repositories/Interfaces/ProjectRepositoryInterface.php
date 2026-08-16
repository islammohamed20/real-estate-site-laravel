<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProjectRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function all(): Collection;

    public function findBySlug(string $slug): ?Project;
}
