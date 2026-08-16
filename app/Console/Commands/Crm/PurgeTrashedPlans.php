<?php

declare(strict_types=1);

namespace App\Console\Commands\Crm;

use App\Models\CompanyProfile;
use App\Models\InstallmentPlan;
use App\Models\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurgeTrashedPlans extends Command
{
    protected $signature = 'crm:purge-trashed-plans
                            {--days=0 : Permanently delete plans that have been in the trash longer than this many days (0 = use the retention period from Settings)}
                            {--auto : Mark this run as automatic (triggered by the scheduler)}';

    protected $description = 'Permanently delete installment plans that stayed in the trash past the retention period.';

    public function handle(): int
    {
        $runType = $this->option('auto') ? 'automatic' : 'manual';
        $settings = CompanyProfile::query()->find(1);

        if ($settings?->auto_purge_enabled === false) {
            Log::channel('daily')->info("Trash cleanup ({$runType}) skipped: automatic cleanup is disabled in Settings.");
            $this->info('Automatic trash cleanup is disabled in Settings. Nothing to purge.');

            return self::SUCCESS;
        }

        $days = (int) $this->option('days');
        if ($days < 1) {
            $days = max(1, (int) ($settings?->trash_retention_days ?? 30));
        }
        $cutoff = now()->subDays($days);

        $trashed = InstallmentPlan::onlyTrashed()
            ->where('deleted_at', '<', $cutoff)
            ->with(['customer', 'unit'])
            ->get();

        $count = $trashed->count();

        if ($count === 0) {
            Log::channel('daily')->info("Trash cleanup ({$runType}): no trashed installment plans older than {$days} day(s). Nothing to purge.");
            $this->info("No trashed installment plans older than {$days} day(s). Nothing to purge.");

            return self::SUCCESS;
        }

        $ids = $trashed->pluck('id');
        $names = $trashed->map(fn (InstallmentPlan $plan) => $plan->name
            ?? ($plan->customer?->name ? $plan->customer->name.' / '.($plan->unit?->unit_number ?? '#'.$plan->id) : '#'.$plan->id)
        )->values()->all();

        DB::transaction(function () use ($ids): void {
            // Detach any offer referencing the purged plans, then hard delete (items cascade via FK).
            Offer::query()->whereIn('installment_plan_id', $ids)->update(['installment_plan_id' => null]);
            InstallmentPlan::onlyTrashed()->whereIn('id', $ids)->forceDelete();
        });

        Log::channel('daily')->info(
            "Trash cleanup ({$runType}): permanently deleted {$count} installment plan(s) trashed for more than {$days} day(s).",
            [
                'plan_ids' => $ids->all(),
                'plan_names' => $names,
                'retention_days' => $days,
            ]
        );

        $this->info("Permanently deleted {$count} installment plan(s) trashed for more than {$days} day(s): ".implode(', ', $names));

        return self::SUCCESS;
    }
}
