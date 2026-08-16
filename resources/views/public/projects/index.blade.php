@extends('layouts.public')

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-brand-950 via-slate-900 to-slate-950 px-6 py-10 text-center shadow-2xl sm:py-14">
        <div class="pointer-events-none absolute inset-0 bg-grid opacity-30"></div>
        <div class="relative z-10 mx-auto max-w-3xl space-y-4">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-400/30 bg-amber-500/10 px-4 py-1.5 text-xs font-semibold text-amber-300 backdrop-blur-md">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3Z" stroke-width="1.8" stroke-linejoin="round"/></svg> {{ __('نخبة المشروعات والوحدات العقارية') }}
            </span>
            <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                {{ __('دليل الوحدات والمشاريع المتاحة') }}
            </h1>
            <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-300 sm:text-base">
                {{ __('اختر من بين أضخم الوحدات السكنية والتجارية، واحتسب خطة التقسيط المباشرة بضغطة زر واحدة.') }}
            </p>
        </div>
    </section>

    {{-- Floating Search & Filter Bar --}}
    <section class="-mt-6 relative z-20 mx-auto max-w-5xl px-2">
        <form method="GET" action="{{ route('public.projects.index') }}" class="app-card space-y-4 p-4 sm:p-6 shadow-2xl backdrop-blur-2xl">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                {{-- Search Input --}}
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute right-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 rtl:right-3.5 rtl:left-auto ltr:left-3.5 ltr:right-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                        <path d="m20 20-3.5-3.5" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="{{ __('بحث باسم المشروع، الحي، أو نوع الوحدة...') }}"
                        class="app-input w-full pr-10 pl-4 text-sm rtl:pr-10 rtl:pl-4 ltr:pl-10 ltr:pr-4"
                    >
                </div>

                {{-- Bedroom Filters --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <span class="text-xs font-semibold text-slate-400 shrink-0">{{ __('عدد الغرف:') }}</span>
                    @foreach (['' => __('الكل'), '2' => '+2', '3' => '+3', '4' => '+4', '5+' => '+5'] as $roomVal => $roomLabel)
                        <a
                            href="{{ route('public.projects.index', array_merge(request()->except('rooms'), array_filter(['rooms' => $roomVal]))) }}"
                            class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-xs font-bold transition-all {{ request('rooms') === (string)$roomVal ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 ring-2 ring-brand-400' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}"
                        >
                            {{ $roomLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Category / Type Pills --}}
            <div class="flex items-center gap-2 overflow-x-auto border-t border-white/10 pt-3 no-scrollbar">
                @php
                    $types = [
                        '' => __('جميع الأنواع'),
                        'فيلا' => __('فيلات مستقلة'),
                        'شقة' => __('شقق سكنية'),
                        'دوبلكس' => __('دوبلكس'),
                        'تاون هاوس' => __('تاون هاوس / بنتهاوس'),
                        'تجاري' => __('تجاري وإداري'),
                    ];
                @endphp
                @foreach ($types as $typeKey => $typeLabel)
                    <a
                        href="{{ route('public.projects.index', array_merge(request()->except('type'), array_filter(['type' => $typeKey]))) }}"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-semibold transition-all {{ request('type') === (string)$typeKey ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}"
                    >
                        {{ $typeLabel }}
                    </a>
                @endforeach
            </div>
        </form>
    </section>

    {{-- Projects Showcase --}}
    @if (isset($projects) && $projects->isNotEmpty())
        <section class="mt-10">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('المشاريع') }}</p>
                    <h2 class="text-xl font-bold text-white sm:text-2xl">{{ __('نخبة المشروعات') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    @php
                        // Cover image first, then first gallery image, then a stock fallback.
                        // The relative path is prefixed with /storage/ exactly once.
                        $projectImage = $project->cover_image_path
                            ?: collect($project->images ?? [])->filter(fn ($img) => is_string($img))->first();
                        $pImg = $projectImage
                            ? asset('storage/'.$projectImage)
                            : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80';
                        $pStatusLabel = match ($project->status) {
                            'active' => __('نشط'),
                            'launching' => __('Launching'),
                            'sold' => __('مباع'),
                            default => __('Draft'),
                        };
                    @endphp
                    <article class="app-card card-hover group flex flex-col overflow-hidden p-0 transition-all duration-300">
                        <div class="relative h-48 w-full overflow-hidden bg-slate-900">
                            <img src="{{ $pImg }}" alt="{{ $project->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                            <div class="absolute top-3 right-3">
                                <span class="badge badge-brand">{{ $pStatusLabel }}</span>
                            </div>
                            <div class="absolute bottom-3 right-3 text-xs text-slate-300 font-semibold">
                                <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M4 21V7l8-4 8 4v14M9 21v-4h6v4M8 7h.01M12 7h.01M16 7h.01M8 11h.01M12 11h.01M16 11h.01" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                {{ $project->units->count() }} {{ __('وحدة سكنية وتجارية') }}
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
                @endforeach
            </div>
        </section>
    @endif

    {{-- Units Listing Grid --}}
    <section class="mt-10">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="mobile-section-title">{{ __('الوحدات المتاحة') }}</p>
                <h2 class="text-xl font-bold text-white sm:text-2xl">
                    {{ __('نتائج البحث والتصفية') }}
                    <span class="text-sm font-normal text-slate-400">({{ $units->total() }} {{ __('وحدة') }})</span>
                </h2>
            </div>
            @if (request()->hasAny(['q', 'type', 'rooms']))
                <a href="{{ route('public.projects.index') }}" class="text-xs text-rose-400 hover:underline">
                    <span class="inline-flex items-center gap-1">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 6l12 12M18 6L6 18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ __('إلغاء التصفية') }}
                    </span>
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($units as $unit)
                @php
                    $price = (float) $unit->current_price;
                    $estQuarterly = $price > 0 ? round(($price * 0.9) / 32) : 0;
                    $deliveryYear = $unit->delivery_date?->year ?? (2026 + ($unit->id % 3));
                    $devName = __('شركة فينسيا للاستثمار والتطوير العقاري');

                    // Clean category tag
                    $unitTypeTag = strtoupper($unit->unit_type ?? 'APARTMENT');
                    if (str_contains($unitTypeTag, 'فيلا')) $unitTypeTag = 'VILLA';
                    elseif (str_contains($unitTypeTag, 'دوبلكس')) $unitTypeTag = 'DUPLEX';
                    elseif (str_contains($unitTypeTag, 'بنتهاوس')) $unitTypeTag = 'PENTHOUSE';
                    elseif (str_contains($unitTypeTag, 'شقة')) $unitTypeTag = 'APARTMENT';

                    // Unit image: main thumbnail first, then first gallery image,
                    // then a stock image as fallback.
                    $unitImage = (is_string($unit->thumbnail) && $unit->thumbnail !== '')
                        ? $unit->thumbnail
                        : collect($unit->images ?? [])->filter(fn ($img) => is_string($img))->first();
                    $imageUrls = [
                        'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
                    ];
                    $imgUrl = $unitImage
                        ? asset('storage/'.$unitImage)
                        : $imageUrls[$unit->id % count($imageUrls)];
                @endphp

                <article class="app-card card-hover group flex flex-col overflow-hidden p-0 transition-all duration-300">
                    {{-- Card Media Section --}}
                    <div class="relative h-56 w-full overflow-hidden bg-slate-900">
                        <img
                            src="{{ $imgUrl }}"
                            alt="{{ $unit->unit_type }} {{ $unit->unit_number }}"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            loading="lazy"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>

                        {{-- Top Badges --}}
                        <div class="absolute top-3 inset-x-3 flex items-center justify-between gap-2">
                            <span class="rounded-lg bg-brand-950/90 px-2.5 py-1 text-[11px] font-extrabold uppercase tracking-wider text-brand-200 backdrop-blur-md border border-brand-400/20">
                                {{ $unitTypeTag }}
                            </span>
                            @if ($unit->featured || $loop->iteration % 2 === 0)
                                <span class="rounded-lg bg-amber-500/90 px-2.5 py-1 text-[11px] font-bold text-slate-950 shadow-md backdrop-blur-md">
                                    {{ __('مميز ★') }}
                                </span>
                            @endif
                        </div>

                        {{-- Bottom Overlay Tag --}}
                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-xs text-white">
                            <span class="rounded-lg bg-slate-900/80 px-2.5 py-1 font-medium backdrop-blur-md border border-white/10">
                                {{ __('الاستلام:') }} <strong class="text-brand-300">{{ $deliveryYear }}</strong>
                            </span>
                            @include('partials.unit-status', ['status' => $unit->status])
                        </div>
                    </div>

                    {{-- Card Content Body --}}
                    <div class="flex flex-1 flex-col p-5 space-y-3">
                        {{-- Developer & Subtitle --}}
                        <p class="text-xs font-semibold text-brand-400">
                            {{ $unit->project?->name ?? __('كمبوند راقي') }} • {{ $devName }}
                        </p>

                        {{-- Main Title --}}
                        <h3 class="text-lg font-bold text-white group-hover:text-brand-300 transition-colors">
                            <a href="{{ route('public.units.show', $unit->unit_number) }}">
                                {{ $unit->unit_type }} فاخرة {{ __('نموذج') }} {{ $unit->unit_number }}
                            </a>
                        </h3>

                        {{-- Location --}}
                        <p class="flex items-center gap-1.5 text-xs text-slate-400">
                            <svg class="h-4 w-4 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" stroke-width="1.8"/>
                                <circle cx="12" cy="10" r="2.5" stroke-width="1.8"/>
                            </svg>
                            {{ $unit->project?->location ?? __('المربع الذهبي، القاهرة الجديدة') }}
                        </p>

                        {{-- Specs Icons Row --}}
                        <div class="flex items-center justify-between border-y border-white/10 py-3 text-xs text-slate-300">
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M3 9h18M9 21V9" stroke-width="1.8"/></svg>
                                <strong>{{ number_format((float)$unit->area) }}</strong> {{ __('م²') }}
                            </span>
                            @if ($unit->bedrooms)
                                <span class="flex items-center gap-1">
                                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M2 4v16M2 8h20v12M2 17h20M6 8v9" stroke-width="1.8"/></svg>
                                    <strong>{{ $unit->bedrooms }}</strong> {{ __('غرف') }}
                                </span>
                            @endif
                            @if ($unit->bathrooms)
                                <span class="flex items-center gap-1">
                                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1Z" stroke-width="1.8"/><path d="M6 12V5a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v7" stroke-width="1.8"/></svg>
                                    <strong>{{ $unit->bathrooms }}</strong> {{ __('حمامات') }}
                                </span>
                            @endif
                        </div>

                        {{-- Financial Summary Box --}}
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-3 space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400">{{ __('سعر الوحدة الإجمالي:') }}</span>
                                <strong class="text-sm font-bold text-white">{{ number_format($price) }} {{ __('ج.م') }}</strong>
                            </div>
                            <div class="flex items-center justify-between text-xs border-t border-white/5 pt-1.5">
                                <span class="text-slate-400">{{ __('قيمة القسط الدوري:') }}</span>
                                <strong class="text-xs font-bold text-emerald-400">{{ number_format($estQuarterly) }} {{ __('ج.م') }}</strong>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="grid grid-cols-2 gap-2 pt-1 mt-auto">
                            <a
                                href="{{ route('public.units.show', $unit->unit_number) }}"
                                class="app-button--ghost text-xs justify-center py-2.5 min-h-10"
                            >
                                {{ __('عرض التفاصيل') }}
                            </a>
                            <a
                                href="{{ route('installments.index', ['unit_id' => $unit->id]) }}"
                                class="app-button text-xs justify-center gap-1.5 py-2.5 min-h-10"
                            >
                                <span>{{ __('احسب خطة السداد') }}</span>
                                <svg class="h-3.5 w-3.5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M5 12h14M13 6l6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="app-card col-span-full flex flex-col items-center py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-white/5 text-slate-400">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                            <path d="m20 20-3.5-3.5" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-white">{{ __('لم نجد وحدات مطابقة للبحث') }}</h3>
                    <p class="mt-1 text-sm text-slate-400">{{ __('جرب استخدام كلمات بحث مختلفة أو تغيير خيارات التصفية.') }}</p>
                    <a href="{{ route('public.projects.index') }}" class="app-button mt-6">{{ __('عرض جميع الوحدات') }}</a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($units->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $units->links() }}
            </div>
        @endif
    </section>
@endsection
