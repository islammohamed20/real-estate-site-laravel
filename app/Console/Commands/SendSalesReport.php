<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\EvolutionApiService;
use App\Services\Reporting\SalesPerformanceService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

/**
 * Send a compact sales performance summary to the sales manager's WhatsApp.
 * Scheduled weekly (default) or monthly via --days.
 */
class SendSalesReport extends Command
{
    protected $signature = 'sales:send-report {--days=7 : Reporting window in days}';

    protected $description = 'Send the sales performance summary to the sales manager on WhatsApp';

    public function handle(EvolutionApiService $evolution, SalesPerformanceService $performance): int
    {
        if (! $evolution->isConfigured()) {
            $this->warn('Evolution API is not configured — report not sent.');

            return self::SUCCESS;
        }

        $days = max(1, min(365, (int) $this->option('days')));
        $since = CarbonImmutable::now()->subDays($days);

        $userIds = \App\Models\User::query()
            ->role(['Sales Executive', 'Sales Manager'])
            ->pluck('id');

        $rows = $performance->metricsForUsers($userIds, $since)->sortByDesc('score')->values();
        $totals = $performance->sumMetrics($rows);

        $lines = [
            "📊 تقرير أداء المبيعات — آخر {$days} يوم",
            '──────────────────────',
            "🧑‍💼 الليدرز: {$totals['leads']}",
            "📄 العروض: {$totals['offers']}",
            "🔖 الحجوزات: {$totals['reservations']}",
            "✅ الصفقات المغلقة: {$totals['deals_won']} من {$totals['deals']}",
            "💰 قيمة الصفقات: ".number_format($totals['deal_value'], 0).' ج.م',
            '──────────────────────',
        ];

        foreach ($rows->take(5) as $index => $row) {
            $lines[] = sprintf(
                '%d. %s — %d ليدر، %d صفقة مغلقة، %s ج.م',
                $index + 1,
                $row['user_name'],
                $row['leads'],
                $row['deals_won'],
                number_format($row['deal_value'], 0),
            );
        }

        if ($rows->isEmpty()) {
            $lines[] = 'لا يوجد نشاط في هذه الفترة.';
        }

        $sent = $evolution->sendToSalesManager(implode("\n", $lines));

        $this->info($sent ? 'Sales report sent to the manager.' : 'Failed to send the sales report.');

        return $sent ? self::SUCCESS : self::FAILURE;
    }
}
