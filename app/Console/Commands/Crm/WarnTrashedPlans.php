<?php

declare(strict_types=1);

namespace App\Console\Commands\Crm;

use App\Models\CompanyProfile;
use App\Models\InstallmentPlan;
use App\Models\User;
use App\Notifications\TrashedPlansPurgeWarning;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

class WarnTrashedPlans extends Command
{
    protected $signature = 'crm:warn-trashed-plans
                            {--days=0 : Retention period before trashed plans are permanently deleted (0 = use the retention period from Settings)}
                            {--before=7 : Warn when this many days remain before permanent deletion}';

    protected $description = 'Notify administrators before trashed installment plans are permanently deleted.';

    public function handle(): int
    {
        $settings = CompanyProfile::query()->find(1);

        if ($settings?->auto_purge_enabled === false) {
            $this->info('Automatic trash cleanup is disabled in Settings. No purge warning needed.');

            return self::SUCCESS;
        }

        $days = (int) $this->option('days');
        if ($days < 1) {
            $days = max(1, (int) ($settings?->trash_retention_days ?? 30));
        }
        $before = max(1, (int) $this->option('before'));
        $warningCutoff = now()->subDays($days - $before);

        // Plans that will be purged within the warning window.
        $approaching = InstallmentPlan::query()
            ->onlyTrashed()
            ->where('deleted_at', '<', $warningCutoff)
            ->with(['customer', 'unit'])
            ->get();

        if ($approaching->isEmpty()) {
            $this->info('No trashed plans approaching the purge window.');

            return self::SUCCESS;
        }

        // Skip plans we already warned about (dedupe against previously stored plan_ids).
        $warnedIds = DatabaseNotification::query()
            ->where('type', TrashedPlansPurgeWarning::class)
            ->get()
            ->flatMap(fn (DatabaseNotification $notification) => (array) ($notification->data['plan_ids'] ?? []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $toWarn = $approaching->filter(fn (InstallmentPlan $plan) => ! $warnedIds->contains($plan->id));

        if ($toWarn->isEmpty()) {
            $this->info('All approaching plans have already been warned about.');

            return self::SUCCESS;
        }

        $payload = $toWarn->map(fn (InstallmentPlan $plan) => [
            'id' => $plan->id,
            'customer_name' => $plan->customer?->name,
            'unit_number' => $plan->unit?->unit_number,
            'deleted_at' => (string) $plan->deleted_at,
        ])->values()->all();

        $recipients = User::query()
            ->where(fn (Builder $query) => $query->role(['Administrator', 'Sales Manager']))
            ->get();

        if ($recipients->isEmpty()) {
            $this->warn('No Administrator/Sales Manager users to notify.');

            return self::FAILURE;
        }

        Notification::send($recipients, new TrashedPlansPurgeWarning($payload, $before));

        $this->info('Warned '.count($toWarn).' plan(s) approaching permanent deletion ('.count($recipients).' user(s) notified).');

        return self::SUCCESS;
    }
}
