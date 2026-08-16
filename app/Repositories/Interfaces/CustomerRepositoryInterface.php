<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function paginate(string $search = '', int $perPage = 15): LengthAwarePaginator;

    public function findByPhone(string $phone): ?Customer;
}
