@extends('layouts.public')

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-brand-950 via-slate-900 to-slate-950 px-6 py-12 text-center shadow-2xl sm:py-20 lg:py-24">
        <div class="pointer-events-none absolute inset-0 bg-grid opacity-30"></div>
        <div class="pointer-events-none absolute -top-40 left-1/2 h-96 w-[600px] -translate-x-1/2 rounded-full bg-brand-500/20 blur-[120px]"></div>

        <div class="relative z-10 mx-auto max-w-4xl space-y-6">
            {{-- Brand Badge --}}
            <span class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-500/10 px-5 py-2 text-xs font-bold text-amber-300 backdrop-blur-md shadow-lg">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg> {{ __('شركة فينسيا للاستثمار والتطوير العقاري · Venecia Developments') }}
            </span>

            {{-- Main Headline --}}
            <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl leading-tight">
                {{ __('ابتكار العمران.. وصناعة مجتمعات') }}
                <span class="bg-gradient-to-r from-brand-400 via-indigo-300 to-amber-300 bg-clip-text text-transparent">
                    {{ __('سكنية فاخرة') }}
                </span>
                {{ __('بمواصفات عالمية') }}
            </h1>

            {{-- Subtitle --}}
            <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-300 sm:text-base sm:leading-8">
                {{ __('نبتكر حلولاً معمارية متكاملة تدمج بين الرفاهية والسكن الراقي في أرقى المواقع الحيوية، مع أنظمة سداد مرنة وتقسيط مباشر يصل إلى 8 سنوات بدون فوائد.') }}
            </p>

            {{-- Search Bar --}}
            <form action="{{ route('public.projects.index') }}" method="GET" role="search" class="mx-auto mt-8 flex max-w-2xl items-center gap-2 rounded-3xl border border-white/15 bg-slate-900/80 p-2 shadow-2xl backdrop-blur-2xl">
                <svg class="ms-3 h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                    <path d="m20 20-3.5-3.5" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="{{ __('ابحث عن شقة، فيلا، مشروع، أو مدينة (الشيخ زايد، التجمع...)...') }}"
                    class="w-full bg-transparent px-2 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none"
                >
                <button type="submit" class="app-button shrink-0 text-sm px-6 py-2.5">
                    {{ __('بحث سريع') }}
                </button>
            </form>

            {{-- Stats Counters --}}
            <dl class="mx-auto mt-12 grid max-w-3xl grid-cols-2 gap-4 border-t border-white/10 pt-8 sm:grid-cols-4">
                <div class="space-y-1">
                    <dt class="text-3xl font-extrabold text-white sm:text-4xl">+15</dt>
                    <dd class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('عاماً من الخبرة والتميز') }}</dd>
                </div>
                <div class="space-y-1">
                    <dt class="text-3xl font-extrabold text-brand-400 sm:text-4xl">{{ $projectCount }}</dt>
                    <dd class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('مجتمعات سكنية وتجارية') }}</dd>
                </div>
                <div class="space-y-1">
                    <dt class="text-3xl font-extrabold text-white sm:text-4xl">{{ $unitCount }}</dt>
                    <dd class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('وحدة سكنية وتجارية') }}</dd>
                </div>
                <div class="space-y-1">
                    <dt class="text-3xl font-extrabold text-emerald-400 sm:text-4xl">{{ $availableUnitCount }}</dt>
                    <dd class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('متاحة للتعاقد المباشر') }}</dd>
                </div>
            </dl>
        </div>
    </section>

    {{-- Promotional Banners Slider --}}
    @include('public.partials.banner-slider', ['banners' => $banners ?? collect()])

    {{-- Venecia Strategic Pillars / Values Section --}}
    <section class="mt-16 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="app-card card-hover space-y-3 p-6">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500/15 text-2xl text-brand-300">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <h3 class="text-lg font-bold text-white">{{ __('تصاميم معمارية أيقونية') }}</h3>
            <p class="text-xs leading-relaxed text-slate-400">
                {{ __('تدمج مشاريعنا بين الطراز الإيطالي الحديث والبساطة الأنيقة، مع استغلال أمثل للمساحات والإضاءة الطبيعية.') }}
            </p>
        </div>

        <div class="app-card card-hover space-y-3 p-6">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/15 text-2xl text-amber-300">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" stroke-width="1.8"/>
                    <circle cx="12" cy="10" r="2.5" stroke-width="1.8"/>
                </svg>
            </span>
            <h3 class="text-lg font-bold text-white">{{ __('مواقع استراتيجية نادرة') }}</h3>
            <p class="text-xs leading-relaxed text-slate-400">
                {{ __('نختار مواقع مشاريعنا بعناية في أرقى الأحياء بالقاهرة الجديدة، الشيخ زايد، والساحل الشمالي لقربها من المحاور الحيوية.') }}
            </p>
        </div>

        <div class="app-card card-hover space-y-3 p-6">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/15 text-2xl text-emerald-300">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="3" y="6" width="18" height="12" rx="2" stroke-width="1.8"/>
                    <path d="M3 10h18M7 15h4" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </span>
            <h3 class="text-lg font-bold text-white">{{ __('أنظمة سداد تفاعلية ومرنة') }}</h3>
            <p class="text-xs leading-relaxed text-slate-400">
                {{ __('خطط تقسيط مباشر تبدأ بمقدم 10% وسداد يصل إلى 8 سنوات مع حاسبة تفاعلية فورية لحساب كافة الدفعات.') }}
            </p>
        </div>

        <div class="app-card card-hover space-y-3 p-6">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-500/15 text-2xl text-violet-300">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="7.5" cy="15.5" r="4.5" stroke-width="1.8"/>
                    <path d="M10.7 12.3 20 3M16.5 5.5l3 3M13.5 8.5l2 2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <h3 class="text-lg font-bold text-white">{{ __('تسليم في الموعد وضمان شامل') }}</h3>
            <p class="text-xs leading-relaxed text-slate-400">
                {{ __('التزام تام بالجدول الزمني للإنشاءات والتسليم مع تشطيبات سوبر لوكس وضمان ممتد على الهيكل والأعمال الكهروميكانيكية.') }}
            </p>
        </div>
    </section>

    {{-- Featured Projects Showcase --}}
    <section class="mt-16">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <p class="mobile-section-title">{{ __('Featured projects') }}</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ __('مجتمعات سكنية متكاملة بلمسة إيطالية') }}</h2>
            </div>
            <a href="{{ route('public.projects.index') }}" class="link-arrow shrink-0 text-sm font-semibold">
                {{ __('استعرض جميع المشاريع') }} ←
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($projects as $project)
                @php
                    // Cover image first, then first gallery image, then a stock fallback.
                    // The relative path is prefixed with /storage/ exactly once.
                    $projectImage = $project->cover_image_path
                        ?: collect($project->images ?? [])->filter(fn ($img) => is_string($img))->first();
                    $pImg = $projectImage
                        ? asset('storage/'.$projectImage)
                        : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80';
                @endphp

                <article class="app-card card-hover group flex flex-col overflow-hidden p-0 transition-all duration-300">
                    <div class="relative h-48 w-full overflow-hidden bg-slate-900">
                        <img src="{{ $pImg }}" alt="{{ $project->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <div class="absolute top-3 right-3">
                            <span class="badge badge-brand">{{ $project->status ?? __('نشط') }}</span>
                        </div>
                        <div class="absolute bottom-3 right-3 text-xs text-slate-300 font-semibold">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                {{ $project->units_count ?? $project->units->count() }} {{ __('وحدة سكنية وتجارية') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col p-5 space-y-3">
                        <h3 class="text-xl font-bold text-white group-hover:text-brand-300 transition-colors">
                            {{ $project->name }}
                        </h3>
                        <p class="flex items-center gap-1.5 text-xs text-slate-400">
                            <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" stroke-width="1.8"/>
                                <circle cx="12" cy="10" r="2.5" stroke-width="1.8"/>
                            </svg>
                            {{ $project->location ?? __('المربع الذهبي، القاهرة الجديدة') }}
                        </p>
                        @if ($project->description)
                            <p class="line-clamp-2 text-xs leading-relaxed text-slate-400">{{ $project->description }}</p>
                        @endif
                        <a href="{{ route('public.projects.show', $project->slug) }}" class="app-button--ghost justify-center mt-auto text-xs py-2.5">
                            {{ __('تفاصيل المشروع والوحدات المتاحة') }} ←
                        </a>
                    </div>
                </article>
            @empty
                <div class="app-card col-span-full py-12 text-center text-slate-400">
                    {{ __('المشاريع قيد التجهيز وسيتم إدراجها قريباً.') }}
                </div>
            @endforelse
        </div>
    </section>

    {{-- Featured Available Units Showcase --}}
    @if (isset($featuredUnits) && $featuredUnits->count() > 0)
        <section class="mt-16">
            <div class="mb-8 flex items-end justify-between gap-4">
                <div>
                    <p class="mobile-section-title">{{ __('فرص استثمارية مميزة') }}</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ __('أحدث الوحدات المتاحة للتعاقد الفوري') }}</h2>
                </div>
                <a href="{{ route('public.projects.index') }}" class="link-arrow shrink-0 text-sm font-semibold">
                    {{ __('تصفح جميع الوحدات') }} ←
                </a>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredUnits as $fUnit)
                @php
                    $uPrice = (float) $fUnit->current_price;
                    $uEstQuarterly = $uPrice > 0 ? round(($uPrice * 0.9) / 32) : 0;
                    $uDelivery = $fUnit->delivery_date?->year ?? (2026 + ($fUnit->id % 3));

                    $unitImage = (is_string($fUnit->thumbnail) && $fUnit->thumbnail !== '')
                        ? $fUnit->thumbnail
                        : collect($fUnit->images ?? [])->filter(fn ($img) => is_string($img))->first();
                    $uImg = $unitImage
                        ? asset('storage/'.$unitImage)
                        : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';
                @endphp

                    <article class="app-card card-hover group flex flex-col overflow-hidden p-0 transition-all duration-300">
                        <div class="relative h-48 w-full overflow-hidden bg-slate-900">
                            <img src="{{ $uImg }}" alt="{{ $fUnit->unit_type }} {{ $fUnit->unit_number }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                            <div class="absolute top-3 right-3 flex gap-2">
                                <span class="rounded-lg bg-indigo-950/90 px-2.5 py-1 text-[11px] font-bold text-indigo-200 backdrop-blur-md border border-indigo-400/20">
                                    {{ $fUnit->unit_type }}
                                </span>
                                <span class="rounded-lg bg-amber-500/90 px-2.5 py-1 text-[11px] font-bold text-slate-950 backdrop-blur-md">
                                    {{ __('مميز ★') }}
                                </span>
                            </div>
                            <div class="absolute bottom-3 right-3 text-xs text-white">
                                <span class="rounded-lg bg-slate-900/80 px-2.5 py-1 font-medium backdrop-blur-md">
                                    {{ __('استلام:') }} <strong class="text-brand-300">{{ $uDelivery }}</strong>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-5 space-y-3">
                            <p class="text-xs font-semibold text-brand-400">
                                {{ $fUnit->project?->name ?? __('Featured projects') }}
                            </p>
                            <h3 class="text-lg font-bold text-white group-hover:text-brand-300 transition-colors">
                                <a href="{{ route('public.units.show', $fUnit->unit_number) }}">
                                    {{ $fUnit->unit_type }} {{ __('نموذج') }} {{ $fUnit->unit_number }}
                                </a>
                            </h3>

                            <div class="flex items-center justify-between border-y border-white/10 py-2.5 text-xs text-slate-300">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M3 9h18M9 21V9" stroke-width="1.8"/></svg>
                                    <strong>{{ number_format((float)$fUnit->area) }}</strong> {{ __('م²') }}
                                </span>
                                @if ($fUnit->bedrooms)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 18v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6M3 18h18M3 18v3M21 18v3M7 10V8a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M7 10h10" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <strong>{{ $fUnit->bedrooms }}</strong> {{ __('غرف') }}
                                    </span>
                                @endif
                                @if ($fUnit->bathrooms)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3s6 5.8 6 11a6 6 0 0 1-12 0c0-5.2 6-11 6-11Z" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                        <strong>{{ $fUnit->bathrooms }}</strong> {{ __('حمام') }}
                                    </span>
                                @endif
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3 space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400">{{ __('السعر الإجمالي:') }}</span>
                                    <strong class="text-sm font-bold text-white">{{ number_format($uPrice) }} {{ __('ج.م') }}</strong>
                                </div>
                                <div class="flex items-center justify-between text-xs text-emerald-400 border-t border-white/5 pt-1">
                                    <span>{{ __('قسط ربع سنوي:') }}</span>
                                    <strong class="font-bold">{{ number_format($uEstQuarterly) }} {{ __('ج.م') }}</strong>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mt-auto pt-1">
                                <a href="{{ route('public.units.show', $fUnit->unit_number) }}" class="app-button--ghost text-xs justify-center py-2 min-h-9">
                                    {{ __('عرض التفاصيل') }}
                                </a>
                                <a href="{{ route('installments.index', ['unit_id' => $fUnit->id]) }}" class="app-button text-xs justify-center py-2 min-h-9">
                                    {{ __('احسب خطتك') }}
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Venecia Calculator Banner --}}
    <section class="mt-16 overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-brand-900/40 via-slate-900 to-brand-950 p-8 text-center sm:p-12 shadow-2xl relative">
        <div class="pointer-events-none absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative z-10 mx-auto max-w-2xl space-y-4">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-4 py-1 text-xs font-bold text-emerald-300">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="2" stroke-width="1.8"/><path d="M8 7h8M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 18.5h.01M12 18.5h.01M16 18.5h.01" stroke-width="1.8" stroke-linecap="round"/></svg> {{ __('حاسبة الأقساط التفاعلية المباشرة') }}
            </span>
            <h2 class="text-2xl font-bold tracking-tight text-white sm:text-4xl">
                {{ __('احسب خطة التقسيط المناسبة لك في ثوانٍ') }}
            </h2>
            <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                {{ __('اختر مقدم التعاقد وسنوات السداد المناسبة لظروفك الاستثمارية مع معاينة حية للأقساط وإمكانية طباعة ملف PDF معتمد.') }}
            </p>
            <div class="pt-4 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('installments.index') }}" class="app-button shadow-lg shadow-brand-600/30 px-8 py-3.5 text-sm">
                    {{ __('افتح حاسبة الأقساط الآن') }}
                </a>
                <a href="{{ route('public.projects.index') }}" class="app-button--ghost px-8 py-3.5 text-sm">
                    {{ __('Browse units') }}
                </a>
            </div>
        </div>
    </section>
@endsection
