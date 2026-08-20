<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\UnitStatus;
use App\Models\Unit;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\UnitRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UnitRepository extends BaseRepository implements UnitRepositoryInterface
{
    public function __construct(Unit $model)
    {
        parent::__construct($model);
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->query()->with(['project', 'phase', 'building', 'floor']);

        if (! empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (! empty($filters['building_id'])) {
            $query->where('building_id', (int) $filters['building_id']);
        }

        if (! empty($filters['unit_type'])) {
            $query->where('unit_type', $filters['unit_type']);
        }

        if (! empty($filters['bedrooms'])) {
            if ($filters['bedrooms'] === '5+') {
                $query->where('bedrooms', '>=', 5);
            } else {
                $query->where('bedrooms', (int) $filters['bedrooms']);
            }
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status'] instanceof UnitStatus ? $filters['status']->value : $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($builder) use ($search): void {
                $builder->where('unit_number', 'like', $search)
                    ->orWhere('unit_type', 'like', $search)
                    ->orWhereHas('project', function ($p) use ($search): void {
                        $p->where('name', 'like', $search)
                            ->orWhere('location', 'like', $search)
                            ->orWhere('city', 'like', $search);
                    });
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function available(): Collection
    {
        return $this->query()->where('status', UnitStatus::Available)->get();
    }

    public function findByNumber(string $unitNumber): ?Unit
    {
        return $this->query()->where('unit_number', $unitNumber)->first();
    }
}
