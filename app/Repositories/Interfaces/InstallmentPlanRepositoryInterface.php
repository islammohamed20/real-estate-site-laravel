<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\InstallmentPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InstallmentPlanRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?InstallmentPlan;
}
