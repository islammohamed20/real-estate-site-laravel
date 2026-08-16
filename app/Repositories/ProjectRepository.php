<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return $this->query()->orderBy('sort_order')->orderBy('name')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->withCount('units')->orderBy('sort_order')->orderBy('name')->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Project
    {
        return $this->query()->where('slug', $slug)->first();
    }
}
