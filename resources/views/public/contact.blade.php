@php($companyProfile = \App\Models\CompanyProfile::query()->first())
@php($title = __('Contact Us'))

@extends('layouts.public')

@section('content')
    <div class="space-y-16">
        {{-- Hero --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-brand-950 via-slate-900 to-slate-950 px-6 py-6 text-center shadow-2xl sm:py-8">
            <div class="pointer-events-none absolute inset-0 bg-grid opacity-30"></div>
            <div class="pointer-events-none absolute -top-40 left-1/2 h-96 w-[600px] -translate-x-1/2 rounded-full bg-brand-500/20 blur-[120px]"></div>

            <div class="relative z-10 mx-auto max-w-4xl space-y-5">
                <span class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-500/10 px-5 py-2 text-xs font-bold text-amber-300 backdrop-blur-md shadow-lg">
                    {{ __('Contact Us') }}
                </span>
                <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl leading-tight">
                    {{ __('Contact Us') }}
                </h1>
                <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-300 sm:text-base sm:leading-8">
                    {{ __('Send us your details and a sales consultant will get back to you with tailored options, payment plans, and booking steps.') }}
                </p>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Contact Form --}}
            <section class="app-card app-card--gradient space-y-6 p-6 sm:p-8">
                <h2 class="text-xl font-bold text-white">{{ __('Send an inquiry') }}</h2>

                @if (session('status'))
                    <div class="flex items-center gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('public.inquiries.store') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label for="name" class="text-sm font-medium text-slate-300">{{ __('Name') }} <span class="text-rose-400">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="app-input" placeholder="{{ __('Your full name') }}">
                        @error('name')<p class="text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <label for="phone" class="text-sm font-medium text-slate-300">{{ __('Phone') }} <span class="text-rose-400">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required class="app-input" placeholder="{{ __('Phone number') }}">
                        @error('phone')<p class="text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium text-slate-300">{{ __('Email') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="app-input" placeholder="you@example.com">
                        @error('email')<p class="text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-2">
                        <label for="message" class="text-sm font-medium text-slate-300">{{ __('Message') }}</label>
                        <textarea id="message" name="message" rows="4" class="app-input" placeholder="{{ __('Tell us what you are looking for...') }}">{{ old('message') }}</textarea>
                        @error('message')<p class="text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="app-button w-full">
                        {{ __('Send Inquiry') }}
                    </button>
                </form>
            </section>

            {{-- Location Map (beside the inquiry form) --}}
            @php($mapAddress = $companyProfile?->address ?? 'Street 12, New Cairo 1, Cairo Governorate, Egypt')
            @php($googleMapsKey = $companyProfile?->google_maps_api_key ?? config('services.google.maps_api_key'))
            @php($mapLat = '27.18573620109978')
            @php($mapLng = '31.18619253682363')
            <section class="app-card app-card--gradient space-y-4 p-6">
                <div>
                    <h2 class="text-lg font-bold text-white">{{ __('Our Location') }}</h2>
                    <p class="mt-1 text-sm text-slate-400">{{ $mapAddress }}</p>
                </div>
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-900/50">
                    @if ($googleMapsKey)
                        <iframe
                            title="{{ $mapAddress }}"
                            class="h-96 w-full"
                            loading="lazy"
                            src="https://www.google.com/maps/embed/v1/place?key={{ $googleMapsKey }}&q={{ $mapLat }},{{ $mapLng }}"
                            allowfullscreen
                        ></iframe>
                    @else
                        <iframe
                            id="contact-map"
                            title="{{ $mapAddress }}"
                            class="h-96 w-full"
                            loading="lazy"
                            src="https://www.google.com/maps?q={{ $mapLat }},{{ $mapLng }}&z=16&output=embed"
                            allowfullscreen
                        ></iframe>
                    @endif
                </div>
                <a href="https://www.google.com/maps?q={{ $mapLat }},{{ $mapLng }}" target="_blank" rel="noopener" class="app-button--ghost w-full justify-center text-center">
                    {{ __('View on Google Maps') }}
                </a>
            </section>
        </div>
    </div>
@endsection
