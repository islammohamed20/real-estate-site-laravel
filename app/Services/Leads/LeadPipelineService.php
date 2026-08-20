<?php

declare(strict_types=1);

namespace App\Services\Leads;

use App\Enums\LeadStage;
use App\Events\LeadStageChanged;
use App\Events\LeadStageUpdated;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeadPipelineService
{
    public function moveToStage(Lead $lead, LeadStage $stage, ?User $actor = null, ?string $notes = null): Lead
    {
        return DB::transaction(function () use ($lead, $stage, $actor, $notes): Lead {
            $previousStage = $lead->stage;
            $lead->stage = $stage;
            $lead->save();

            $lead->stageHistory()->create([
                'user_id' => $actor?->id,
                'stage_from' => $previousStage,
                'stage_to' => $stage,
                'notes' => $notes,
                'changed_at' => now(),
            ]);

            LeadStageChanged::dispatch($lead, $previousStage, $stage, $actor);
            LeadStageUpdated::dispatch($lead, $previousStage?->value, $stage->value);

            return $lead->refresh();
        });
    }
}
