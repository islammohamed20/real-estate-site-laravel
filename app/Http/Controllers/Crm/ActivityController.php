<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\ActivityRequest;
use App\Models\Crm\CrmActivity;
use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmDeal;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ActivityController extends Controller
{
    public function store(ActivityRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $activityable = $this->resolveActivityable($validated);

        if (! $activityable) {
            return back()->with('error', __('Related record not found.'));
        }

        $this->authorize('view', $activityable);

        $data = [
            'created_by' => auth()->id(),
            'activityable_id' => $activityable->id,
            'activityable_type' => $activityable::class,
            'type' => $validated['type'],
            'subject' => $validated['subject'] ?? null,
            'body' => $validated['body'] ?? null,
            'due_at' => $validated['due_at'] ?? null,
            'completed_at' => $validated['completed_at'] ?? null,
            'outcome' => $validated['outcome'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'contact_id' => $validated['contact_id'] ?? null,
        ];

        if ($activityable instanceof CrmDeal) {
            $data['deal_id'] = $activityable->id;
        } elseif ($activityable instanceof Lead && $activityable->offers()->exists()) {
            $data['deal_id'] = optional($activityable->offers()->first())->deal_id;
        } elseif ($activityable instanceof Customer) {
            $data['deal_id'] = optional($activityable->deals()->first())->id;
        }

        CrmActivity::query()->create($data);

        return back()->with('status', __('Activity logged successfully.'));
    }

    public function update(ActivityRequest $request, CrmActivity $activity): RedirectResponse
    {
        $this->authorize('view', $activity->activityable);

        $activity->update($request->only([
            'type', 'subject', 'body', 'due_at', 'completed_at', 'outcome', 'duration', 'contact_id',
        ]));

        return back()->with('status', __('Activity updated successfully.'));
    }

    public function destroy(CrmActivity $activity): RedirectResponse
    {
        $this->authorize('view', $activity->activityable);

        $activity->delete();

        return back()->with('status', __('Activity deleted successfully.'));
    }

    public function complete(CrmActivity $activity): JsonResponse
    {
        $this->authorize('view', $activity->activityable);

        $activity->update(['completed_at' => now()]);

        return response()->json(['message' => __('Activity marked as completed.'), 'activity' => $activity->fresh()]);
    }

    private function resolveActivityable(array $validated): ?object
    {
        if (! empty($validated['lead_id'])) {
            return Lead::query()->find($validated['lead_id']);
        }

        if (! empty($validated['customer_id'])) {
            return Customer::query()->find($validated['customer_id']);
        }

        if (! empty($validated['deal_id'])) {
            return CrmDeal::query()->find($validated['deal_id']);
        }

        if (! empty($validated['contact_id'])) {
            return CrmContact::query()->find($validated['contact_id']);
        }

        return null;
    }
}
