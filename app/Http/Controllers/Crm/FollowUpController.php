<?php

declare(strict_types=1);

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\FollowUpRequest;
use App\Models\FollowUp;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(Request $request): View
    {
        $query = FollowUp::query()
            ->with(['lead', 'customer', 'deal', 'assignee'])
            ->when($request->filled('status'), fn (Builder $q, $status) => $q->where('status', $status))
            ->when($request->filled('type'), fn (Builder $q, $type) => $q->where('type', $type))
            ->when($request->filled('assigned'), fn (Builder $q, $assigned) => $q->where('assigned_to', $assigned));

        $followUps = $query->orderBy('follow_up_at')->paginate(15)->withQueryString();

        return view('crm.follow_ups.index', [
            'followUps' => $followUps,
            'users' => User::active()->pluck('name', 'id'),
            'filters' => $request->only(['status', 'type', 'assigned']),
        ]);
    }

    public function store(FollowUpRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        if (! empty($validated['completed_at'])) {
            $validated['status'] = 'completed';
        }

        FollowUp::query()->create($validated);

        return back()->with('status', __('Follow-up scheduled successfully.'));
    }

    public function update(FollowUpRequest $request, FollowUp $followUp): RedirectResponse
    {
        $data = $request->only([
            'lead_id', 'customer_id', 'deal_id', 'assigned_to', 'follow_up_at', 'type',
            'channel', 'priority', 'reminder', 'notes', 'status', 'completed_at',
        ]);

        if (! empty($data['completed_at']) && $followUp->completed_at === null) {
            $data['status'] = 'completed';
        } elseif (empty($data['completed_at']) && $followUp->completed_at !== null) {
            $data['status'] = $data['status'] ?? 'pending';
        }

        $followUp->update($data);

        return back()->with('status', __('Follow-up updated successfully.'));
    }

    public function destroy(FollowUp $followUp): RedirectResponse
    {
        $followUp->delete();

        return back()->with('status', __('Follow-up deleted successfully.'));
    }

    public function complete(FollowUp $followUp): JsonResponse
    {
        $followUp->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json(['message' => __('Follow-up completed.'), 'follow_up' => $followUp->fresh()]);
    }
}
