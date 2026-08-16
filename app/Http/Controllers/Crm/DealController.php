<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmActivity;
use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmDeal;
use App\Models\Crm\CrmDealStageHistory;
use App\Models\Crm\CrmOrganization;
use App\Models\Crm\CrmPipeline;
use App\Models\Crm\CrmStage;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DealController extends Controller
{
    public function index(Request $request): View
    {
        $pipelineId = $request->input('pipeline', CrmPipeline::query()->where('is_default', true)->value('id'));

        $pipelines = CrmPipeline::query()
            ->with(['stages' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $pipeline = $pipelines->firstWhere('id', $pipelineId) ?? $pipelines->first();

        $stages = $pipeline?->stages ?? collect();

        $dealsQuery = CrmDeal::query()
            ->with(['contact', 'organization', 'assignedUser', 'activities' => fn ($q) => $q->latest()->limit(3)])
            ->where('pipeline_id', $pipeline?->id ?? 0)
            ->when($request->filled('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->filled('assigned'), fn ($q, $user) => $q->where('assigned_to', $user))
            ->when($request->filled('search'), function ($q, $search): void {
                $q->where(function ($sub) use ($search): void {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhereHas('organization', fn ($o) => $o->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('contact', fn ($c) => $c->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('updated_at');

        $deals = $dealsQuery->get()->groupBy('stage_id');

        $stats = [
            'open_deals' => CrmDeal::query()->where('status', 'open')->count(),
            'won_deals' => CrmDeal::query()->where('status', 'won')->count(),
            'lost_deals' => CrmDeal::query()->where('status', 'lost')->count(),
            'total_value' => CrmDeal::query()->whereIn('status', ['open', 'won'])->sum('value'),
        ];

        return view('crm.deals.index', [
            'pipelines' => $pipelines,
            'pipeline' => $pipeline,
            'stages' => $stages,
            'dealsByStage' => $deals,
            'stats' => $stats,
            'users' => User::query()->active()->pluck('name', 'id'),
            'statuses' => ['open', 'won', 'lost'],
            'filters' => $request->only(['status', 'assigned', 'search']),
            'organizations' => CrmOrganization::query()->orderBy('name')->pluck('name', 'id'),
            'contacts' => CrmContact::query()->orderBy('first_name')->get()->mapWithKeys(fn ($c) => [$c->id => $c->full_name]),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::query()->with('project')->orderBy('unit_number')->get()->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]),
            'leads' => Lead::query()->orderBy('name')->pluck('name', 'id'),
            'customers' => Customer::query()->orderBy('name')->pluck('name', 'id'),
            'currencies' => ['USD' => 'USD', 'EGP' => 'EGP', 'EUR' => 'EUR', 'GBP' => 'GBP', 'SAR' => 'SAR', 'AED' => 'AED'],
        ]);
    }

    public function show(CrmDeal $deal): View
    {
        $deal->load([
            'pipeline.stages',
            'stage',
            'organization.contacts',
            'contacts.organization',
            'assignedUser',
            'creator',
            'project',
            'unit',
            'lead',
            'customer',
            'activities.creator',
            'stageHistory.toStage',
            'stageHistory.fromStage',
            'stageHistory.changedBy',
        ]);

        $pipelines = CrmPipeline::query()
            ->with(['stages' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('crm.deals.show', [
            'deal' => $deal,
            'pipelines' => $pipelines,
            'users' => User::query()->active()->pluck('name', 'id'),
            'organizations' => CrmOrganization::query()->orderBy('name')->pluck('name', 'id'),
            'contacts' => CrmContact::query()->orderBy('first_name')->get()->mapWithKeys(fn ($c) => [$c->id => $c->full_name]),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::query()->with('project')->orderBy('unit_number')->get()->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]),
            'leads' => Lead::query()->orderBy('name')->pluck('name', 'id'),
            'customers' => Customer::query()->orderBy('name')->pluck('name', 'id'),
            'currencies' => ['USD' => 'USD', 'EGP' => 'EGP', 'EUR' => 'EUR', 'GBP' => 'GBP', 'SAR' => 'SAR', 'AED' => 'AED'],
            'activityTypes' => CrmActivity::types(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'pipeline_id' => 'required|exists:crm_pipelines,id',
            'stage_id' => 'required|exists:crm_stages,id',
            'organization_id' => 'nullable|exists:crm_organizations,id',
            'contact_id' => 'nullable|exists:crm_contacts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'customer_id' => 'nullable|exists:customers,id',
            'assigned_to' => 'nullable|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'unit_id' => 'nullable|exists:units,id',
            'value' => 'nullable|numeric|min:0',
            'currency_code' => 'nullable|string|max:10',
            'expected_close_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'source' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['stage_changed_at'] = now();

        $deal = DB::transaction(function () use ($validated): CrmDeal {
            $deal = CrmDeal::query()->create($validated);

            CrmDealStageHistory::query()->create([
                'deal_id' => $deal->id,
                'from_stage_id' => null,
                'to_stage_id' => $deal->stage_id,
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);

            if ($deal->contact_id) {
                $deal->contacts()->attach($deal->contact_id, ['is_primary' => true]);
            }

            return $deal;
        });

        return redirect()->route('dashboard.crm.deals.show', $deal)->with('success', __('Deal created.'));
    }

    public function update(Request $request, CrmDeal $deal): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'pipeline_id' => 'sometimes|required|exists:crm_pipelines,id',
            'stage_id' => 'sometimes|required|exists:crm_stages,id',
            'organization_id' => 'nullable|exists:crm_organizations,id',
            'contact_id' => 'nullable|exists:crm_contacts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'customer_id' => 'nullable|exists:customers,id',
            'assigned_to' => 'nullable|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'unit_id' => 'nullable|exists:units,id',
            'value' => 'nullable|numeric|min:0',
            'currency_code' => 'nullable|string|max:10',
            'expected_close_date' => 'nullable|date',
            'priority' => 'sometimes|required|in:low,medium,high',
            'source' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:open,won,lost',
        ]);

        $fromStageId = $deal->stage_id;

        DB::transaction(function () use ($deal, $validated, $fromStageId): void {
            $wasStageChanged = isset($validated['stage_id']) && $validated['stage_id'] != $fromStageId;

            if (isset($validated['status']) && $validated['status'] !== 'open' && $deal->status === 'open') {
                $validated['closed_at'] = now();
            } elseif (isset($validated['status']) && $validated['status'] === 'open' && $deal->status !== 'open') {
                $validated['closed_at'] = null;
            }

            $deal->update($validated);

            if ($wasStageChanged) {
                $deal->update(['stage_changed_at' => now()]);

                CrmDealStageHistory::query()->create([
                    'deal_id' => $deal->id,
                    'from_stage_id' => $fromStageId,
                    'to_stage_id' => $deal->stage_id,
                    'changed_by' => auth()->id(),
                    'changed_at' => now(),
                ]);

                $this->recordActivity($deal, 'follow_up', __('Stage changed'), null, null);
            }

            if (isset($validated['contact_id']) && $validated['contact_id']) {
                if (! $deal->contacts()->where('crm_contacts.id', $validated['contact_id'])->exists()) {
                    $deal->contacts()->attach($validated['contact_id'], ['is_primary' => $deal->contacts()->count() === 0]);
                }
            }
        });

        return redirect()->back()->with('success', __('Deal updated.'));
    }

    public function moveStage(Request $request, CrmDeal $deal): JsonResponse
    {
        $validated = $request->validate([
            'stage_id' => 'required|exists:crm_stages,id',
            'status' => 'nullable|in:open,won,lost',
        ]);

        $fromStageId = $deal->stage_id;
        $toStageId = $validated['stage_id'];

        if ($fromStageId === (int) $toStageId) {
            return response()->json(['message' => __('No change.')]);
        }

        $toStage = CrmStage::query()->findOrFail($toStageId);

        DB::transaction(function () use ($deal, $fromStageId, $toStage, $validated): void {
            $update = ['stage_id' => $toStage->id, 'stage_changed_at' => now()];

            if (! empty($validated['status'])) {
                $update['status'] = $validated['status'];
            } else {
                $stageName = strtolower($toStage->name);
                if (str_starts_with($stageName, 'won')) {
                    $update['status'] = 'won';
                } elseif (str_starts_with($stageName, 'lost') || str_starts_with($stageName, 'closed lost')) {
                    $update['status'] = 'lost';
                } else {
                    $update['status'] = 'open';
                }
            }

            $update['closed_at'] = $update['status'] !== 'open' ? now() : null;

            $deal->update($update);

            CrmDealStageHistory::query()->create([
                'deal_id' => $deal->id,
                'from_stage_id' => $fromStageId,
                'to_stage_id' => $toStage->id,
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);

            $this->recordActivity($deal, 'follow_up', __('Stage changed via board'), null, null);
        });

        return response()->json([
            'message' => __('Stage updated.'),
            'deal' => $deal->fresh()->load('stage'),
        ]);
    }

    public function storeActivity(Request $request, CrmDeal $deal): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:'.implode(',', CrmActivity::types()),
            'subject' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'due_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'outcome' => 'nullable|string|max:100',
            'duration' => 'nullable|string|max:30',
            'contact_id' => 'nullable|exists:crm_contacts,id',
        ]);

        $validated['deal_id'] = $deal->id;
        $validated['created_by'] = auth()->id();
        $validated['activityable_id'] = $deal->id;
        $validated['activityable_type'] = CrmDeal::class;

        CrmActivity::query()->create($validated);

        return redirect()->back()->with('success', __('Activity added.'));
    }

    public function updateActivity(Request $request, CrmDeal $deal, CrmActivity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'nullable|string|max:255',
            'body' => 'nullable|string',
            'due_at' => 'nullable|date',
            'completed_at' => 'nullable|date',
            'outcome' => 'nullable|string|max:100',
            'duration' => 'nullable|string|max:30',
            'contact_id' => 'nullable|exists:crm_contacts,id',
        ]);

        $activity->update($validated);

        return redirect()->back()->with('success', __('Activity updated.'));
    }

    public function destroyActivity(CrmDeal $deal, CrmActivity $activity): RedirectResponse
    {
        $activity->delete();

        return redirect()->back()->with('success', __('Activity deleted.'));
    }

    public function destroy(CrmDeal $deal): RedirectResponse
    {
        $deal->delete();

        return redirect()->route('dashboard.crm.deals.index')->with('success', __('Deal deleted.'));
    }

    private function recordActivity(CrmDeal $deal, string $type, ?string $subject, ?string $body, ?int $contactId): void
    {
        CrmActivity::query()->create([
            'deal_id' => $deal->id,
            'contact_id' => $contactId,
            'created_by' => auth()->id(),
            'activityable_id' => $deal->id,
            'activityable_type' => CrmDeal::class,
            'type' => $type,
            'subject' => $subject,
            'body' => $body,
        ]);
    }
}
