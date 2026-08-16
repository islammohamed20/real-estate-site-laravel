<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Crm\ContactRequest;
use App\Http\Requests\Crm\OrganizationRequest;
use App\Http\Requests\LeadInquiryRequest;
use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmDeal;
use App\Models\Crm\CrmOrganization;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Project;
use App\Models\Task;
use App\Models\Unit;
use App\Models\User;
use App\Services\CRM\LeadCreationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmController extends Controller
{
    public function index(): View
    {
        $tasks = Task::query()->with(['assignee', 'creator', 'taskable'])->latest()->take(50)->get();
        $notes = Note::query()->with(['user', 'noteable'])->latest()->take(50)->get();

        $histories = Lead::query()->with(['customer', 'stageHistory.user', 'stageHistory.lead.customer'])->latest()->take(25)->get()->pluck('stageHistory')->flatten()->sortByDesc('changed_at')->take(25)->values();

        $timeline = new Collection;

        foreach ($histories as $history) {
            $timeline->push([
                'type' => 'stage',
                'at' => $history->changed_at,
                'title' => __('Stage changed'),
                'body' => $history->stage_from?->label().' → '.$history->stage_to?->label().($history->notes ? ' — '.$history->notes : ''),
                'user' => $history->user?->name,
                'link' => $history->lead?->customer?->name ?? $history->lead?->name,
            ]);
        }

        foreach ($tasks as $task) {
            $timeline->push([
                'type' => 'task',
                'at' => $task->created_at,
                'title' => $task->title,
                'body' => $task->description,
                'user' => $task->creator?->name,
                'link' => $task->taskable ? $task->taskable->name : null,
            ]);
        }

        foreach ($notes as $note) {
            $timeline->push([
                'type' => 'note',
                'at' => $note->noted_at ?? $note->created_at,
                'title' => __(':type note', ['type' => __(ucfirst($note->type))]),
                'body' => $note->body,
                'user' => $note->user?->name,
                'link' => $note->noteable ? $note->noteable->name : null,
            ]);
        }

        return view('crm.index', [
            'leads' => Lead::query()->with(['customer', 'assignedSales', 'interestedProjects'])->latest()->paginate(8),
            'customers' => Customer::query()->withCount('leads')->latest()->paginate(8),
            'stats' => [
                'leads' => Lead::query()->count(),
                'customers' => Customer::query()->count(),
                'follow_ups_today' => Lead::query()->whereDate('follow_up_at', today())->count(),
                'open_tasks' => Task::query()->whereNotIn('status', ['completed', 'cancelled'])->count(),
                'open_deals' => CrmDeal::query()->where('status', 'open')->count(),
            ],
            'tasks' => $tasks,
            'notes' => $notes,
            'timeline' => $timeline->sortByDesc('at')->values(),
            'users' => User::query()->active()->pluck('name', 'id'),
            'leadOptions' => Lead::query()->orderBy('name')->pluck('name', 'id'),
            'customerOptions' => Customer::query()->orderBy('name')->pluck('name', 'id'),
            'dealOptions' => CrmDeal::query()->orderBy('title')->pluck('title', 'id'),
            'organizationOptions' => CrmOrganization::query()->orderBy('name')->pluck('name', 'id'),
            'contactOptions' => CrmContact::query()->orderBy('first_name')->get()->mapWithKeys(fn ($c) => [$c->id => $c->full_name]),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::query()->with('project')->orderBy('unit_number')->get()->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]),
        ]);
    }

    public function quickCreate(): View
    {
        return view('crm.quick', [
            'users' => User::query()->active()->pluck('name', 'id'),
            'projects' => Project::query()->orderBy('name')->pluck('name', 'id'),
            'units' => Unit::query()->with('project')->orderBy('unit_number')->get()->mapWithKeys(fn ($u) => [$u->id => ($u->project?->name ?? __('Unit')).' #'.$u->unit_number]),
            'organizationOptions' => CrmOrganization::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function storeLead(LeadInquiryRequest $request, LeadCreationService $leadCreationService): RedirectResponse
    {
        $data = $request->validated();
        $data['source'] = $data['source'] ?? 'dashboard';

        $leadCreationService->createFromInquiry($data);

        return back()->with('status', __('Lead created successfully.'));
    }

    public function storeTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'related_type' => ['required', 'in:lead,customer'],
            'related_id' => ['required', 'integer'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'due_at' => ['nullable', 'date'],
        ]);

        $taskable = match ($validated['related_type']) {
            'lead' => Lead::query()->find($validated['related_id']),
            'customer' => Customer::query()->find($validated['related_id']),
        };

        if (! $taskable) {
            return back()->with('error', __('Related record not found.'));
        }

        $taskable->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'created_by' => auth()->id(),
            'priority' => $validated['priority'],
            'due_at' => $validated['due_at'] ?? null,
            'status' => 'open',
        ]);

        return back()->with('status', __('Task created successfully.'));
    }

    public function updateTask(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,in_progress,completed,cancelled'],
        ]);

        $task->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        return back()->with('status', __('Task updated successfully.'));
    }

    public function destroyTask(Task $task): RedirectResponse
    {
        $task->delete();

        return back()->with('status', __('Task deleted successfully.'));
    }

    public function storeNote(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string'],
            'type' => ['required', 'in:call,meeting,note'],
            'related_type' => ['required', 'in:lead,customer,deal,organization,contact'],
            'related_id' => ['required', 'integer'],
            'noted_at' => ['nullable', 'date'],
        ]);

        $noteable = match ($validated['related_type']) {
            'lead' => Lead::query()->find($validated['related_id']),
            'customer' => Customer::query()->find($validated['related_id']),
            'deal' => CrmDeal::query()->find($validated['related_id']),
            'organization' => CrmOrganization::query()->find($validated['related_id']),
            'contact' => CrmContact::query()->find($validated['related_id']),
        };

        if (! $noteable) {
            return back()->with('error', __('Related record not found.'));
        }

        $noteable->recordedNotes()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
            'type' => $validated['type'],
            'noted_at' => $validated['noted_at'] ?? now(),
        ]);

        return back()->with('status', __('Note created successfully.'));
    }

    public function destroyNote(Note $note): RedirectResponse
    {
        $note->delete();

        return back()->with('status', __('Note deleted successfully.'));
    }

    public function indexOrganizations(): View
    {
        $organizations = CrmOrganization::query()
            ->withCount('contacts')
            ->latest()
            ->paginate(15);

        return view('crm.organizations.index', ['organizations' => $organizations]);
    }

    public function showOrganization(CrmOrganization $organization): View
    {
        $organization->load(['contacts', 'deals']);

        return view('crm.organizations.show', [
            'organization' => $organization,
            'contacts' => $organization->contacts()->with('organization')->get(),
        ]);
    }

    public function editOrganization(CrmOrganization $organization): View
    {
        return view('crm.organizations.edit', ['organization' => $organization]);
    }

    public function updateOrganization(OrganizationRequest $request, CrmOrganization $organization): RedirectResponse
    {
        $organization->update($request->validated());

        return redirect()->route('dashboard.crm.organizations.show', $organization)
            ->with('status', __('Organization updated successfully.'));
    }

    public function destroyOrganization(CrmOrganization $organization): RedirectResponse
    {
        $organization->delete();

        return redirect()->route('dashboard.crm.organizations.index')
            ->with('status', __('Organization deleted successfully.'));
    }

    public function editContact(CrmContact $contact): View
    {
        return view('crm.contacts.edit', [
            'contact' => $contact,
            'organizations' => CrmOrganization::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function updateContact(ContactRequest $request, CrmContact $contact): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_primary'] = (bool) ($validated['is_primary'] ?? false);

        if ($validated['is_primary']) {
            CrmContact::query()
                ->where('organization_id', $validated['organization_id'])
                ->where('id', '!=', $contact->id)
                ->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return redirect()->route('dashboard.crm.organizations.show', $contact->organization_id)
            ->with('status', __('Contact updated successfully.'));
    }

    public function destroyContact(CrmContact $contact): RedirectResponse
    {
        $organizationId = $contact->organization_id;
        $contact->delete();

        return redirect()->route('dashboard.crm.organizations.show', $organizationId)
            ->with('status', __('Contact deleted successfully.'));
    }

    public function storeOrganization(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:255', 'regex:/^https?:\/\/.+/'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]);

        CrmOrganization::query()->create($validated);

        return back()->with('status', __('Organization created successfully.'));
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'exists:crm_organizations,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:100'],
            'is_primary' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]);

        $validated['is_primary'] = (bool) ($validated['is_primary'] ?? false);

        if ($validated['is_primary']) {
            CrmContact::query()->where('organization_id', $validated['organization_id'])->update(['is_primary' => false]);
        }

        CrmContact::query()->create($validated);

        return back()->with('status', __('Contact created successfully.'));
    }
}
