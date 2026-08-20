<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AutomationSetting;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Single scheduled entry point for the sales automations.
 *
 * Reads the dashboard Automation settings and decides what to run:
 * - response SLA      (every pass, when enabled)
 * - overdue follow-ups (at most once per hour, when enabled)
 * - weekly report      (configured weekday + time, once per week)
 * - monthly scorecard  (configured day + time, once per month)
 */
class DispatchSalesAutomation extends Command
{
    protected $signature = 'sales:dispatch';

    protected $description = 'Run the enabled sales automations according to the dashboard settings';

    public function handle(): int
    {
        AutomationSetting::ensureDefaults();

        // 1. Response SLA — runs on every dispatcher pass.
        if (AutomationSetting::enabled('sla_enabled')) {
            Artisan::call('sales:enforce-response-sla', [
                '--sla-minutes' => AutomationSetting::integer('sla_alert_minutes', 30),
                '--escalate-minutes' => AutomationSetting::integer('sla_escalate_minutes', 120),
            ]);
            $this->line(trim(Artisan::output()));
        }

        // 2. Overdue follow-ups — at most once per hour.
        if (AutomationSetting::enabled('followup_alerts_enabled') && $this->shouldRun('followups_last_run', now()->subHour())) {
            Artisan::call('sales:notify-overdue-followups');
            $this->line(trim(Artisan::output()));
            AutomationSetting::setMany(['followups_last_run' => now()->toDateTimeString()]);
        }

        // 3. Weekly WhatsApp report — configured weekday + time, once per week.
        $weekStamp = now()->startOfWeek()->toDateString();
        if (AutomationSetting::enabled('weekly_report_enabled') && $this->isDue(
            (int) AutomationSetting::get('weekly_report_day', '1'),
            AutomationSetting::get('weekly_report_time', '10:00'),
            'weekly_report_last_sent',
            $weekStamp,
        )) {
            Artisan::call('sales:send-report', ['--days' => 7]);
            $this->line(trim(Artisan::output()));
            AutomationSetting::setMany(['weekly_report_last_sent' => $weekStamp]);
        }

        // 4. Monthly scorecard — configured day + time, once per month (previous month).
        $period = now()->subMonth()->format('Y-m');
        if (AutomationSetting::enabled('scorecard_enabled') && $this->isDue(
            (int) AutomationSetting::get('scorecard_day', '1'),
            AutomationSetting::get('scorecard_time', '02:00'),
            'scorecard_last_period',
            $period,
        )) {
            Artisan::call('sales:scorecard', ['--period' => $period]);
            $this->line(trim(Artisan::output()));
            AutomationSetting::setMany(['scorecard_last_period' => $period]);
        }

        $this->info('Sales automation dispatch complete.');

        return self::SUCCESS;
    }

    /**
     * Weekly tasks match the configured weekday; monthly tasks match the
     * configured day-of-month. The marker value (week stamp / month period)
     * guarantees the task only runs once per period.
     */
    protected function isDue(int $day, string $time, string $markerKey, string $markerValue): bool
    {
        $now = CarbonImmutable::now();
        $matches = (int) $now->dayOfWeek === $day || (int) $now->format('j') === $day;

        if (! $matches) {
            return false;
        }

        [$hour, $minute] = array_map('intval', explode(':', $time));

        if ($now->hour < $hour || ($now->hour === $hour && $now->minute < $minute)) {
            return false;
        }

        return AutomationSetting::get($markerKey) !== $markerValue;
    }

    /**
     * "Run at most once per interval" guard using a stored timestamp.
     */
    protected function shouldRun(string $markerKey, \Carbon\CarbonInterface $cutoff): bool
    {
        $lastRun = AutomationSetting::get($markerKey);

        if ($lastRun === null) {
            return true;
        }

        return CarbonImmutable::parse($lastRun)->lt($cutoff);
    }
}
