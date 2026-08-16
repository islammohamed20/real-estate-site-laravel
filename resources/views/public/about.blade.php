@extends('layouts.public')

@section('content')
    <div class="space-y-16">
        {{-- Hero --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-brand-950 via-slate-900 to-slate-950 px-6 py-16 text-center shadow-2xl sm:py-24">
            <div class="pointer-events-none absolute inset-0 bg-grid opacity-30"></div>
            <div class="pointer-events-none absolute -top-40 left-1/2 h-96 w-[600px] -translate-x-1/2 rounded-full bg-brand-500/20 blur-[120px]"></div>

            <div class="relative z-10 mx-auto max-w-4xl space-y-5">
                <span class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-500/10 px-5 py-2 text-xs font-bold text-amber-300 backdrop-blur-md shadow-lg">
                    {{ __('About Venecia') }}
                </span>
                <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl leading-tight">
                    {{ __('We build communities, not just properties') }}
                </h1>
                <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-300 sm:text-base sm:leading-8">
                    {{ __('Venecia Developments blends Italian-inspired architecture with flexible payment plans, creating luxury residential and commercial destinations that fit real lifestyles.') }}
                </p>
            </div>
        </section>

        {{-- Stats --}}
        <section class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="app-card app-card--gradient space-y-1 p-5 text-center">
                <p class="text-3xl font-extrabold text-brand-400">+15</p>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('Years of experience') }}</p>
            </div>
            <div class="app-card app-card--gradient space-y-1 p-5 text-center">
                <p class="text-3xl font-extrabold text-white">{{ $projectCount }}</p>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('Projects') }}</p>
            </div>
            <div class="app-card app-card--gradient space-y-1 p-5 text-center">
                <p class="text-3xl font-extrabold text-emerald-400">{{ $unitCount }}</p>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('Units') }}</p>
            </div>
            <div class="app-card app-card--gradient space-y-1 p-5 text-center">
                <p class="text-3xl font-extrabold text-amber-400">8</p>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('Years installment') }}</p>
            </div>
        </section>

        {{-- Values --}}
        <section class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['title' => __('Architectural excellence'), 'body' => __('Modern Italian design with optimal space and natural light.')],
                ['title' => __('Flexible payments'), 'body' => __('Up to 8 years installment with no-interest options tailored to buyers.')],
                ['title' => __('Prime locations'), 'body' => __('Projects in the most vibrant and connected areas.')],
                ['title' => __('Transparent pricing'), 'body' => __('Clear unit pricing, maintenance fees, and payment schedules.')],
                ['title' => __('After-sales support'), 'body' => __('A dedicated team for contracts, installments, and unit delivery.')],
                ['title' => __('Smart investment'), 'body' => __('High resale value and strong rental demand in every development.')],
            ] as $value)
                <div class="app-card app-card--gradient space-y-3 p-6">
                    <h3 class="text-lg font-bold text-white">{{ $value['title'] }}</h3>
                    <p class="text-sm leading-relaxed text-slate-400">{{ $value['body'] }}</p>
                </div>
            @endforeach
        </section>
    </div>
@endsection
