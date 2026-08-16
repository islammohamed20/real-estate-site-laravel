<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Models\Customer;
use Illuminate\Support\Collection;

class CustomerTimelineService
{
    public function build(Customer $customer): Collection
    {
        $events = collect();

        foreach ($customer->leads()->with('stageHistory.user', 'offers', 'reservations', 'recordedNotes.user', 'tasks.assignee', 'interestedUnits.unit.project')->get() as $lead) {
            $events->push([
                'id' => "lead-{$lead->id}",
                'type' => 'lead_created',
                'icon' => 'user-plus',
                'color' => 'emerald',
                'title' => __('Lead created'),
                'body' => $lead->name.' — '.$lead->stage->label(),
                'user' => $lead->assignedSales?->name,
                'at' => $lead->created_at,
                'link' => route('dashboard.crm.leads.show', $lead),
            ]);

            foreach ($lead->stageHistory as $history) {
                $events->push([
                    'id' => "stage-{$history->id}",
                    'type' => 'stage_change',
                    'icon' => 'arrow-right',
                    'color' => 'blue',
                    'title' => __('Stage changed'),
                    'body' => ($history->stage_from?->label() ?? '—').' → '.$history->stage_to?->label(),
                    'user' => $history->user?->name,
                    'at' => $history->changed_at,
                    'link' => route('dashboard.crm.leads.show', $lead),
                ]);
            }

            foreach ($lead->offers as $offer) {
                $events->push([
                    'id' => "offer-{$offer->id}",
                    'type' => 'offer',
                    'icon' => 'document-text',
                    'color' => 'violet',
                    'title' => __('Offer issued'),
                    'body' => __('Offer').' '.$offer->offer_number.' — '.'EGP '.number_format((float) $offer->total_amount),
                    'user' => $offer->sales?->name,
                    'at' => $offer->issue_date,
                    'link' => null,
                ]);
            }

            foreach ($lead->reservations as $reservation) {
                $events->push([
                    'id' => "reservation-{$reservation->id}",
                    'type' => 'reservation',
                    'icon' => 'key',
                    'color' => 'amber',
                    'title' => __('Reservation created'),
                    'body' => __('Reservation').' '.$reservation->reservation_number.' — '.'EGP '.number_format((float) $reservation->deposit_amount),
                    'user' => $reservation->sales?->name,
                    'at' => $reservation->reserved_at,
                    'link' => null,
                ]);
            }

            foreach ($lead->recordedNotes as $note) {
                $events->push([
                    'id' => "note-{$note->id}",
                    'type' => 'note',
                    'icon' => 'chat',
                    'color' => 'slate',
                    'title' => __('Note').': '.__(ucfirst($note->type)),
                    'body' => $note->body,
                    'user' => $note->user?->name,
                    'at' => $note->noted_at ?? $note->created_at,
                    'link' => route('dashboard.crm.leads.show', $lead),
                ]);
            }

            foreach ($lead->tasks as $task) {
                $events->push([
                    'id' => "task-{$task->id}",
                    'type' => 'task',
                    'icon' => 'check-circle',
                    'color' => 'cyan',
                    'title' => __('Task').': '.$task->title,
                    'body' => $task->status.' • '.$task->priority,
                    'user' => $task->assignee?->name,
                    'at' => $task->due_at ?? $task->created_at,
                    'link' => route('dashboard.crm.leads.show', $lead),
                ]);
            }
        }

        foreach ($customer->recordedNotes as $note) {
            $events->push([
                'id' => "customer-note-{$note->id}",
                'type' => 'note',
                'icon' => 'chat',
                'color' => 'slate',
                'title' => __('Customer note').': '.__(ucfirst($note->type)),
                'body' => $note->body,
                'user' => $note->user?->name,
                'at' => $note->noted_at ?? $note->created_at,
                'link' => null,
            ]);
        }

        foreach ($customer->tasks as $task) {
            $events->push([
                'id' => "customer-task-{$task->id}",
                'type' => 'task',
                'icon' => 'check-circle',
                'color' => 'cyan',
                'title' => __('Customer task').': '.$task->title,
                'body' => $task->status.' • '.$task->priority,
                'user' => $task->assignee?->name,
                'at' => $task->due_at ?? $task->created_at,
                'link' => null,
            ]);
        }

        return $events->sortByDesc('at')->values();
    }
}
