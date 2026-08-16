<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\InstallmentTemplate;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\InstallmentTemplateRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InstallmentTemplateRepository extends BaseRepository implements InstallmentTemplateRepositoryInterface
{
    public function __construct(InstallmentTemplate $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->orderByDesc('is_default')->orderBy('name')->paginate($perPage);
    }

    public function all(): Collection
    {
        return $this->query()->orderByDesc('is_default')->orderBy('name')->get();
    }

    public function find(int $id): ?InstallmentTemplate
    {
        return $this->query()->find($id);
    }
}
