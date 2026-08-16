@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')
    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-4">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('Tasks') }}</h1>
                    <p class="mt-2 text-sm text-slate-300">{{ __('Track follow-up actions across leads, customers and deals.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-lg">
        <div class="p-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($tasks as $task)
                    <div class="app-card app-card--gradient p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-lg font-semibold text-white">{{ $task->title }}</h3>
                                <p class="text-sm text-slate-400">@php($taskType = strtolower(class_basename($task->taskable_type ?? ''))){{ $taskType === 'lead' ? __('Lead') : ($taskType === 'customer' ? __('Customer') : ($taskType === 'cmrdeal' ? __('Deal') : $taskType)) }} #{{ $task->taskable_id }} • {{ $task->taskable?->name ?? '—' }}</p>
                            </div>
                            @include('crm.partials.status-badge', ['status' => $task->status === 'completed' ? 'completed' : $task->priority, 'label' => $task->status === 'completed' ? __('Completed') : __(ucfirst($task->priority))])
                        </div>
                        <p class="mt-2 text-sm text-slate-300">{{ $task->description }}</p>
                        <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                            <span>{{ $task->assignee?->name ?? __('Unassigned') }}</span>
                            <span>•</span>
                            <span>{{ $task->due_at?->format('Y-m-d H:i') ?? __('No due date') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-white/10 bg-white/5 p-8 text-center text-slate-400">
                        <p>{{ __('No tasks found.') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $tasks->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
