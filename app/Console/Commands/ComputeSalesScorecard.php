<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\SalesEvaluation;
use App\Models\User;
use App\Services\Reporting\SalesPerformanceService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

/**
 * Compute the monthly performance scorecard for the whole sales team.
 *
 * Each active salesperson receives a composite score (same formula as the
 * leaderboard) and a relative grade compared to the best performer of the
 * period: A >= 80% of the max, B >= 60%, C >= 40%, D below.
 */
class ComputeSalesScorecard extends Command
{
    protected $signature = 'sales:scorecard {--period= : YYYY-MM, defaults to the current month}';

    protected $description = 'Compute and store the monthly sales evaluations (scorecard)';

    public function handle(SalesPerformanceService $performance): int
    {
        $period = $this->option('period')
            ?: now()->format('Y-m');

        $start = CarbonImmutable::createFromFormat('Y-m-d', $period.'-01')->startOfMonth();
        $end = $start->addMonth();

        $userIds = User::query()
            ->active()
            ->role(['Sales Executive', 'Sales Manager'])
            ->pluck('id');

        if ($userIds->isEmpty()) {
            $this->warn('No active sales users found.');

            return self::SUCCESS;
        }

        $rows = $performance->metricsForUsers($userIds, $start)->sortByDesc('score')->values();
        $maxScore = (float) $rows->max('score');
        $hasActivity = $maxScore > 0;

        $stored = 0;
        foreach ($rows as $row) {
            $score = (float) $row['score'];
            // Without any activity in the period, no grade is assigned yet.
            $grade = null;
            if ($hasActivity) {
                $ratio = $score / $maxScore;
                $grade = $ratio >= 0.8 ? 'A' : ($ratio >= 0.6 ? 'B' : ($ratio >= 0.4 ? 'C' : 'D'));
            }

            $metrics = [
                'conversations' => $row['conversations'],
                'messages_sent' => $row['messages_sent'],
                'avg_response_minutes' => $row['avg_response_minutes'],
                'leads' => $row['leads'],
                'offers' => $row['offers'],
                'reservations' => $row['reservations'],
                'deals' => $row['deals'],
                'deals_won' => $row['deals_won'],
                'deal_value' => $row['deal_value'],
                'lead_rate' => $row['lead_rate'],
            ];

            SalesEvaluation::query()->updateOrCreate(
                ['user_id' => $row['user_id'], 'period' => $period],
                [
                    'score' => $score,
                    'grade' => $grade,
                    'metrics' => $metrics,
                ],
            );

            $stored++;
            $this->line("{$row['user_name']} — score {$score} — grade {$grade}");
        }

        $this->info("Scorecard stored for {$stored} salesperson(s) in {$period}.");

        return self::SUCCESS;
    }
}
