<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function paginate(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->query();

        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function ($builder) use ($like): void {
                $builder->where('name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function findByPhone(string $phone): ?Customer
    {
        return $this->query()->where('phone', $phone)->first();
    }
}
