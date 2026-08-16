@php
    $leadOptions = $leadOptions ?? collect();
    $customerOptions = $customerOptions ?? collect();
    $userOptions = $users ?? collect();
@endphp

<div class="space-y-6">
    <section class="app-card app-card--gradient space-y-5">
        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/15 text-blue-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
                <h3 class="text-lg font-semibold text-white">{{ __('New task') }}</h3>
                <p class="text-sm text-slate-400">{{ __('Assign a follow-up action.') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('dashboard.crm.tasks.store') }}" class="grid gap-4 sm:grid-cols-2">
            @csrf
            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Task title') }}</label>
                <input type="text" name="title" class="app-input" placeholder="{{ __('e.g. Call back to confirm viewing') }}" value="{{ old('title') }}" required>
            </div>
            
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Priority') }}</label>
                <select name="priority" class="app-select" required>
                    <option value="low">{{ __('Low') }}</option>
                    <option value="normal" selected>{{ __('Normal') }}</option>
                    <option value="high">{{ __('High') }}</option>
                    <option value="urgent">{{ __('Urgent') }}</option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Due Date') }}</label>
                <input type="datetime-local" name="due_at" class="app-input" value="{{ old('due_at') }}">
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Assign to') }}</label>
                <select name="assigned_to" class="app-select">
                    <option value="">{{ __('Unassigned') }}</option>
                    @foreach ($userOptions as $id => $name)
                        <option value="{{ $id }}" @selected(old('assigned_to') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:col-span-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Related to') }}</label>
                    <select name="related_type" class="app-select" required>
                        <option value="lead" @selected(old('related_type') === 'lead')>{{ __('Lead') }}</option>
                        <option value="customer" @selected(old('related_type') === 'customer')>{{ __('Customer') }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Contact Name') }}</label>
                    <select name="related_id" class="app-select" required>
                        <optgroup label="{{ __('Leads') }}">
                            @foreach ($leadOptions as $id => $name)
                                <option value="{{ $id }}" @selected(old('related_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="{{ __('Customers') }}">
                            @foreach ($customerOptions as $id => $name)
                                <option value="{{ $id }}" @selected(old('related_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Notes') }}</label>
                <textarea name="description" class="app-textarea min-h-24" placeholder="{{ __('Internal instructions...') }}">{{ old('description') }}</textarea>
            </div>

            <button type="submit" class="app-button sm:col-span-2">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ __('Save task') }}
            </button>
        </form>
    </section>

    <div class="space-y-4">
        <h3 class="px-1 text-sm font-bold uppercase tracking-widest text-slate-500">{{ __('Open Tasks') }}</h3>
        <div class="grid gap-3">
            @forelse ($tasks as $task)
                <article class="group rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:border-white/20 hover:bg-white/[0.07]">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex gap-3">
                            <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border-2 {{ $task->status === 'completed' ? 'border-emerald-500 bg-emerald-500/20 text-emerald-400' : 'border-slate-600 bg-transparent text-transparent' }}">
                                @if($task->status === 'completed')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="font-semibold text-white group-hover:text-brand-300 transition-colors {{ $task->status === 'completed' ? 'line-through text-slate-500' : '' }}">{{ $task->title }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke-width="2"/></svg>
                                        {{ __(ucfirst($task->priority)) }}
                                    </span>
                                    @if ($task->due_at)
                                        <span class="flex items-center gap-1 {{ $task->due_at->isPast() && $task->status !== 'completed' ? 'text-rose-400' : '' }}">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="2"/><path d="M12 7v5l3 3" stroke-width="2"/></svg>
                                            {{ $task->due_at->diffForHumans() }}
                                        </span>
                                    @endif
                                    @if ($task->assignee)
                                        <span class="flex items-center gap-1 text-brand-400">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke-width="2"/><circle cx="12" cy="7" r="4" stroke-width="2"/></svg>
                                            {{ $task->assignee->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('dashboard.crm.tasks.update', $task) }}" class="opacity-0 group-hover:opacity-100 transition">
                                @csrf @method('PUT')
                                <select name="status" class="h-8 rounded-lg border border-white/10 bg-slate-900 px-2 text-[10px] font-bold uppercase text-slate-400 focus:ring-0" onchange="this.form.submit()">
                                    <option value="open" @selected($task->status === 'open')>{{ __('Open') }}</option>
                                    <option value="in_progress" @selected($task->status === 'in_progress')>{{ __('In Progress') }}</option>
                                    <option value="completed" @selected($task->status === 'completed')>{{ __('Done') }}</option>
                                    <option value="cancelled" @selected($task->status === 'cancelled')>{{ __('Cancel') }}</option>
                                </select>
                            </form>
                            <form method="POST" id="delete-task-{{ $task->id }}" action="{{ route('dashboard.crm.tasks.destroy', $task) }}">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete task') }}', '{{ __('Remove this task permanently?') }}', () => document.getElementById('delete-task-{{ $task->id }}').submit(), '{{ __('Delete') }}')" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-rose-500/10 hover:text-rose-400 transition">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @if ($task->description)
                        <p class="mt-3 pl-9 text-sm text-slate-400 leading-relaxed">{{ $task->description }}</p>
                    @endif
                </article>
            @empty
                <div class="flex min-h-32 flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 text-center text-sm text-slate-500">
                    <p>{{ __('No tasks found.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
