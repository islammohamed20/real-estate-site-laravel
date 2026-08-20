<?php

declare(strict_types=1);

namespace App\Services\Sales;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Automatic lead distribution across the sales team.
 *
 * - New leads without an assigned salesperson are routed to the member with
 *   the lightest current workload (least-loaded).
 * - SLA escalations pick another salesperson at random (as configured by the
 *   sales manager) to give the customer a fresh, responsive contact.
 */
class LeadAssignmentService
{
    /**
     * Pick the salesperson with the fewest open leads.
     */
    public function pickLeastLoadedId(): ?int
    {
        $candidates = $this->candidates();

        if ($candidates->isEmpty()) {
            return null;
        }

        $counts = $this->openLeadCounts($candidates);

        return $candidates->sortBy(fn (User $user) => $counts[$user->id] ?? 0)->first()->id;
    }

    /**
     * Pick a random salesperson excluding the given user (SLA re-assignment).
     */
    public function pickRandomIdExcluding(int $excludeUserId): ?int
    {
        $candidates = $this->candidates()->reject(fn (User $user) => $user->id === $excludeUserId);

        if ($candidates->isEmpty()) {
            return null;
        }

        return $candidates->random()->id;
    }

    /**
     * Assign a lead to the least-loaded salesperson when it has no owner.
     */
    public function assignIfUnassigned(Lead $lead): bool
    {
        if ($lead->assigned_sales_id !== null) {
            return false;
        }

        $targetId = $this->pickLeastLoadedId();
        if ($targetId === null) {
            return false;
        }

        $lead->forceFill(['assigned_sales_id' => $targetId])->save();

        return true;
    }

    /**
     * Re-assign a lead to a new (random) salesperson, recording the transfer.
     */
    public function reassignRandomly(Lead $lead): ?int
    {
        $currentId = $lead->assigned_sales_id;
        $targetId = $this->pickRandomIdExcluding((int) $currentId);

        if ($targetId === null || $targetId === $currentId) {
            return null;
        }

        $lead->forceFill(['assigned_sales_id' => $targetId])->save();

        \App\Models\LeadAssignmentHistory::query()->create([
            'lead_id' => $lead->id,
            'from_user_id' => $currentId,
            'to_user_id' => $targetId,
            'assigned_by' => null,
            'notes' => __('Automatic re-assignment: no reply within the response SLA.'),
            'assigned_at' => now(),
        ]);

        return $targetId;
    }

    /**
     * Active users that can receive lead assignments (sales roles first).
     *
     * @return Collection<int, User>
     */
    protected function candidates(): Collection
    {
        return User::query()
            ->active()
            ->role(['Sales Executive', 'Sales Manager'])
            ->get(['id']);
    }

    /**
     * @param  Collection<int, User>  $candidates
     * @return array<int, int>
     */
    protected function openLeadCounts(Collection $candidates): array
    {
        return Lead::query()
            ->whereIn('assigned_sales_id', $candidates->pluck('id'))
            ->where('status', '!=', 'converted')
            ->selectRaw('assigned_sales_id, COUNT(*) as total')
            ->groupBy('assigned_sales_id')
            ->pluck('total', 'assigned_sales_id')
            ->all();
    }
}
