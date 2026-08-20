@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    @include('crm.partials.crm-nav')

    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)" class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>{{ session('status') }}</span>
            <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-200">✕</button>
        </div>
    @endif

    <section class="dashboard-hero-card p-6 sm:p-8">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#1877f2]/20">
                <svg class="h-6 w-6 text-[#1877f2]" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">{{ __('Facebook Messenger') }}</h1>
                <p class="text-sm text-slate-400">{{ __('Connect your Facebook page to receive and reply to messages') }}</p>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('dashboard.facebook-settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="app-card app-card--gradient p-6 sm:p-8">
            <h2 class="mb-4 text-lg font-semibold text-white">{{ __('Facebook App Settings') }}</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs text-slate-400">{{ __('Page ID') }}</label>
                    <input type="text" name="page_id" value="{{ old('page_id', $settings->page_id) }}" class="form-input w-full" placeholder="123456789012345" required>
                </div>
                <div>
                    <label class="mb-1 block text-xs text-slate-400">{{ __('Verify Token') }}</label>
                    <input type="text" name="verify_token" value="{{ old('verify_token', $settings->verify_token ?? 'venecia_fb_verify') }}" class="form-input w-full" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs text-slate-400">{{ __('Page Access Token') }}</label>
                    <input type="password" name="access_token" value="{{ old('access_token', $settings->access_token) }}" class="form-input w-full" placeholder="EAA..." required>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-xs text-slate-400">{{ __('App Secret') }} ({{ __('optional') }})</label>
                    <input type="password" name="app_secret" value="{{ old('app_secret', $settings->app_secret) }}" class="form-input w-full" placeholder="a1b2c3d4e5...">
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <label class="relative inline-flex cursor-pointer items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ $settings->is_active ? 'checked' : '' }} class="peer sr-only">
                    <div class="peer h-6 w-11 rounded-full bg-slate-700 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:bg-emerald-500 peer-checked:after:translate-x-full"></div>
                    <span class="text-sm text-white">{{ __('Activate Facebook Messenger') }}</span>
                </label>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                <button type="submit" class="app-button">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ __('Save Settings') }}
                </button>
            </div>
        </section>
    </form>

    <section class="app-card app-card--gradient p-6 sm:p-8">
        <h2 class="mb-4 text-lg font-semibold text-white">{{ __('Webhook Setup Guide') }}</h2>

        <div class="space-y-4 text-sm text-slate-300">
            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-4">
                <p class="font-semibold text-white">{{ __('Step 1: Create a Facebook App') }}</p>
                <ol class="mt-2 list-decimal space-y-1 ps-5">
                    <li>{{ __('Go to') }} <a href="https://developers.facebook.com" target="_blank" class="text-brand-400 hover:underline">developers.facebook.com</a></li>
                    <li>{{ __('Create a new App and select "Business" type') }}</li>
                    <li>{{ __('Add "Messenger" product to your app') }}</li>
                </ol>
            </div>

            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-4">
                <p class="font-semibold text-white">{{ __('Step 2: Configure Webhook') }}</p>
                <div class="mt-2 space-y-2">
                    <div>
                        <p class="text-xs text-slate-500">{{ __('Callback URL') }}</p>
                        <div class="flex items-center gap-2">
                            <code class="rounded bg-slate-800 px-2 py-1 text-xs text-emerald-300">{{ $webhookUrl }}</code>
                            <button onclick="navigator.clipboard.writeText('{{ $webhookUrl }}')" class="text-xs text-brand-400 hover:underline">Copy</button>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">{{ __('Verify Token') }}</p>
                        <div class="flex items-center gap-2">
                            <code class="rounded bg-slate-800 px-2 py-1 text-xs text-emerald-300">{{ $verifyToken }}</code>
                            <button onclick="navigator.clipboard.writeText('{{ $verifyToken }}')" class="text-xs text-brand-400 hover:underline">Copy</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-white/10 bg-slate-950/40 p-4">
                <p class="font-semibold text-white">{{ __('Step 3: Subscribe to Events') }}</p>
                <p class="mt-1">{{ __('Subscribe to: messages, messaging_postbacks, message_deliveries') }}</p>
            </div>
        </div>
    </section>
</div>
@endsection
