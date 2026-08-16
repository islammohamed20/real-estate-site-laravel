<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\InstallmentPlan;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\InstallmentPlanRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InstallmentPlanRepository extends BaseRepository implements InstallmentPlanRepositoryInterface
{
    public function __construct(InstallmentPlan $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->with(['customer', 'project', 'unit', 'template'])->latest()->paginate($perPage);
    }
}
