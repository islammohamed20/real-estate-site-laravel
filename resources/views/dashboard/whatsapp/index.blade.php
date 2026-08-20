@extends('layouts.dashboard')

@section('content')
    @php
        $canReply = auth()->user()->can('reply whatsapp');
    @endphp

    <div class="space-y-6" x-data="whatsappPanel()" x-init="init()">
        {{-- Header --}}
        <section class="dashboard-hero-card p-4 sm:p-6" :class="selected ? '!pb-3' : ''">
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="relative flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400 sm:h-12 sm:w-12 sm:rounded-2xl">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                    </span>
                    <div>
                        <h1 class="text-base font-bold text-white sm:text-lg">{{ __('WhatsApp Panel') }}</h1>
                        <p class="hidden text-xs text-slate-400 sm:block">{{ __('Reply to customers from the sales team, assign conversations and track deals.') }}</p>
                    </div>
                </div>

                {{-- Secondary actions: icons-only on mobile, labels on desktop --}}
                <div class="ms-auto flex items-center gap-1.5 sm:flex-wrap sm:gap-2" :class="selected ? 'hidden lg:flex' : ''">
                    <template x-if="!evolutionConfigured">
                        <a href="{{ route('dashboard.settings.index') }}" class="hidden sm:inline-flex items-center gap-1.5 rounded-xl border border-amber-500/30 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-300 hover:bg-amber-500/20" title="{{ __('Gateway not configured') }}">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01" stroke-linecap="round"/></svg>
                            {{ __('Gateway not configured') }}
                        </a>
                    </template>
                    <template x-if="evolutionConfigured && !connectionOpen">
                        <span class="hidden sm:inline-flex items-center gap-1.5 rounded-xl border border-rose-500/30 bg-rose-500/10 px-3 py-1.5 text-xs font-semibold text-rose-300" title="{{ __('WhatsApp disconnected') }}">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-rose-400"></span>
                            {{ __('WhatsApp disconnected') }}
                        </span>
                    </template>
                    <template x-if="evolutionConfigured && connectionOpen">
                        <span class="hidden sm:inline-flex items-center gap-1.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold text-emerald-300" title="{{ __('WhatsApp connected') }}">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            {{ __('WhatsApp connected') }}
                        </span>
                    </template>

                    @if($evolutionDashboardUrl && auth()->user()->hasAnyRole(['Administrator', 'Owner']))
                        <a href="{{ $evolutionDashboardUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-sky-500/20 bg-sky-500/10 text-sky-200 transition hover:bg-sky-500/20 sm:h-auto sm:w-auto sm:gap-1.5 sm:px-3 sm:py-1.5 sm:text-xs sm:font-semibold" title="{{ __('Open Evolution Dashboard') }}">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 3h7v7" stroke-linecap="round"/><path d="M10 14L21 3" stroke-linecap="round"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" stroke-linecap="round"/></svg>
                            <span class="hidden sm:inline">{{ __('Dashboard') }}</span>
                        </a>
                    @endif

                    <button @click="toggleSound()" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-200 transition hover:bg-white/10" :title="soundOn ? '{{ __('Notification sound on') }}' : '{{ __('Notification sound off') }}'">
                        <svg x-show="soundOn" x-cloak class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0" stroke-linecap="round"/></svg>
                        <svg x-show="!soundOn" x-cloak class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 9a5.15 5.15 0 0 1 0 6" stroke-linecap="round" stroke-linejoin="round"/><path d="M19.4 5.6a10.3 10.3 0 0 1 0 12.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 3l18 18" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    @if(auth()->user()->hasAnyRole(['Administrator', 'Owner']))
                        <button @click="registerWebhook()" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-200 transition hover:bg-white/10 sm:h-auto sm:w-auto sm:gap-1.5 sm:px-3 sm:py-1.5 sm:text-xs sm:font-semibold" title="{{ __('Register Webhook') }}">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke-linecap="round"/></svg>
                            <span class="hidden sm:inline">{{ __('Register Webhook') }}</span>
                        </button>
                    @endif

                    <a href="{{ route('dashboard.whatsapp.reports') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-200 transition hover:bg-white/10 sm:h-auto sm:w-auto sm:gap-1.5 sm:px-3 sm:py-1.5 sm:text-xs sm:font-semibold" title="{{ __('Reports') }}">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83" stroke-linecap="round"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                        <span class="hidden sm:inline">{{ __('Reports') }}</span>
                    </a>

                    <a href="{{ route('dashboard.whatsapp.templates.index') }}" class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-200 transition hover:bg-white/10 sm:h-auto sm:w-auto sm:gap-1.5 sm:px-3 sm:py-1.5 sm:text-xs sm:font-semibold" title="{{ __('Templates') }}">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-linecap="round"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-linecap="round"/></svg>
                        <span class="hidden sm:inline">{{ __('Templates') }}</span>
                    </a>

                    @if($canReply)
                        <button @click="openStartModal = true" type="button" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-500 px-3 py-2 text-xs font-bold text-white shadow-lg shadow-emerald-500/25 transition hover:bg-emerald-400">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>
                            <span class="hidden sm:inline">{{ __('New Conversation') }}</span>
                            <svg class="h-3.5 w-3.5 sm:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M8 12h8M12 8v8" stroke-linecap="round"/></svg>
                        </button>
                    @endif
                </div>
            </div>
        </section>

        {{-- Chat panel --}}
        <section class="app-card app-card--gradient !p-0 overflow-hidden">
            <div class="wa-chat flex" :class="selected ? 'wa-chat--open' : ''">

                {{-- ===== Conversations list (right in RTL) ===== --}}
                <div class="flex w-full shrink-0 flex-col border-e border-white/10 lg:w-80 xl:w-96" :class="selected ? 'hidden lg:flex' : 'flex'">
                    <div class="space-y-2 border-b border-white/10 p-3">
                        <div class="relative">
                            <svg class="pointer-events-none absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3" stroke-linecap="round"/></svg>
                            <input x-model="search" type="text" placeholder="{{ __('Search name or phone...') }}" class="w-full rounded-xl border border-white/10 bg-slate-950/40 py-2 pe-3 ps-9 text-sm text-white placeholder-slate-500 focus:border-brand-500 focus:outline-none">
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-slate-500 shrink-0">{{ __('Platform') }}:</span>
                            <select x-model="platformFilter" class="flex-1 rounded-xl border border-white/10 bg-slate-950/40 py-1.5 px-2 text-xs text-white focus:border-brand-500 focus:outline-none">
                                <option value="all">{{ __('All') }}</option>
                                <option value="whatsapp">🟢 WhatsApp</option>
                                <option value="facebook">🔵 Facebook</option>
                            </select>
                        </div>
                        <div class="flex gap-1.5">
                            <button @click="filter = 'all'; loadConversations()" type="button" class="flex-1 rounded-lg px-2 py-1.5 text-xs font-semibold transition" :class="filter === 'all' ? 'bg-brand-600 text-white' : 'bg-white/5 text-slate-400 hover:bg-white/10'">{{ __('All') }}</button>
                            <button @click="filter = 'new'; loadConversations()" type="button" class="flex-1 rounded-lg px-2 py-1.5 text-xs font-semibold transition" :class="filter === 'new' ? 'bg-amber-500 text-slate-950' : 'bg-white/5 text-slate-400 hover:bg-white/10'">{{ __('New') }}</button>
                            <button @click="filter = 'closed'; loadConversations()" type="button" class="flex-1 rounded-lg px-2 py-1.5 text-xs font-semibold transition" :class="filter === 'closed' ? 'bg-slate-600 text-white' : 'bg-white/5 text-slate-400 hover:bg-white/10'">{{ __('Closed') }}</button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        <template x-for="c in filteredConversations" :key="c.id">
                            <button @click="openConversation(c.id)" type="button"
                                class="flex w-full items-center gap-3 border-b border-white/5 px-3 py-3 text-start transition hover:bg-white/5"
                                :class="selected === c.id ? 'bg-brand-600/15' : ''">
                                <span class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-sm font-bold"
                                    :class="c.unread_count > 0 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-brand-500/15 text-brand-300'">
                                    <span x-text="(c.customer_name || c.customer_phone).charAt(0).toUpperCase()"></span>
                                    <span x-show="c.status === 'new' && c.unread_count > 0" class="absolute -top-0.5 -end-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white" x-text="c.unread_count"></span>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-baseline justify-between gap-2">
                                        <span class="truncate text-sm font-semibold" :class="c.unread_count > 0 ? 'text-white' : 'text-slate-300'" x-text="c.customer_name || c.customer_phone"></span>
                                        <span x-show="c.platform === 'facebook'" class="inline-flex items-center gap-0.5 rounded-md bg-[#1877f2]/15 px-1.5 py-0.5 text-[9px] font-bold text-[#4da6ff]">FB</span>
                                        <span x-show="c.platform === 'instagram'" class="inline-flex items-center gap-0.5 rounded-md bg-pink-500/15 px-1.5 py-0.5 text-[9px] font-bold text-pink-400">IG</span>
                                        <span class="shrink-0 text-[10px] tabular-nums" :class="c.unread_count > 0 ? 'font-bold text-emerald-400' : 'text-slate-500'" :title="c.last_message_raw ? fullTime(c.last_message_raw) : ''" x-text="c.last_message_raw ? lastTimeLabel(c.last_message_raw) : ''"></span>
                                    </span>
                                    <span class="flex items-center justify-between gap-2">
                                        <span class="truncate text-xs" :class="c.unread_count > 0 ? 'font-medium text-emerald-200' : 'text-slate-500'" x-text="c.customer_phone"></span>
                                        <span class="shrink-0 rounded-md px-1.5 py-0.5 text-[9px] font-bold"
                                            :class="c.status === 'closed' ? 'bg-slate-600/40 text-slate-300' : (c.status === 'assigned' ? 'bg-brand-500/15 text-brand-300' : 'bg-amber-500/15 text-amber-300')"
                                            x-text="statusLabel(c.status)"></span>
                                    </span>
                                    <template x-if="c.assigned_name">
                                        <span class="mt-0.5 block truncate text-[10px] text-slate-500">👤 <span x-text="c.assigned_name"></span></span>
                                    </template>
                                </span>
                            </button>
                        </template>
                        <div x-show="filteredConversations.length === 0" class="flex flex-col items-center gap-2 py-14 text-center">
                            <svg class="h-10 w-10 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <p class="text-sm text-slate-500">{{ __('No conversations yet.') }}</p>
                            <template x-if="canReply">
                                <button @click="openStartModal = true" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">{{ __('Start a new conversation') }}</button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- ===== Chat window ===== --}}
                <div class="flex min-w-0 flex-1 flex-col" :class="selected ? 'flex' : 'hidden lg:flex'">
                    <template x-if="current">
                        <div class="flex h-full flex-col">
                            {{-- Chat header --}}
                            <div class="flex items-center gap-3 border-b border-white/10 bg-slate-950/30 px-3 py-2.5">
                                <button @click="selected = null; messages = []" type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-white/10 lg:hidden">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-sm font-bold text-emerald-300" x-text="(current.customer_name || current.customer_phone).charAt(0).toUpperCase()"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-white" x-text="current.customer_name || current.customer_phone"></p>
                                    <p class="truncate text-[11px] text-slate-500"><span x-show="current.platform === 'facebook'" class="inline-flex items-center gap-1 rounded-md bg-[#1877f2]/15 px-1.5 py-0.5 text-[9px] font-bold text-[#4da6ff]">Facebook Messenger</span> <span x-show="current.platform === 'instagram'" class="inline-flex items-center gap-1 rounded-md bg-pink-500/15 px-1.5 py-0.5 text-[9px] font-bold text-pink-400">Instagram DM</span> <span x-show="!current.platform || current.platform === 'whatsapp'" x-text="current.customer_phone"></span><span x-text="(current.budget ? ' · ' + '{{ __('Budget') }}' + ': EGP ' + Number(current.budget).toLocaleString() : '') + (current.assigned_name ? ' · ' + current.assigned_name : '')"></span></p>
                                </div>

                                {{-- CRM links --}}
                                <div class="hidden items-center gap-1.5 md:flex">
                                    {{-- Existing lead (by phone): status badge + direct edit --}}
                                    <template x-if="existingLead">
                                        <button @click="linkExistingLead(current, existingLead, existingCustomer)" type="button"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500/25 bg-emerald-500/10 px-2 py-1 text-[10px] font-bold text-emerald-300 transition hover:bg-emerald-500/20"
                                            :title="current.linked_lead_id ? '{{ __('Open linked lead') }}' : '{{ __('Link lead') }}'">
                                            <span class="h-1.5 w-1.5 rounded-full" :class="stageColor(existingLead.stage)"></span>
                                            <span x-text="existingLead.stage_label || '{{ __('Lead') }}'"></span>
                                        </button>
                                        <a :href="'/real-statement-control/crm/leads/' + existingLead.id + '/edit'" class="inline-flex items-center gap-1 rounded-lg border border-white/10 bg-white/5 px-2 py-1 text-[10px] font-bold text-slate-200 hover:bg-white/10" :title="'{{ __('Edit lead') }}: ' + existingLead.name">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9" stroke-linecap="round"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ __('Edit lead') }}
                                        </a>
                                    </template>

                                    {{-- Existing customer only (no lead): direct edit --}}
                                    <template x-if="!existingLead && existingCustomer">
                                        <a :href="'/real-statement-control/crm/customers/' + existingCustomer.id + '/edit'" class="inline-flex items-center gap-1 rounded-lg bg-brand-500/15 px-2 py-1 text-[10px] font-bold text-brand-300 hover:bg-brand-500/25">
                                            {{ __('Customer') }} · <span x-text="existingCustomer.name || ''"></span>
                                        </a>
                                    </template>

                                    {{-- Linked to a different lead than the phone's own --}}
                                    <template x-if="current.linked_lead_id && !existingLead">
                                        <a :href="'/real-statement-control/crm/leads/' + current.linked_lead_id + '/edit'" class="inline-flex items-center gap-1 rounded-lg bg-emerald-500/15 px-2 py-1 text-[10px] font-bold text-emerald-300 hover:bg-emerald-500/25">
                                            {{ __('Linked lead') }} · <span x-text="current.linked_lead_name || ''"></span>
                                        </a>
                                    </template>
                                    <template x-if="current.linked_customer_id">
                                        <a :href="'/real-statement-control/crm/customers/' + current.linked_customer_id + '/edit'" class="inline-flex items-center gap-1 rounded-lg bg-brand-500/15 px-2 py-1 text-[10px] font-bold text-brand-300 hover:bg-brand-500/25">
                                            {{ __('Linked customer') }}
                                        </a>
                                    </template>

                                    {{-- Existing lead: open profile; new number: create lead --}}
                                    <template x-if="existingLead">
                                        <a :href="'{{ url('/real-statement-control/crm/leads') }}/' + existingLead.id" class="inline-flex items-center gap-1 rounded-lg border border-brand-500/20 bg-brand-500/10 px-2 py-1 text-[10px] font-bold text-brand-300 hover:bg-brand-500/20">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M10 14 21 3M18 13v5a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6a3 3 0 0 1 3-3h5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ __('Open lead profile') }}
                                        </a>
                                    </template>
                                    <template x-if="!existingLead && canCreateLeads">
                                        <button @click="showLeadModal()" type="button" class="inline-flex items-center gap-1 rounded-lg border border-white/10 bg-white/5 px-2 py-1 text-[10px] font-bold text-slate-200 hover:bg-white/10">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-linecap="round"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6" stroke-linecap="round"/></svg>
                                            {{ __('Create lead') }}
                                        </button>
                                    </template>
                                </div>

                                {{-- Claim (unassigned, managers only) --}}
                                <template x-if="!current.assigned_to && canManage">
                                    <button @click="claimConversation(current)" type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-3 py-1.5 text-xs font-bold text-emerald-300 transition hover:bg-emerald-500/20">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z" stroke-linejoin="round"/></svg>
                                        {{ __('Claim') }}
                                    </button>
                                </template>

                                {{-- Assign (manager) --}}
                                <template x-if="canManage">
                                    <select x-model="current.assigned_to" @change="assignConversation(current)" class="hidden w-40 rounded-xl border border-white/10 bg-slate-950/60 px-2 py-1.5 text-xs font-semibold text-slate-200 focus:border-brand-500 focus:outline-none sm:block">
                                        <option value="">— {{ __('Assign to') }} —</option>
                                        <template x-for="u in salesUsers" :key="u.id">
                                            <option :value="u.id" x-text="u.name"></option>
                                        </template>
                                    </select>
                                </template>

                                <button @click="toggleStatus(current)" type="button"
                                    class="rounded-xl px-3 py-1.5 text-xs font-bold transition"
                                    :class="current.status === 'closed' ? 'bg-emerald-500 text-white hover:bg-emerald-400' : 'bg-white/5 text-slate-300 hover:bg-white/10'"
                                    x-text="current.status === 'closed' ? '{{ __('Reopen') }}' : '{{ __('Close') }}'"></button>

                                <button @click="openRenameModal()" type="button" class="rounded-xl p-2 text-slate-400 transition hover:bg-white/10 hover:text-sky-300" title="{{ __('Rename customer') }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9" stroke-linecap="round"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <template x-if="canManage">
                                    <button @click="deleteConversation()" type="button" class="rounded-xl p-2 text-slate-400 transition hover:bg-rose-500/10 hover:text-rose-400" title="{{ __('Delete conversation') }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-linecap="round"/></svg>
                                    </button>
                                </template>
                            </div>

                            {{-- Messages --}}
                            <div class="chat-bg relative flex-1 overflow-y-auto px-3 py-4 sm:px-5" x-ref="messagesBox" @scroll="onMessagesScroll" :style="'background-color:' + chatBackground">
                                <div class="space-y-2">
                                    <template x-for="m in messages" :key="m.id">
                                        <div class="flex" :class="m.direction === 'outgoing' ? 'justify-start' : 'justify-end'">
                                            <div class="relative max-w-[85%] rounded-2xl px-3.5 py-2 text-sm leading-relaxed shadow sm:max-w-[70%]"
                                                :class="m.direction === 'outgoing' ? 'rounded-ss-md' : 'rounded-se-md'"
                                                :style="bubbleStyle(m)">
                                                <template x-if="m.message_type === 'document'">
                                                    <div class="mb-1.5 flex items-center gap-3 rounded-xl bg-white/10 p-2.5">
                                                        <a :href="'/real-statement-control/whatsapp/media/' + m.id" target="_blank" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-500/20 text-rose-300 transition hover:bg-rose-500/30" title="{{ __('Download') }}">
                                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6M12 18v-6M9 15l3 3 3-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                        </a>
                                                        <div class="min-w-0">
                                                            <p class="truncate text-xs font-bold" x-text="m.media_name || 'document.pdf'"></p>
                                                            <p class="text-[10px] opacity-80">{{ __('Payment plan PDF') }}</p>
                                                        </div>
                                                    </div>
                                                </template>
                                                <p class="whitespace-pre-wrap break-words" x-text="m.body"></p>
                                                <template x-if="m.reactions && m.reactions.length">
                                                    <div class="mt-1 flex flex-wrap items-center gap-1">
                                                        <template x-for="reaction in m.reactions" :key="reaction.direction + reaction.created_at">
                                                            <span class="rounded-full border border-white/15 bg-black/20 px-1.5 py-0.5 text-sm leading-none" :title="reaction.direction === 'outgoing' ? '{{ __('Reaction from the team') }}' : '{{ __('Reaction from customer') }}'" x-text="reaction.emoji"></span>
                                                        </template>
                                                    </div>
                                                </template>
                                                <div class="mt-1 flex items-center justify-end gap-1.5 text-[10px]"
                                                    :class="m.direction === 'outgoing' ? 'text-emerald-100/70' : 'text-slate-400'">
                                                    <span x-text="m.created_at"></span>
                                                    <template x-if="m.direction === 'outgoing'">
                                                        <span>
                                                            <template x-if="m.delivery_status === 'failed'">
                                                                <span class="font-bold text-rose-300" title="{{ __('Not delivered — gateway issue') }}">✕ {{ __('failed') }}</span>
                                                            </template>
                                                            <template x-if="m.delivery_status !== 'failed'">
                                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                                            </template>
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <div x-show="messages.length === 0" class="py-10 text-center text-sm text-slate-500">
                                        {{ __('No messages yet — say hello!') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Composer --}}
                            <div class="border-t border-white/10 bg-slate-950/30 p-2.5 sm:p-3">
                                <div class="flex items-end gap-2">
                                    <div class="relative flex-1">
                                        <textarea x-model="draft" @keydown="handleComposerKeydown($event)" rows="1" x-ref="composer" @input="autoGrow($el)"
                                            class="max-h-36 w-full resize-none rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none"
                                            placeholder="{{ __('Type a message...') }}"></textarea>
                                        <div class="absolute bottom-2 end-2 flex items-center gap-1">
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" type="button" class="rounded-xl p-1.5 text-slate-400 transition hover:bg-white/10 hover:text-rose-300" title="{{ __('Send payment plan PDF') }}">
                                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6M12 18v-6M9 15l3 3 3-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                                <div x-show="open" @click.outside="open = false" x-transition x-cloak class="absolute bottom-10 end-0 z-20 w-72 rounded-2xl border border-white/10 bg-slate-900 p-2 shadow-2xl">
                                                    <p class="px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ __('Payment plans') }}</p>
                                                    <div class="max-h-56 overflow-y-auto">
                                                        <template x-for="p in plans" :key="p.id">
                                                            <button @click="sendPlanPdf(p); open = false" type="button" class="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-start hover:bg-white/10">
                                                                <svg class="h-4 w-4 shrink-0 text-rose-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                                <span class="min-w-0">
                                                                    <span class="block truncate text-xs font-semibold text-slate-200" x-text="p.name"></span>
                                                                    <span class="block truncate text-[10px] text-slate-500" x-text="(p.customer_name || '') + (p.unit ? ' · ' + p.unit : '') + (p.project ? ' · ' + p.project : '')"></span>
                                                                </span>
                                                            </button>
                                                        </template>
                                                        <template x-if="plans.length === 0">
                                                            <p class="px-2 py-2 text-xs text-slate-500">{{ __('No plans saved yet — create one in the calculator first.') }}</p>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" type="button" class="rounded-xl p-1.5 text-slate-400 transition hover:bg-white/10 hover:text-emerald-300" title="{{ __('Quick templates') }}">
                                                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                </button>
                                                <div x-show="open" @click.outside="open = false" x-transition x-cloak class="absolute bottom-10 end-0 z-20 w-64 rounded-2xl border border-white/10 bg-slate-900 p-2 shadow-2xl">
                                                    <p class="px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ __('Quick replies') }}</p>
                                                    <template x-for="t in templates" :key="t.id">
                                                        <button @click="draft = t.body; open = false; $nextTick(() => $refs.composer.focus())" type="button" class="block w-full truncate rounded-lg px-2 py-1.5 text-start text-xs text-slate-300 hover:bg-white/10" x-text="t.name"></button>
                                                    </template>
                                                    <template x-if="templates.length === 0">
                                                        <p class="px-2 py-1 text-xs text-slate-500">{{ __('No templates — add some in Templates.') }}</p>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button @click="sendMessage()" type="button"
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-500/25 transition hover:bg-emerald-400 disabled:opacity-40"
                                        :disabled="!draft.trim() || sending" title="{{ __('Send') }}">
                                        <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="!current" class="flex flex-1 flex-col items-center justify-center gap-3 text-center">
                        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-400/60">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                        </span>
                        <p class="text-sm text-slate-400">{{ __('Select a conversation to start chatting.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- New conversation modal --}}
        <div x-show="openStartModal" x-cloak x-transition.opacity class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm" @click.self="openStartModal = false">
            <div class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-900 p-6 shadow-2xl" x-show="openStartModal" x-transition.scale.origin.center>
                <h3 class="text-lg font-bold text-white">{{ __('New conversation') }}</h3>
                <p class="mt-1 text-xs text-slate-400">{{ __('Start an outbound WhatsApp conversation with a customer.') }}</p>
                <div class="mt-5 space-y-4">
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-semibold text-slate-300">{{ __('Phone number') }}</span>
                        <input x-model="startPhone" type="text" placeholder="01012345678" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-semibold text-slate-300">{{ __('Customer name (optional)') }}</span>
                        <input x-model="startName" type="text" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none">
                    </label>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button @click="openStartModal = false" class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-300 hover:bg-white/5">{{ __('Cancel') }}</button>
                    <button @click="startConversation()" :disabled="!startPhone.trim()" class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-400 disabled:opacity-40">{{ __('Start') }}</button>
                </div>
            </div>
        </div>

        {{-- Create lead modal --}}
        <div x-show="openLeadModal" x-cloak x-transition.opacity class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm" @click.self="openLeadModal = false">
            <div class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-900 p-6 shadow-2xl" x-show="openLeadModal" x-transition.scale.origin.center>
                <h3 class="text-lg font-bold text-white">{{ __('Create lead from conversation') }}</h3>
                <p class="mt-1 text-xs text-slate-400">{{ __('The lead will be linked to this conversation and appears in the CRM.') }}</p>
                <div class="mt-5 space-y-4">
                    <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2.5 text-sm">
                        <span class="text-slate-400">{{ __('Phone number') }}:</span>
                        <span class="font-semibold text-white" x-text="current?.customer_phone || ''"></span>
                    </div>
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-semibold text-slate-300">{{ __('Name') }}</span>
                        <input x-model="leadName" type="text" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-semibold text-slate-300">{{ __('Budget (EGP)') }}</span>
                        <input x-model="leadBudget" type="number" min="0" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none">
                    </label>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button @click="openLeadModal = false" class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-300 hover:bg-white/5">{{ __('Cancel') }}</button>
                    <button @click="createLead()" class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-400">{{ __('Create & link') }}</button>
                </div>
            </div>
        </div>

        {{-- Rename customer modal --}}
        <div x-show="renameModal" x-cloak x-transition.opacity class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm" @click.self="renameModal = false">
            <div class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-900 p-6 shadow-2xl" x-show="renameModal" x-transition.scale.origin.center>
                <h3 class="text-lg font-bold text-white">{{ __('Rename customer') }}</h3>
                <p class="mt-1 text-xs text-slate-400">{{ __('Change the display name of this conversation.') }}</p>
                <div class="mt-5">
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-semibold text-slate-300">{{ __('Customer name') }}</span>
                        <input x-model="renameName" type="text" maxlength="255" class="w-full rounded-xl border border-white/10 bg-slate-950/60 px-3 py-2.5 text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none" @keydown.enter.prevent="saveRename()">
                    </label>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button @click="renameModal = false" class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-300 hover:bg-white/5">{{ __('Cancel') }}</button>
                    <button @click="saveRename()" :disabled="!renameName.trim()" class="rounded-xl bg-emerald-500 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-400 disabled:opacity-40">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        .chat-bg {
            background-color: rgb(10 18 34 / 0.35);
            background-image:
                radial-gradient(circle at 15% 20%, rgb(16 185 129 / 0.05), transparent 45%),
                radial-gradient(circle at 85% 80%, rgb(99 102 241 / 0.05), transparent 45%);
        }
        .h-4\.5 { height: 1.125rem; }
        .w-4\.5 { width: 1.125rem; }
    </style>

    <script>
        function whatsappPanel() {
            return {
                conversations: @json($conversationsJson, JSON_UNESCAPED_UNICODE),
                templates: @json($templatesJson, JSON_UNESCAPED_UNICODE),
                plans: @json($plansJson, JSON_UNESCAPED_UNICODE),
                salesUsers: @json($salesUsersJson, JSON_UNESCAPED_UNICODE),
                existingLeads: @json($existingLeadsJson, JSON_UNESCAPED_UNICODE),
                existingCustomers: @json($existingCustomersJson, JSON_UNESCAPED_UNICODE),
                canManage: @json($canManage),
                canReply: @json($canReply),
                evolutionConfigured: @json($evolutionConfigured),
                connectionOpen: @json($connectionOpen),
                outgoingColor: @json($outgoingColor),
                incomingColor: @json($incomingColor),
                chatBackground: @json($chatBackground),
                openPhone: @json($openPhone),
                openName: @json($openName),
                canCreateLeads: @json($canCreateLeads),

                selected: null,
                messages: [],
                filter: 'all',
                platformFilter: 'all',
                search: '',
                draft: '',
                sending: false,
                openStartModal: false,
                openLeadModal: false,
                renameModal: false,
                renameName: '',
                startPhone: '',
                startName: '',
                leadName: '',
                leadBudget: '',
                pollTimer: null,
                msgPollTimer: null,
                knownState: {},          // id -> { last_message_raw, unread_count }
                lastAlertTs: {},         // id -> timestamp of last alert (cooldown)
                originalTitle: document.title,
                flashTimer: null,
                audioCtx: null,
                soundOn: localStorage.getItem('waSound') !== '0',

                get current() {
                    return this.conversations.find(c => c.id === this.selected) || null;
                },

                get filteredConversations() {
                    const q = this.search.trim().toLowerCase();
                    return this.conversations.filter(c =>
                        (!q || (c.customer_name || '').toLowerCase().includes(q) || (c.customer_phone || '').includes(q)) && (this.platformFilter === 'all' || c.platform === this.platformFilter)
                    );
                },

                get existingLead() {
                    return (this.existingLeads[this.current?.customer_phone || ''] || [])[0] || null;
                },

                get existingCustomer() {
                    return (this.existingCustomers[this.current?.customer_phone || ''] || [])[0] || null;
                },

                init() {
                    this.sortConversations();
                    this.snapshotState(this.conversations);
                    this.pollTimer = setInterval(() => this.loadConversations(true), 5000);
                    if (this.openPhone) {
                        this.openPhoneConversation();
                    }
                    // Browsers block autoplay audio until the user interacts —
                    // unlock the AudioContext on the first click anywhere.
                    document.addEventListener('click', () => this.unlockAudio(), { once: true });
                },

                unlockAudio() {
                    try {
                        const AC = window.AudioContext || window.webkitAudioContext;
                        if (!AC) return;
                        if (!this.audioCtx) this.audioCtx = new AC();
                        if (this.audioCtx.state === 'suspended') this.audioCtx.resume();
                    } catch (e) { /* audio not supported — skip */ }
                },

                toggleSound() {
                    this.soundOn = !this.soundOn;
                    localStorage.setItem('waSound', this.soundOn ? '1' : '0');
                    if (this.soundOn) this.unlockAudio();
                },

                snapshotState(list) {
                    list.forEach(c => { this.knownState[c.id] = { last_message_raw: c.last_message_raw || '', unread_count: c.unread_count || 0 }; });
                },

                detectNewIncoming(list) {
                    const now = Date.now();
                    let hasNew = false;
                    list.forEach(c => {
                        const prev = this.knownState[c.id];
                        const cur = { last_message_raw: c.last_message_raw || '', unread_count: c.unread_count || 0 };
                        if (!prev) {
                            this.knownState[c.id] = cur;
                            if (cur.unread_count > 0 && this.selected !== c.id) { hasNew = true; this.lastAlertTs[c.id] = now; }
                            return;
                        }
                        const newMessage = cur.last_message_raw !== prev.last_message_raw;
                        const unreadGrew = cur.unread_count > prev.unread_count;
                        if ((newMessage || unreadGrew) && this.selected !== c.id) {
                            const last = this.lastAlertTs[c.id] || 0;
                            if (now - last > 60000) { hasNew = true; this.lastAlertTs[c.id] = now; }
                        }
                        this.knownState[c.id] = cur;
                    });
                    if (hasNew) this.alertNewMessage();
                },

                playAlertSound() {
                    if (!this.soundOn) return;
                    try {
                        const AC = window.AudioContext || window.webkitAudioContext;
                        if (!AC) return;
                        if (!this.audioCtx) this.audioCtx = new AC();
                        if (this.audioCtx.state === 'suspended') this.audioCtx.resume();
                        const now = this.audioCtx.currentTime;
                        const notes = [[880, 0], [1174.66, 0.18]];
                        notes.forEach(([freq, offset]) => {
                            const osc = this.audioCtx.createOscillator();
                            const gain = this.audioCtx.createGain();
                            osc.type = 'sine';
                            osc.frequency.value = freq;
                            gain.gain.setValueAtTime(0.0001, now + offset);
                            gain.gain.exponentialRampToValueAtTime(0.2, now + offset + 0.03);
                            gain.gain.exponentialRampToValueAtTime(0.0001, now + offset + 0.3);
                            osc.connect(gain);
                            gain.connect(this.audioCtx.destination);
                            osc.start(now + offset);
                            osc.stop(now + offset + 0.35);
                        });
                    } catch (e) { /* audio not supported — skip */ }
                },

                flashTab() {
                    if (this.flashTimer) clearInterval(this.flashTimer);
                    const base = this.originalTitle || '{{ __('WhatsApp Panel') }}';
                    let count = 0;
                    const msg = '🔔 {{ __('New message') }}';
                    this.flashTimer = setInterval(() => {
                        count++;
                        if (count > 12) { clearInterval(this.flashTimer); this.flashTimer = null; document.title = base; return; }
                        document.title = (count % 2 === 1) ? msg + ' — ' + base : base;
                    }, 900);
                },

                alertNewMessage() {
                    if (!document.hidden) { this.playAlertSound(); return; }
                    this.playAlertSound();
                    this.flashTab();
                },

                sortConversations() {
                    this.conversations.sort((a, b) => (b.last_message_raw || '').localeCompare(a.last_message_raw || ''));
                },

                async loadConversations(silent = false) {
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations?status=' + this.filter, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.conversations = data.conversations;
                        this.sortConversations();
                        this.detectNewIncoming(this.conversations);
                    } catch (e) {
                        if (!silent) window.toast.danger('{{ __('Failed to load conversations.') }}');
                    }
                },

                async openConversation(id) {
                    this.selected = id;
                    this.messages = [];
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + id + '/messages', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.messages = data.messages;
                        const conv = this.conversations.find(c => c.id === id);
                        if (conv) {
                            conv.unread_count = 0;
                            Object.assign(conv, data.conversation);
                        }
                        this.scrollToBottom(true);
                        if (this.msgPollTimer) clearInterval(this.msgPollTimer);
                        this.msgPollTimer = setInterval(() => this.refreshMessages(), 5000);
                    } catch (e) {
                        window.toast.danger('{{ __('Failed to load messages.') }}');
                    }
                },

                async refreshMessages() {
                    if (!this.selected) return;
                    const id = this.selected;
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + id + '/messages', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.messages = data.messages;
                        const conv = this.conversations.find(c => c.id === id);
                        if (conv) {
                            conv.unread_count = 0;
                            Object.assign(conv, data.conversation);
                        }
                        this.detectNewIncoming(this.conversations);
                    } catch (e) { /* silent */ }
                },

                async sendMessage() {
                    const body = this.draft.trim();
                    if (!body || !this.selected || this.sending) return;
                    this.sending = true;
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + this.selected + '/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ body })
                        });
                        const data = await res.json();
                        if (!data.success) {
                            window.toast.danger(data.message || '{{ __('Failed to send.') }}');
                        } else {
                            this.draft = '';
                            this.messages.push({
                                id: data.msg.id,
                                direction: 'outgoing',
                                body,
                                message_type: 'text',
                                delivery_status: data.msg.delivery_status,
                                sender_name: null,
                                created_at: data.msg.created_at || new Date().toISOString().slice(0, 16).replace('T', ' ')
                            });
                            if (!data.sent) window.toast.warning(data.message);
                            this.scrollToBottom();
                            this.loadConversations(true);
                        }
                    } catch (e) {
                        window.toast.danger('{{ __('Failed to send.') }}');
                    } finally {
                        this.sending = false;
                        if (this.$refs.composer) this.$refs.composer.focus();
                    }
                },

                async sendPlanPdf(plan) {
                    if (!plan || !this.selected || this.sending) return;
                    this.sending = true;
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + this.selected + '/send-plan', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ plan_id: plan.id })
                        });
                        const data = await res.json();
                        if (!data.success) {
                            window.toast.danger(data.message || '{{ __('Failed to send PDF.') }}');
                        } else {
                            this.messages.push({
                                id: data.msg.id,
                                direction: 'outgoing',
                                body: data.msg.body || '',
                                message_type: 'document',
                                media_name: data.msg.media_name,
                                media_path: data.msg.media_path,
                                delivery_status: data.msg.delivery_status,
                                sender_name: null,
                                created_at: data.msg.created_at || new Date().toISOString().slice(0, 16).replace('T', ' ')
                            });
                            if (!data.sent) window.toast.warning(data.message);
                            else window.toast.success(data.message);
                            this.scrollToBottom();
                            this.loadConversations(true);
                        }
                    } catch (e) {
                        window.toast.danger('{{ __('Failed to send PDF.') }}');
                    } finally {
                        this.sending = false;
                    }
                },

                async claimConversation(conv) {
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + conv.id + '/claim', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: '{}'
                        });
                        const data = await res.json();
                        if (data.success) {
                            Object.assign(conv, data.conversation);
                            window.toast.success('{{ __('Conversation claimed — follow up with this customer.') }}');
                        } else {
                            window.toast.danger(data.message || '{{ __('Failed to claim conversation.') }}');
                        }
                    } catch (e) {
                        window.toast.danger('{{ __('Failed to claim conversation.') }}');
                    }
                },

                async assignConversation(conv) {
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + conv.id + '/assign', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ user_id: conv.assigned_to || null })
                        });
                        const data = await res.json();
                        if (data.success) {
                            Object.assign(conv, data.conversation);
                            window.toast.success('{{ __('Conversation assigned.') }}');
                        } else {
                            window.toast.danger(data.message || '{{ __('Failed to assign.') }}');
                        }
                    } catch (e) {
                        window.toast.danger('{{ __('Failed to assign.') }}');
                    }
                },

                async toggleStatus(conv) {
                    const newStatus = conv.status === 'closed' ? 'assigned' : 'closed';
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + conv.id + '/status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ status: newStatus })
                        });
                        const data = await res.json();
                        if (data.success) Object.assign(conv, data.conversation);
                    } catch (e) { /* silent */ }
                },

                async linkExistingLead(conv, leadId) {
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + conv.id + '/link', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ linked_lead_id: leadId })
                        });
                        const data = await res.json();
                        if (data.success) {
                            Object.assign(conv, data.conversation);
                            window.toast.success('{{ __('Lead linked.') }}');
                        }
                    } catch (e) { /* silent */ }
                },

                showLeadModal() {
                    this.leadName = this.current?.customer_name || '';
                    this.leadBudget = '';
                    this.openLeadModal = true;
                },

                async createLead() {
                    if (!this.current) return;
                    const conv = this.current;
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + conv.id + '/lead', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ name: this.leadName, budget: this.leadBudget })
                        });
                        const data = await res.json();
                        if (data.success) {
                            conv.linked_lead_id = data.lead_id;
                            this.openLeadModal = false;
                            this.leadName = '';
                            this.leadBudget = '';
                            window.toast.success(data.message);
                        } else {
                            window.toast.danger(data.message || '{{ __('Failed to create lead.') }}');
                        }
                    } catch (e) {
                        window.toast.danger('{{ __('Failed to create lead.') }}');
                    }
                },

                // Opened from a customer/lead "WhatsApp" button: open the
                // existing conversation for that number, or if it was never
                // chatted before, create it. A CRM lead stored for this number
                // is linked automatically (name + budget come from CRM); a
                // brand-new customer gets the "create lead from conversation"
                // screen with the phone pre-filled from the conversation.
                async openPhoneConversation() {
                    const phone = this.openPhone;
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations?search=' + encodeURIComponent(phone), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        const normalized = phone.replace(/^0/, '20');
                        const existing = data.conversations.find(c =>
                            (c.customer_phone || '').includes(phone) || (c.customer_phone || '').includes(normalized)
                        );
                        const lead = (this.existingLeads[normalized] || [])[0];
                        const customer = (this.existingCustomers[normalized] || [])[0];
                        if (existing) {
                            this.openConversation(existing.id);
                            if (lead && !existing.linked_lead_id) this.linkExistingLead(existing, lead, customer);
                            return;
                        }
                        // No previous chat — create the conversation.
                        this.startPhone = phone;
                        this.startName = this.openName || '';
                        await this.startConversation();
                        // Lead already stored in CRM → auto-link name + budget,
                        // no re-entry screen.
                        if (lead) {
                            this.linkExistingLead(this.current, lead, customer);
                            return;
                        }
                        // Brand-new customer from WhatsApp → show the lead
                        // creation screen (phone + name pre-filled).
                        if (this.canCreateLeads) this.showLeadModal();
                    } catch (e) {
                        this.startPhone = phone;
                        this.startName = this.openName || '';
                        this.startConversation();
                    }
                },

                async linkExistingLead(conv, lead, customer) {
                    if (!conv) return;
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + conv.id + '/link', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                linked_lead_id: lead.id,
                                linked_customer_id: customer ? customer.id : null
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            const idx = this.conversations.findIndex(c => c.id === conv.id);
                            if (idx !== -1) this.conversations[idx] = data.conversation;
                            window.toast.success('{{ __('Linked to existing lead.') }}');
                        }
                    } catch (e) { /* keep the conversation as-is */ }
                },

                async startConversation() {
                    if (!this.startPhone.trim()) return;
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/start', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ phone: this.startPhone, name: this.startName })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.openStartModal = false;
                            this.startPhone = '';
                            this.startName = '';
                            this.conversations.unshift(data.conversation);
                            this.openConversation(data.conversation.id);
                            window.toast.success('{{ __('Conversation started.') }}');
                        } else {
                            window.toast.danger(data.message || '{{ __('Failed to start conversation.') }}');
                        }
                    } catch (e) {
                        window.toast.danger('{{ __('Failed to start conversation.') }}');
                    }
                },

                openRenameModal() {
                    this.renameName = (this.current?.customer_name || this.current?.customer_phone || '');
                    this.renameModal = true;
                },

                async saveRename() {
                    if (!this.current || !this.renameName.trim()) return;
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + this.current.id + '/rename', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ name: this.renameName.trim() })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.renameModal = false;
                            const conv = this.conversations.find(c => c.id === this.current.id);
                            if (conv) conv.customer_name = data.conversation.customer_name;
                            window.toast.success('{{ __('Customer renamed.') }}');
                        } else {
                            window.toast.danger(data.message || '{{ __('Failed to rename customer.') }}');
                        }
                    } catch (e) {
                        window.toast.danger('{{ __('Failed to rename customer.') }}');
                    }
                },

                async deleteConversation() {
                    if (!this.current) return;
                    if (!confirm('{{ __('Delete this conversation? It moves to the trash and can be restored.') }}')) return;
                    const id = this.current.id;
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/conversations/' + id, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.conversations = this.conversations.filter(c => c.id !== id);
                            this.selected = null;
                            this.messages = [];
                            window.toast.success('{{ __('Conversation moved to trash.') }}');
                        } else {
                            window.toast.danger(data.message || '{{ __('Failed to delete conversation.') }}');
                        }
                    } catch (e) {
                        window.toast.danger('{{ __('Failed to delete conversation.') }}');
                    }
                },

                async registerWebhook() {
                    try {
                        const res = await fetch('/real-statement-control/whatsapp/webhook/register', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: '{}'
                        });
                        const data = await res.json();
                        (data.success ? window.toast.success(data.message) : window.toast.danger(data.message));
                    } catch (e) {
                        window.toast.danger('{{ __('Failed to register webhook.') }}');
                    }
                },

                scrollToBottom(instant = false) {
                    this.$nextTick(() => {
                        const box = this.$refs.messagesBox;
                        if (box) box.scrollTop = box.scrollHeight;
                    });
                },

                onMessagesScroll(e) {
                    // Keep polling while scrolled (no special handling for v1).
                },

                handleComposerKeydown(event) {
                    if (event.key !== 'Enter' || event.shiftKey || event.ctrlKey || event.metaKey || event.altKey) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();
                    this.sendMessage();
                },

                autoGrow(el) {
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 144) + 'px';
                },

                timeAgo(raw) {
                    if (!raw) return '';
                    const d = new Date(raw.replace(' ', 'T'));
                    const s = Math.floor((Date.now() - d.getTime()) / 1000);
                    if (s < 60) return '{{ __('now') }}';
                    if (s < 3600) return Math.floor(s / 60) + '{{ __('m') }}';
                    if (s < 86400) return Math.floor(s / 3600) + '{{ __('h') }}';
                    return Math.floor(s / 86400) + '{{ __('d') }}';
                },

                fullTime(raw) {
                    if (!raw) return '';
                    const d = new Date(raw.replace(' ', 'T'));
                    return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0')
                        + ' ' + String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0')
                        + '/' + d.getFullYear();
                },

                lastTimeLabel(raw) {
                    if (!raw) return '';
                    const d = new Date(raw.replace(' ', 'T'));
                    const now = new Date();
                    // Today: show the exact time (HH:MM) of the last message.
                    if (d.toDateString() === now.toDateString()) {
                        return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
                    }
                    // Within this week: show the weekday.
                    if (d.getTime() > now.getTime() - 7 * 86400000) {
                        return d.toLocaleDateString('en-US', { weekday: 'short' });
                    }
                    // Older: show the date.
                    return String(d.getDate()).padStart(2, '0') + '/' + String(d.getMonth() + 1).padStart(2, '0');
                },

                statusLabel(status) {
                    return status === 'closed' ? '{{ __('Closed') }}' : (status === 'assigned' ? '{{ __('Assigned') }}' : '{{ __('New') }}');
                },

                stageColor(stage) {
                    const map = {
                        new: 'bg-amber-400',
                        contacted: 'bg-sky-400',
                        interested: 'bg-emerald-400',
                        meeting: 'bg-violet-400',
                        site_visit: 'bg-indigo-400',
                        negotiation: 'bg-orange-400',
                        reserved: 'bg-rose-400',
                        contract: 'bg-fuchsia-400',
                        delivered: 'bg-teal-400',
                    };
                    return map[stage] || 'bg-slate-400';
                },

                bubbleStyle(m) {
                    const bg = m.direction === 'outgoing' ? this.outgoingColor : this.incomingColor;
                    const hex = (bg || '#ffffff').replace('#', '');
                    const r = parseInt(hex.substring(0, 2), 16) || 0;
                    const g = parseInt(hex.substring(2, 4), 16) || 0;
                    const b = parseInt(hex.substring(4, 6), 16) || 0;
                    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
                    return 'background-color:' + (bg || '#ffffff') + ';color:' + (luminance > 0.6 ? '#0f172a' : '#ffffff');
                }
            };
        }
    </script>
@endpush
