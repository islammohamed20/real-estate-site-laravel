<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Unit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UnitRepositoryInterface
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function available(): Collection;

    public function findByNumber(string $unitNumber): ?Unit;
}
