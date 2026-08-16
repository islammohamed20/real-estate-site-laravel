@php
    $leadOptions = $leadOptions ?? collect();
    $customerOptions = $customerOptions ?? collect();
    $dealOptions = $dealOptions ?? collect();
    $organizationOptions = $organizationOptions ?? collect();
    $contactOptions = $contactOptions ?? collect();
@endphp

<div class="space-y-6">
    <section class="app-card app-card--gradient space-y-5">
        <div class="flex items-center gap-3 border-b border-white/5 pb-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
                <h3 class="text-lg font-semibold text-white">{{ __('New note') }}</h3>
                <p class="text-sm text-slate-400">{{ __('Attach a log, update, or meeting summary.') }}</p>
            </div>
        </div>

        <form x-data="{ relatedType: '{{ old('related_type', 'lead') }}' }" method="POST" action="{{ route('dashboard.crm.notes.store') }}" class="grid gap-4 sm:grid-cols-2">
            @csrf
            <div class="grid grid-cols-2 gap-3 sm:col-span-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Type') }}</label>
                    <select name="type" class="app-select" required>
                        <option value="call">{{ __('Call') }}</option>
                        <option value="meeting">{{ __('Meeting') }}</option>
                        <option value="note" selected>{{ __('General Note') }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Date & Time') }}</label>
                    <input type="datetime-local" name="noted_at" class="app-input" value="{{ old('noted_at', now()->format('Y-m-d\TH:i')) }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:col-span-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Related to') }}</label>
                    <select name="related_type" x-model="relatedType" class="app-select" required>
                        <option value="lead" @selected(old('related_type') === 'lead')>{{ __('Lead') }}</option>
                        <option value="customer" @selected(old('related_type') === 'customer')>{{ __('Customer') }}</option>
                        <option value="deal" @selected(old('related_type') === 'deal')>{{ __('Deal') }}</option>
                        <option value="organization" @selected(old('related_type') === 'organization')>{{ __('Organization') }}</option>
                        <option value="contact" @selected(old('related_type') === 'contact')>{{ __('Contact') }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Record') }}</label>

                    <select name="related_id" class="app-select" :disabled="relatedType !== 'lead'" :required="relatedType === 'lead'" x-show="relatedType === 'lead'">
                        <optgroup label="{{ __('Leads') }}">
                            @foreach ($leadOptions as $id => $name)
                                <option value="{{ $id }}" @selected(old('related_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </optgroup>
                    </select>

                    <select name="related_id" class="app-select" :disabled="relatedType !== 'customer'" :required="relatedType === 'customer'" x-show="relatedType === 'customer'">
                        <optgroup label="{{ __('Customers') }}">
                            @foreach ($customerOptions as $id => $name)
                                <option value="{{ $id }}" @selected(old('related_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </optgroup>
                    </select>

                    <select name="related_id" class="app-select" :disabled="relatedType !== 'deal'" :required="relatedType === 'deal'" x-show="relatedType === 'deal'">
                        <optgroup label="{{ __('Deals') }}">
                            @foreach ($dealOptions as $id => $title)
                                <option value="{{ $id }}" @selected(old('related_id') == $id)>{{ $title }}</option>
                            @endforeach
                        </optgroup>
                    </select>

                    <select name="related_id" class="app-select" :disabled="relatedType !== 'organization'" :required="relatedType === 'organization'" x-show="relatedType === 'organization'">
                        <optgroup label="{{ __('Organizations') }}">
                            @foreach ($organizationOptions as $id => $name)
                                <option value="{{ $id }}" @selected(old('related_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </optgroup>
                    </select>

                    <select name="related_id" class="app-select" :disabled="relatedType !== 'contact'" :required="relatedType === 'contact'" x-show="relatedType === 'contact'">
                        <optgroup label="{{ __('Contacts') }}">
                            @foreach ($contactOptions as $id => $name)
                                <option value="{{ $id }}" @selected(old('related_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-300">{{ __('Body') }}</label>
                <textarea name="body" class="app-textarea min-h-32" placeholder="{{ __('Type your update here...') }}" required>{{ old('body') }}</textarea>
            </div>

            <button type="submit" class="app-button sm:col-span-2">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ __('Save note') }}
            </button>
        </form>
    </section>

    <div class="space-y-4">
        <h3 class="px-1 text-sm font-bold uppercase tracking-widest text-slate-500">{{ __('Recent Timeline') }}</h3>
        <div class="space-y-4 border-l-2 border-white/5 ml-4 pl-6">
            @forelse ($notes as $note)
                <article class="relative group">
                    <span class="absolute -left-[31px] top-0 flex h-4 w-4 items-center justify-center rounded-full bg-slate-900 ring-4 ring-slate-950">
                        <span class="h-1.5 w-1.5 rounded-full {{ $note->type === 'call' ? 'bg-blue-400' : ($note->type === 'meeting' ? 'bg-violet-400' : 'bg-emerald-400') }}"></span>
                    </span>
                    
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:border-white/20 hover:bg-white/[0.07]">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">{{ __(ucfirst($note->type)) }}</p>
                                    <span class="text-slate-600">·</span>
                                    <p class="text-xs text-slate-400">{{ $note->noted_at?->diffForHumans() ?? $note->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
                                    <span class="font-semibold text-white">{{ $note->user?->name ?? __('System') }}</span>
                                    @if ($note->noteable)
                                        <span class="text-slate-500">{{ __('logged for') }}</span>
                                        <span class="font-medium text-brand-400">{{ $note->noteable->name }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <form method="POST" id="delete-note-{{ $note->id }}" action="{{ route('dashboard.crm.notes.destroy', $note) }}" class="opacity-0 group-hover:opacity-100 transition">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmAction('{{ __('Delete note') }}', '{{ __('Are you sure you want to delete this note?') }}', () => document.getElementById('delete-note-{{ $note->id }}').submit(), '{{ __('Delete') }}')" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-rose-500/10 hover:text-rose-400 transition">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                        </div>
                        
                        <div class="mt-3 whitespace-pre-wrap text-sm text-slate-300 leading-relaxed">{{ $note->body }}</div>
                    </div>
                </article>
            @empty
                <div class="flex min-h-32 flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/5 text-center text-sm text-slate-500 -ml-6">
                    <p>{{ __('No logs found yet.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
