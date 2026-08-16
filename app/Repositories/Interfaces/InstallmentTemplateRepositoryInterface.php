<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\InstallmentTemplate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface InstallmentTemplateRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function all(): Collection;

    public function find(int $id): ?InstallmentTemplate;
}
