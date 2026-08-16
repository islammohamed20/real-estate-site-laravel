<?php

declare(strict_types=1);

namespace App\Console\Commands\Crm;

use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmDeal;
use App\Models\Crm\CrmOrganization;
use App\Models\Crm\CrmPipeline;
use App\Models\Crm\CrmStage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearSampleData extends Command
{
    protected $signature = 'crm:clear-sample-data
                            {--all : Remove default pipeline and stages as well}
                            {--force : Skip confirmation in production}';

    protected $description = 'Clear CRM sample data created by CrmSeeder.';

    public function handle(): int
    {
        if (app()->isProduction() && ! $this->option('force')) {
            $this->error('This command is destructive. Run with --force in production.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('This will delete all CRM deals, contacts, organizations and optionally non-default pipelines. Continue?')) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }

        DB::transaction(function (): void {
            $deals = CrmDeal::query()->delete();
            $contacts = CrmContact::query()->delete();
            $organizations = CrmOrganization::query()->delete();

            if ($this->option('all')) {
                $stages = CrmStage::query()->delete();
                $pipelines = CrmPipeline::query()->delete();
            } else {
                $defaultPipeline = CrmPipeline::query()->where('slug', 'real-estate-sales')->orWhere('is_default', true)->first();

                if ($defaultPipeline) {
                    $stages = CrmStage::query()->whereNot('pipeline_id', $defaultPipeline->id)->delete();
                    $pipelines = CrmPipeline::query()->whereNot('id', $defaultPipeline->id)->delete();
                } else {
                    $stages = CrmStage::query()->delete();
                    $pipelines = CrmPipeline::query()->delete();
                }
            }

            $this->info("Deleted: {$deals} deals, {$contacts} contacts, {$organizations} organizations.");
            $this->info('Pipelines/Stages remaining or deleted based on --all option.');
        });

        $this->info('CRM sample data cleared successfully.');

        return self::SUCCESS;
    }
}
