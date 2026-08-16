<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Building;
use App\Models\CompanyProfile;
use App\Models\Crm\CrmDeal;
use App\Models\Customer;
use App\Models\Floor;
use App\Models\InstallmentPlan;
use App\Models\InstallmentTemplate;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Phase;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use App\Policies\BuildingPolicy;
use App\Policies\CompanyProfilePolicy;
use App\Policies\CrmDealPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\FloorPolicy;
use App\Policies\InstallmentPlanPolicy;
use App\Policies\InstallmentTemplatePolicy;
use App\Policies\LeadPolicy;
use App\Policies\OfferPolicy;
use App\Policies\PhasePolicy;
use App\Policies\ProjectPolicy;
use App\Policies\ReservationPolicy;
use App\Policies\UnitPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        CompanyProfile::class => CompanyProfilePolicy::class,
        Project::class => ProjectPolicy::class,
        Phase::class => PhasePolicy::class,
        Building::class => BuildingPolicy::class,
        Floor::class => FloorPolicy::class,
        Unit::class => UnitPolicy::class,
        Customer::class => CustomerPolicy::class,
        CrmDeal::class => CrmDealPolicy::class,
        Lead::class => LeadPolicy::class,
        Offer::class => OfferPolicy::class,
        Reservation::class => ReservationPolicy::class,
        Document::class => DocumentPolicy::class,
        InstallmentTemplate::class => InstallmentTemplatePolicy::class,
        InstallmentPlan::class => InstallmentPlanPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
