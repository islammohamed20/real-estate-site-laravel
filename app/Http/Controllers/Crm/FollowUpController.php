<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\FollowUpRequest;
use App\Notifications\CrmActivityNotification;
use App\Models\Crm\CrmDeal;
use App\Models\Customer;
use App\Models\FollowUp;
use App\Models\SalesTeam;
use App\Models\Lead;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(Request $request): View
    {
        $query = FollowUp::query()
            ->with(['lead', 'customer', 'deal', 'assignee'])
            ->when(! auth()->user()->hasAnyPermission(['view all follow-ups', 'manage crm']), function (Builder $q): void {
                $q->where(function (Builder $scope): void {
                    $scope->where('assigned_to', auth()->id())
                        ->orWhere('created_by', auth()->id());
                });
            })
            ->when($request->filled('status'), fn (Builder $q, $status) => $q->where('status', $status))
            ->when($request->filled('type'), fn (Builder $q, $type) => $q->where('type', $type))
            ->when($request->filled('assigned'), fn (Builder $q, $assigned) => $q->where('assigned_to', $assigned));

        $followUps = $query->orderBy('follow_up_at')->paginate(15)->withQueryString();

        $canViewAll = auth()->user()->hasAnyPermission(['view reports', 'manage crm']);

        $users = $this->salesExecutiveSelfAssignment()
            ? User::query()->whereKey(auth()->id())->pluck('name', 'id')
            : User::active()->pluck('name', 'id');

        return view('crm.follow_ups.index', [
            'followUps' => $followUps,
            'users' => $users,
            'leads' => Lead::query()
                ->when(! $canViewAll, fn ($q) => $q->where('assigned_sales_id', auth()->id()))
                ->orderBy('name')->pluck('name', 'id'),
            'customers' => Customer::query()
                ->when(! $canViewAll, fn ($q) => $q->whereHas('leads', fn ($lead) => $lead->where('assigned_sales_id', auth()->id())))
                ->orderBy('name')->pluck('name', 'id'),
            'deals' => CrmDeal::query()
                ->when(! $canViewAll, fn ($q) => $q->where(fn ($scope) => $scope
                    ->where('assigned_to', auth()->id())
                    ->orWhere('created_by', auth()->id())))
                ->orderBy('title')->pluck('title', 'id'),
            'types' => [
                'phone_call' => __('Phone call'),
                'whatsapp' => __('WhatsApp'),
                'email' => __('Email'),
                'meeting' => __('Meeting'),
                'site_visit' => __('Site visit'),
                'follow_up' => __('Follow-up'),
                'other' => __('Other'),
            ],
            'priorities' => [
                'low' => __('Low'),
                'normal' => __('Normal'),
                'high' => __('High'),
                'urgent' => __('Urgent'),
            ],
            'filters' => $request->only(['status', 'type', 'assigned']),
        ]);
    }

    public function store(FollowUpRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->authorizeRelated($validated);
        $validated['created_by'] = auth()->id();
        $validated['assigned_to'] = $this->salesExecutiveSelfAssignment()
            ? auth()->id()
            : ($validated['assigned_to'] ?? auth()->id());

        if (! empty($validated['completed_at'])) {
            $validated['status'] = 'completed';
        }

        $followUp = FollowUp::query()->create($validated);
        $followUp->load(['assignee', 'lead', 'customer', 'deal']);

        if ($followUp->assignee) {
            $clientName = $followUp->customer?->name
                ?? $followUp->lead?->name
                ?? $followUp->deal?->title
                ?? __('Customer');

            $notification = new CrmActivityNotification('followup_created', [
                'follow_up_id' => $followUp->id,
                'name' => $clientName,
                'at' => $followUp->follow_up_at?->format('Y-m-d H:i'),
                'action_url' => route('dashboard.crm.follow_ups.index'),
            ], auth()->user()?->name);

            if (! $followUp->assignee->hasAnyRole(['Administrator', 'Owner'])
                && $followUp->assignee->acceptsNotification('followup_created')) {
                Notification::send($followUp->assignee, $notification);
            }

            CrmActivityNotification::notifyRelevant($notification, $followUp->assignee, includeOwner: false);

            $managerIds = SalesTeam::query()
                ->whereHas('members', fn ($query) => $query->whereKey($followUp->assignee->id))
                ->pluck('manager_id');
            $recipientIds = $managerIds->push($followUp->assignee->id)->unique()->values();
            $pushRecipients = User::query()
                ->where('is_active', true)
                ->where(function ($query) use ($recipientIds): void {
                    $query->whereIn('id', $recipientIds)
                        ->orWhereHas('roles', fn ($role) => $role->whereIn('name', ['Administrator', 'Owner']));
                })
                ->get();

            app(PushNotificationService::class)->sendToUsers(
                $pushRecipients,
                'متابعة جديدة',
                $clientName.' — '.$followUp->follow_up_at?->format('Y-m-d H:i'),
                '/real-statement-control/crm/follow-ups',
                ['tag' => 'follow-up-created-'.$followUp->id],
            );
        }

        return back()->with('status', __('Follow-up scheduled successfully.'));
    }

    public function update(FollowUpRequest $request, FollowUp $followUp): RedirectResponse
    {
        $this->authorizeFollowUp($followUp);

        $data = $request->only([
            'lead_id', 'customer_id', 'deal_id', 'assigned_to', 'follow_up_at', 'type',
            'priority', 'reminder', 'notes', 'status', 'completed_at',
        ]);
        $this->authorizeRelated($data);

        if (! empty($data['completed_at']) && $followUp->completed_at === null) {
            $data['status'] = 'completed';
        } elseif (empty($data['completed_at']) && $followUp->completed_at !== null) {
            $data['status'] = $data['status'] ?? 'pending';
        }

        if (($data['follow_up_at'] ?? null) !== null
            && $data['follow_up_at'] !== $followUp->follow_up_at?->toDateTimeString()) {
            $data['overdue_notified_at'] = null;
            $data['status'] = 'pending';
        }

        $followUp->update($data);

        return back()->with('status', __('Follow-up updated successfully.'));
    }

    public function destroy(FollowUp $followUp): RedirectResponse
    {
        $this->authorizeFollowUp($followUp);

        $followUp->delete();

        return back()->with('status', __('Follow-up deleted successfully.'));
    }

    public function complete(FollowUp $followUp): JsonResponse
    {
        $this->authorizeFollowUp($followUp);

        $followUp->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json(['message' => __('Follow-up completed.'), 'follow_up' => $followUp->fresh()]);
    }

    private function salesExecutiveSelfAssignment(): bool
    {
        $user = auth()->user();

        return $user->hasRole('Sales Executive')
            && ! $user->hasAnyRole(['Administrator', 'Owner', 'Sales Manager']);
    }

    private function authorizeRelated(array $data): void
    {
        $related = null;
        if (! empty($data['lead_id'])) {
            $related = Lead::query()->find($data['lead_id']);
        } elseif (! empty($data['customer_id'])) {
            $related = Customer::query()->find($data['customer_id']);
        } elseif (! empty($data['deal_id'])) {
            $related = CrmDeal::query()->find($data['deal_id']);
        }

        if ($related !== null) {
            $this->authorize('view', $related);
        }
    }

    private function authorizeFollowUp(FollowUp $followUp): void
    {
        if (auth()->user()->hasAnyPermission(['manage crm', 'edit all follow-ups', 'delete follow-ups'])) {
            return;
        }

        abort_unless(
            $followUp->assigned_to === auth()->id() || $followUp->created_by === auth()->id(),
            403,
        );
    }
}
