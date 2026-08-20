<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Enums\LeadStage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\LeadRequest;
use App\Models\Crm\CrmActivity;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadAssignmentHistory;
use App\Models\LeadSource;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Unit;
use App\Models\User;
use App\Services\CRM\CustomerConversionService;
use App\Services\CRM\CustomerLeadSyncService;
use App\Services\CRM\LeadCreationService;
use App\Services\Leads\LeadPipelineService;
use App\Services\PushNotificationService;
use App\Support\WhatsApp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lead::class);

        $query = Lead::query()
            ->with(['customer', 'assignedSales', 'leadSource', 'tags', 'interestedProjects', 'interestedUnits.unit'])
            ->when($request->filled('search'), function (Builder $q, $search) {
                $q->where(function (Builder $sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('whatsapp', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('stage'), fn (Builder $q, $stage) => $q->where('stage', $stage))
            ->when($request->filled('priority'), fn (Builder $q, $priority) => $q->where('priority', $priority))
            ->when($request->filled('source'), fn (Builder $q, $source) => $q->where('source', $source))
            ->when($request->filled('assigned'), fn (Builder $q, $user) => $q->where('assigned_sales_id', $user))
            ->when($request->filled('status'), fn (Builder $q, $status) => $q->where('status', $status));

        if (! auth()->user()?->hasAnyPermission(['view all leads', 'manage crm'])) {
            $query->where('assigned_sales_id', auth()->id());
        }

        $leads = $query->latest()->paginate(15)->withQueryString();

        return view('crm.leads.index', [
            'leads' => $leads,
            'stages' => collect(LeadStage::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]),
            'priorities' => ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'],
            'sources' => LeadSource::active()->orderBy('sort_order')->pluck('name', 'name'),
            'users' => User::active()->pluck('name', 'id'),
            'filters' => $request->only(['search', 'stage', 'priority', 'source', 'assigned', 'status']),
        ]);
    }

    public function show(Lead $lead): View
    {
        $this->authorize('view', $lead);

        $lead->load([
            'customer',
            'assignedSales',
            'leadSource',
            'interestedProjects',
            'interestedUnits.unit.project',
            'stageHistory.user',
            'assignmentHistory.fromUser',
            'assignmentHistory.toUser',
            'assignmentHistory.assignedBy',
            'tasks.assignee',
            'recordedNotes.user',
            'offers',
            'reservations',
            'tags',
            'activities.creator',
            'documents',
        ]);

        return view('crm.leads.show', [
            'lead' => $lead,
            'stages' => collect(LeadStage::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]),
            'priorities' => ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'],
            'sources' => LeadSource::active()->orderBy('sort_order')->pluck('name', 'name'),
            'users' => User::active()->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::query()->with('project')->orderBy('unit_number')->get()->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]),
            'tags' => Tag::active()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id'),
            'activityTypes' => CrmActivity::types(),
        ]);
    }

    public function edit(Lead $lead): View
    {
        $this->authorize('update', $lead);

        $lead->load(['interestedProjects', 'interestedUnits.unit.project', 'tags', 'assignedSales', 'leadSource']);

        return view('crm.leads.edit', [
            'lead' => $lead,
            'stages' => collect(LeadStage::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]),
            'priorities' => ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'],
            'sources' => LeadSource::active()->orderBy('sort_order')->pluck('name', 'id'),
            'users' => User::active()->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::query()->with('project')->orderBy('unit_number')->get()->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]),
            'tags' => Tag::active()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(LeadRequest $request, LeadCreationService $leadCreationService): RedirectResponse
    {
        $this->authorize('create', Lead::class);

        $lead = $leadCreationService->createFromInquiry($request->validated());
        $this->syncTags($lead, $request->input('tags', []));
        $this->syncInterestedUnits($lead, $request->input('interested_unit_ids', []));

        app(PushNotificationService::class)->notifyCrmEvent('📋 ليد جديد', $lead->name ?? 'New lead', '/real-statement-control/crm/leads/'.$lead->id);

        return redirect()->route('dashboard.crm.leads.show', $lead)
            ->with('status', __('Lead created successfully.'));
    }

    public function update(LeadRequest $request, Lead $lead, CustomerLeadSyncService $syncService): RedirectResponse
    {
        $this->authorize('update', $lead);

        $lead->update($request->validated());

        if ($request->has('interested_project_ids')) {
            $lead->interestedProjects()->sync($request->input('interested_project_ids', []));
        }

        $this->syncInterestedUnits($lead, $request->input('interested_unit_ids', []));
        $this->syncTags($lead, $request->input('tags', []));

        // Keep the owning customer's profile data in sync with the lead.
        $syncService->syncFromLead($lead);

        return redirect()->route('dashboard.crm.leads.show', $lead)
            ->with('status', __('Lead updated successfully.'));
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $this->authorize('delete', $lead);

        $lead->delete();

        return redirect()->route('dashboard.crm.leads.index')
            ->with('status', __('Lead moved to trash.'));
    }

    public function assign(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorize('assign', $lead);

        $validated = $request->validate([
            'assigned_sales_id' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $fromUserId = $lead->assigned_sales_id;

        $lead->update(['assigned_sales_id' => $validated['assigned_sales_id']]);

        LeadAssignmentHistory::query()->create([
            'lead_id' => $lead->id,
            'from_user_id' => $fromUserId,
            'to_user_id' => $validated['assigned_sales_id'],
            'assigned_by' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
            'assigned_at' => now(),
        ]);

        return redirect()->route('dashboard.crm.leads.show', $lead)
            ->with('status', __('Lead assigned successfully.'));
    }

    public function moveStage(Request $request, Lead $lead, LeadPipelineService $service): JsonResponse
    {
        $this->authorize('update', $lead);

        $validated = $request->validate([
            'stage' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $stage = LeadStage::tryFrom($validated['stage']);

        if (! $stage) {
            return response()->json(['message' => __('Invalid stage.')], 422);
        }

        $oldStage = $lead->stage->label();
        $service->moveToStage($lead, $stage, auth()->user(), $validated['notes'] ?? null);

        // Push notification: lead stage changed
        $stageLabels = [
            'contacted' => 'تم التواصل',
            'interested' => 'مُهتم',
            'meeting' => 'اجتماع',
            'site_visit' => 'زيارة الموقع',
            'negotiation' => 'تفاوض',
            'reserved' => 'محجوز',
            'contract' => 'عقد',
            'delivered' => 'تم التسليم',
            'converted' => 'تم التحويل لعميل',
        ];
        $stageLabel = $stageLabels[$stage->value] ?? $stage->label();
        if ($stage->value === 'converted') {
            app(PushNotificationService::class)->notifyCrmEvent(
                '🎉 تم تحويل لييد لعميل',
                ($lead->name ?: $lead->phone) . ' — ' . $stageLabel,
                '/real-statement-control/crm/leads/' . $lead->id
            );
        } elseif (in_array($stage->value, ['reserved', 'contract'])) {
            app(PushNotificationService::class)->notifyCrmEvent(
                '📋 تقدم في المبيعات',
                ($lead->name ?: $lead->phone) . ' — ' . $oldStage . ' → ' . $stageLabel,
                '/real-statement-control/crm/leads/' . $lead->id
            );
        }

        return response()->json([
            'message' => __('Stage updated.'),
            'lead' => $lead->fresh()->load('stageHistory'),
        ]);
    }

    public function convert(Lead $lead, CustomerConversionService $conversionService): RedirectResponse
    {
        $this->authorize('update', $lead);

        $customer = $conversionService->convertFromLead($lead);

        return redirect()->route('dashboard.crm.customers.show', $customer)
            ->with('status', __('Lead converted to customer successfully.'));
    }

    /**
     * AJAX: find existing leads/customers with the same phone number.
     */
    public function checkDuplicate(Request $request): JsonResponse
    {
        $phone = trim((string) $request->query('phone', ''));
        $ignoreId = (int) $request->query('ignore', 0);

        if ($phone === '') {
            return response()->json(['leads' => [], 'customers' => []]);
        }

        $normalized = WhatsApp::number($phone) ?? preg_replace('/[^0-9]/', '', $phone);

        $leads = Lead::query()
            ->where(function (Builder $q) use ($phone, $normalized): void {
                $q->where('phone', $phone)->orWhere('phone', $normalized);
            })
            ->when($ignoreId > 0, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
            ->with(['customer:id,name'])
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'name', 'phone', 'stage', 'status', 'customer_id', 'created_at']);

        $customers = Customer::query()
            ->where(function (Builder $q) use ($phone, $normalized): void {
                $q->where('phone', $phone)->orWhere('phone', $normalized);
            })
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'name', 'phone', 'created_at']);

        return response()->json([
            'leads' => $leads->map(fn (Lead $lead) => [
                'id' => $lead->id,
                'name' => $lead->name,
                'phone' => $lead->phone,
                'stage' => $lead->stage,
                'status' => $lead->status,
                'created_at' => $lead->created_at?->format('Y-m-d'),
                'url' => route('dashboard.crm.leads.show', $lead),
            ])->values(),
            'customers' => $customers->map(fn (Customer $customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'created_at' => $customer->created_at?->format('Y-m-d'),
                'url' => route('dashboard.crm.customers.edit', $customer),
            ])->values(),
        ]);
    }

    private function syncTags(Lead $lead, array $tagIds): void
    {
        $ids = collect($tagIds)
            ->map(fn ($value) => is_numeric($value) ? (int) $value : Tag::firstOrCreate(['name' => $value])->id)
            ->filter()
            ->values()
            ->all();

        $lead->tags()->sync($ids);
    }

    private function syncInterestedUnits(Lead $lead, array $unitIds): void
    {
        $lead->interestedUnits()->whereNotIn('unit_id', $unitIds)->delete();

        foreach ($unitIds as $unitId) {
            $lead->interestedUnits()->firstOrCreate(
                ['unit_id' => $unitId],
                ['customer_id' => $lead->customer_id, 'status' => 'interested', 'priority' => 'normal']
            );
        }
    }
}
