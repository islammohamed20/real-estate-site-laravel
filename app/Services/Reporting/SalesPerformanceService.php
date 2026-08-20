<?php

declare(strict_types=1);

namespace App\Services\Reporting;

use App\Models\Crm\CrmDeal;
use App\Models\Lead;
use App\Models\Offer;
use App\Models\Reservation;
use App\Models\SalesTeam;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Batched computation of combined WhatsApp + CRM sales metrics,
 * shared by the leaderboard and the monthly targets pages.
 */
class SalesPerformanceService
{
    /**
     * Compute the combined metric set for a list of user IDs since a date.
     *
     * @param  Collection<int, int>  $userIds
     * @return Collection<int, array<string, mixed>>
     */
    public function metricsForUsers(Collection $userIds, CarbonImmutable $since): Collection
    {
        $ids = $userIds->all();

        if ($ids === []) {
            return collect();
        }

        $leadsByUser = Lead::query()
            ->where('created_at', '>=', $since)
            ->whereIn('assigned_sales_id', $ids)
            ->selectRaw('assigned_sales_id, COUNT(*) as total')
            ->groupBy('assigned_sales_id')
            ->pluck('total', 'assigned_sales_id');

        $offersByUser = Offer::query()
            ->where('created_at', '>=', $since)
            ->whereIn('sales_id', $ids)
            ->selectRaw('sales_id, COUNT(*) as total')
            ->groupBy('sales_id')
            ->pluck('total', 'sales_id');

        $reservationsByUser = Reservation::query()
            ->where('created_at', '>=', $since)
            ->whereIn('sales_id', $ids)
            ->selectRaw('sales_id, COUNT(*) as total')
            ->groupBy('sales_id')
            ->pluck('total', 'sales_id');

        $deals = CrmDeal::query()
            ->where('created_at', '>=', $since)
            ->whereIn('assigned_to', $ids)
            ->get(['assigned_to', 'status', 'value'])
            ->groupBy('assigned_to');

        $conversationsByUser = WhatsAppConversation::query()
            ->where('created_at', '>=', $since)
            ->whereIn('assigned_to', $ids)
            ->get(['id', 'assigned_to'])
            ->groupBy('assigned_to');

        $convRep = $conversationsByUser->flatMap(fn ($rows, $uid) => $rows->mapWithKeys(fn ($c) => [$c->id => (int) $uid]));
        $convIds = $convRep->keys();

        $responseSeconds = [];
        $responsePairs = [];
        foreach ($userIds as $uid) {
            $responseSeconds[$uid] = 0;
            $responsePairs[$uid] = 0;
        }

        if ($convIds->isNotEmpty()) {
            $messages = WhatsAppMessage::query()
                ->whereIn('conversation_id', $convIds)
                ->orderBy('created_at')
                ->get(['conversation_id', 'direction', 'created_at'])
                ->groupBy('conversation_id');

            foreach ($messages as $conversationId => $rows) {
                $uid = $convRep[$conversationId] ?? null;
                if ($uid === null) {
                    continue;
                }
                $waitingIncoming = null;
                foreach ($rows as $message) {
                    if ($message->direction === WhatsAppMessage::DIRECTION_INCOMING) {
                        $waitingIncoming = $message->created_at;
                    } elseif ($message->direction === WhatsAppMessage::DIRECTION_OUTGOING && $waitingIncoming !== null) {
                        $responseSeconds[$uid] += abs($message->created_at->diffInSeconds($waitingIncoming));
                        $responsePairs[$uid]++;
                        $waitingIncoming = null;
                    }
                }
            }
        }

        $messagesByUser = WhatsAppMessage::query()
            ->where('created_at', '>=', $since)
            ->where('sender_user_id', '!=', null)
            ->whereIn('sender_user_id', $ids)
            ->selectRaw('sender_user_id, COUNT(*) as total')
            ->groupBy('sender_user_id')
            ->pluck('total', 'sender_user_id');

        $users = User::query()->whereIn('id', $ids)->get(['id', 'name', 'job_title'])->keyBy('id');

        $rows = collect();
        foreach ($userIds as $uid) {
            $dealRows = $deals->get($uid, collect());
            $dealCount = $dealRows->count();
            $dealsWon = $dealRows->where('status', 'won')->count();
            $dealValue = (float) $dealRows->whereIn('status', ['open', 'won'])->sum('value');

            $conversations = $conversationsByUser->get($uid, collect())->count();
            $pairs = $responsePairs[$uid] ?? 0;
            $avgResponseMinutes = $pairs > 0 ? round(($responseSeconds[$uid] ?? 0) / $pairs / 60, 1) : null;

            $leads = (int) ($leadsByUser[$uid] ?? 0);
            $offers = (int) ($offersByUser[$uid] ?? 0);
            $reservations = (int) ($reservationsByUser[$uid] ?? 0);

            $score = (float) round(
                $leads * 1
                + $offers * 3
                + $reservations * 5
                + $dealsWon * 10
                + ($dealValue / 50000)
                + ($avgResponseMinutes !== null && $avgResponseMinutes <= 5 ? 2 : 0),
                1
            );

            $user = $users->get($uid);

            $rows->push([
                'user_id' => $uid,
                'user_name' => $user?->name ?? __('Deleted user'),
                'job_title' => $user?->job_title,
                'conversations' => $conversations,
                'messages_sent' => (int) ($messagesByUser[$uid] ?? 0),
                'avg_response_minutes' => $avgResponseMinutes,
                'leads' => $leads,
                'offers' => $offers,
                'reservations' => $reservations,
                'deals' => $dealCount,
                'deals_won' => $dealsWon,
                'deal_value' => round($dealValue, 2),
                'lead_rate' => $leads > 0 ? round($dealCount / $leads * 100, 1) : 0.0,
                'score' => $score,
            ]);
        }

        return $rows;
    }

    /**
     * Aggregate the metrics of a team's members into one row.
     *
     * @return array<string, mixed>
     */
    public function metricsForTeam(SalesTeam $team, CarbonImmutable $since): array
    {
        return $this->sumMetrics(
            $this->metricsForUsers($team->members->pluck('id'), $since)
        );
    }

    /**
     * Sum a set of metric rows into totals (including a weighted total score).
     *
     * @return array<string, mixed>
     */
    public function sumMetrics(Collection $rows): array
    {
        $totals = [
            'conversations' => 0,
            'messages_sent' => 0,
            'leads' => 0,
            'offers' => 0,
            'reservations' => 0,
            'deals' => 0,
            'deals_won' => 0,
            'deal_value' => 0.0,
        ];
        foreach ($rows as $row) {
            foreach ($totals as $key => $_) {
                if ($key === 'deal_value') {
                    $totals[$key] += (float) $row[$key];
                } else {
                    $totals[$key] += (int) $row[$key];
                }
            }
        }
        $totals['deal_value'] = round($totals['deal_value'], 2);
        $totals['score'] = round(
            $totals['leads'] * 1
            + $totals['offers'] * 3
            + $totals['reservations'] * 5
            + $totals['deals_won'] * 10
            + ($totals['deal_value'] / 50000),
            1
        );

        return $totals;
    }
}
