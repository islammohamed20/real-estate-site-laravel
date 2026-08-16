<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\BuildingRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\FloorRepository;
use App\Repositories\InstallmentPlanRepository;
use App\Repositories\InstallmentTemplateRepository;
use App\Repositories\Interfaces\BuildingRepositoryInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\Interfaces\FloorRepositoryInterface;
use App\Repositories\Interfaces\InstallmentPlanRepositoryInterface;
use App\Repositories\Interfaces\InstallmentTemplateRepositoryInterface;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Interfaces\UnitRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\UnitRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(UnitRepositoryInterface::class, UnitRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(BuildingRepositoryInterface::class, BuildingRepository::class);
        $this->app->bind(FloorRepositoryInterface::class, FloorRepository::class);
        $this->app->bind(InstallmentTemplateRepositoryInterface::class, InstallmentTemplateRepository::class);
        $this->app->bind(InstallmentPlanRepositoryInterface::class, InstallmentPlanRepository::class);
    }
}
