<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\CustomerRequest;
use App\Models\Crm\CrmActivity;
use App\Models\Customer;
use App\Models\LeadSource;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\CrmActivityNotification;
use App\Services\CRM\CustomerLeadSyncService;
use App\Services\CRM\CustomerTimelineService;
use App\Services\PushNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Customer::class);

        $query = Customer::query()
            ->with(['leads', 'tags', 'interestedProjects'])
            ->withCount('leads')
            ->when($request->filled('search'), function (Builder $q, $search) {
                $q->where(function (Builder $sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('source'), fn (Builder $q, $source) => $q->where('source', $source))
            ->when($request->filled('assigned'), fn (Builder $q, $assigned) => $q->whereHas('leads', fn (Builder $l) => $l->where('assigned_sales_id', $assigned)));

        if (! auth()->user()?->hasAnyPermission(['view all customers', 'manage crm'])) {
            $query->whereHas('leads', fn (Builder $l) => $l->where('assigned_sales_id', auth()->id()));
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('crm.customers.index', [
            'customers' => $customers,
            'tags' => Tag::active()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id'),
            'users' => User::active()->pluck('name', 'id'),
            'filters' => $request->only(['search', 'source', 'assigned']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Customer::class);

        return view('crm.customers.edit', [
            'customer' => new Customer,
            'sources' => LeadSource::active()->orderBy('sort_order')->pluck('name', 'name'),
            'tags' => Tag::active()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id'),
            'users' => User::active()->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->get(),
        ]);
    }

    public function show(Customer $customer, CustomerTimelineService $timelineService): View
    {
        $this->authorize('view', $customer);

        $customer->load([
            'leads.assignedSales',
            'leads.stageHistory',
            'leads.recordedNotes.user',
            'leads.tasks',
            'leads.offers',
            'leads.reservations',
            'leads.interestedProjects',
            'leads.interestedUnits.unit.project',
            'recordedNotes.user',
            'tasks.assignee',
            'interestedProjects',
            'offers',
            'reservations',
            'deals',
            'plans.unit.project',
            'tags',
            'activities.creator',
            'documents',
        ]);

        $timeline = $timelineService->build($customer);

        return view('crm.customers.show', [
            'customer' => $customer,
            'timeline' => $timeline,
            'tags' => Tag::active()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id'),
            'users' => User::active()->pluck('name', 'id'),
            'sources' => LeadSource::active()->orderBy('sort_order')->pluck('name', 'name'),
            'activityTypes' => CrmActivity::types(),
        ]);
    }

    public function edit(Customer $customer): View
    {
        $this->authorize('update', $customer);

        $customer->load(['tags', 'interestedProjects']);

        return view('crm.customers.edit', [
            'customer' => $customer,
            'sources' => LeadSource::active()->orderBy('sort_order')->pluck('name', 'name'),
            'tags' => Tag::active()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id'),
            'users' => User::active()->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->get(),
        ]);
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        $this->authorize('create', Customer::class);

        $customer = Customer::query()->create($request->validated());

        if ($request->has('interested_project_ids')) {
            $customer->interestedProjects()->sync($request->input('interested_project_ids', []));
        }

        $this->syncTags($customer, $request->input('tags', []));

        CrmActivityNotification::notifyRelevant(new CrmActivityNotification(
            'customer',
            ['name' => $customer->name, 'action_url' => route('dashboard.crm.customers.show', $customer)],
            auth()->user()?->name,
        ), auth()->user());

        app(PushNotificationService::class)->notifyCrmEvent('👤 عميل جديد', $customer->name ?? 'New customer', '/real-statement-control/crm/customers/'.$customer->id);

        return redirect()->route('dashboard.crm.customers.show', $customer)
            ->with('status', __('Customer created successfully.'));
    }

    public function update(CustomerRequest $request, Customer $customer, CustomerLeadSyncService $syncService): RedirectResponse
    {
        $this->authorize('update', $customer);

        $customer->update($request->validated());

        if ($request->has('interested_project_ids')) {
            $customer->interestedProjects()->sync($request->input('interested_project_ids', []));
        }

        $this->syncTags($customer, $request->input('tags', []));

        // Keep every linked lead's profile data in sync with the customer.
        $syncService->syncFromCustomer($customer);

        return redirect()->route('dashboard.crm.customers.show', $customer)
            ->with('status', __('Customer updated successfully.'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return redirect()->route('dashboard.crm.customers.index')
            ->with('status', __('Customer moved to trash.'));
    }

    private function syncTags(Customer $customer, array $tagIds): void
    {
        $ids = collect($tagIds)
            ->map(fn ($value) => is_numeric($value) ? (int) $value : Tag::firstOrCreate(['name' => $value])->id)
            ->filter()
            ->values()
            ->all();

        $customer->tags()->sync($ids);
    }
}
