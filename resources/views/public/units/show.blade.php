@extends('layouts.public')

@section('content')
    @php
        $price = (float) $unit->current_price;
        $devName = __('شركة فينسيا للاستثمار والتطوير العقاري');
        $deliveryDate = $unit->delivery_date?->translatedFormat('F Y') ?: (__('December') . ' ' . (2026 + ($unit->id % 3)));
        $locationFull = ($unit->project?->location ?? __('Location pending')) . ', ' . ($unit->project?->city ?? __('City'));
        
        // Gallery: main thumbnail first, then uploaded images, stock images as fallback
        $uploadedImages = collect([$unit->thumbnail])
            ->merge(collect($unit->images ?? []))
            ->filter(fn ($img) => is_string($img) && $img !== '')
            ->map(fn ($img) => asset('storage/'.$img))
            ->unique()
            ->values()
            ->all();
        $stockImages = [
            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
        ];
        $galleryImages = $uploadedImages !== [] ? $uploadedImages : $stockImages;
        $hasMap = $unit->map_lat && $unit->map_lng;

        // Finishing & additional services: only the features selected for this unit are shown.
        $availableFeatures = collect($availableFeatures ?? []);
        $selectedFeatureTitles = collect($unit->features ?? [])->filter(fn ($title) => is_string($title) && $title !== '');
        // Selected features first (global list or custom admin-added titles),
        // falling back to the full global list when nothing is selected.
        $unitFeatures = $selectedFeatureTitles->isNotEmpty()
            ? $selectedFeatureTitles
                ->map(fn ($title) => $availableFeatures->first(fn ($f) => ($f['title'] ?? null) === $title)
                    ?? ['icon' => 'sparkles', 'title' => $title, 'desc' => ''])
                ->values()
            : $availableFeatures;
        
        $whatsappMessage = rawurlencode(
            __('Hello, I would like to inquire about unit') . " #{$unit->unit_number} ({$unit->unit_type}) " . __('in project') . " {$unit->project?->name} " . __('at price') . ' ' . number_format($price) . ' EGP.\n' . __('Unit link') . ': ' . request()->fullUrl()
        );
    @endphp

    <div class="animate-fade-up space-y-8 pt-4" x-data="{ activeImage: '{{ $galleryImages[0] }}' }">
        {{-- Breadcrumb Navigation --}}
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="{{ route('home') }}" class="hover:text-white">{{ __('الرئيسية') }}</a>
            <span>/</span>
            <a href="{{ route('public.projects.index') }}" class="hover:text-white">{{ __('المشاريع والوحدات') }}</a>
            <span>/</span>
            @if ($unit->project)
                <a href="{{ route('public.projects.show', $unit->project->slug) }}" class="hover:text-white">{{ $unit->project->name }}</a>
                <span>/</span>
            @endif
            <span class="text-slate-200 font-semibold">{{ __('وحدة') }} {{ $unit->unit_number }}</span>
        </div>

        {{-- 1. High Quality Gallery & Main Unit Hero --}}
        <section class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- Main Interactive Image Preview (8 cols) --}}
            <div class="space-y-3 lg:col-span-8">
                <div class="relative h-[360px] sm:h-[480px] w-full overflow-hidden rounded-3xl border border-white/10 bg-slate-900 shadow-2xl">
                    <img
                        :src="activeImage"
                        alt="{{ $unit->unit_type }} {{ $unit->unit_number }}"
                        class="h-full w-full object-cover transition-all duration-500"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-black/30"></div>

                    {{-- Floating Top Badges --}}
                    <div class="absolute top-4 inset-x-4 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-xl bg-brand-950/90 px-3 py-1.5 text-xs font-extrabold text-brand-200 border border-brand-400/30 backdrop-blur-md">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                {{ $unit->unit_type }}
                            </span>
                            @if ($unit->featured)
                                <span class="rounded-xl bg-amber-500/90 px-3 py-1.5 text-xs font-bold text-slate-950 shadow-lg backdrop-blur-md">
                                    ★ {{ __('وحدة مميزة') }}
                                </span>
                            @endif
                        </div>
                        @include('partials.unit-status', ['status' => $unit->status])
                    </div>

                    {{-- Bottom Overlay Info --}}
                    <div class="absolute bottom-4 inset-x-4 flex flex-col gap-1 text-white">
                        <span class="text-xs font-medium text-brand-300">{{ $devName }}</span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold">{{ $unit->unit_type }} فاخرة - {{ __('وحدة') }} {{ $unit->unit_number }}</h1>
                        <p class="flex items-center gap-1.5 text-xs text-slate-300">
                            <svg class="h-4 w-4 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" stroke-width="1.8"/>
                                <circle cx="12" cy="10" r="2.5" stroke-width="1.8"/>
                            </svg>
                            {{ $locationFull }}
                        </p>
                    </div>
                </div>

                {{-- Thumbnails Carousel --}}
                <div class="flex items-center gap-3 overflow-x-auto pb-1 no-scrollbar">
                    @foreach ($galleryImages as $index => $img)
                        <button
                            type="button"
                            @click="activeImage = '{{ $img }}'"
                            class="relative h-20 w-28 shrink-0 overflow-hidden rounded-2xl border-2 transition-all"
                            :class="activeImage === '{{ $img }}' ? 'border-brand-500 scale-95 shadow-md shadow-brand-500/20' : 'border-transparent opacity-70 hover:opacity-100'"
                        >
                            <img src="{{ $img }}" alt="Thumbnail {{ $index + 1 }}" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Quick Pricing & Direct Actions Card (4 cols) --}}
            <div class="flex flex-col justify-between space-y-4 lg:col-span-4">
                <div class="app-card space-y-5 p-6 shadow-2xl">
                    <div>
                        <p class="mobile-section-title">{{ __('القيمة والسعر الإجمالي') }}</p>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl sm:text-4xl font-extrabold text-white">{{ number_format($price) }}</span>
                            <span class="text-sm font-semibold text-slate-400">{{ __('ج.م') }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ __('متوسط سعر المتر:') }} <strong class="text-slate-200">{{ number_format((float)$unit->price_per_meter) }} {{ __('ج.م/م²') }}</strong>
                        </p>
                        <div class="mt-3 flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-xs">
                            <span class="inline-flex items-center gap-1.5 text-slate-400">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M3 9h18M9 21V9" stroke-width="1.8"/></svg>
                                {{ __('مساحة الوحدة') }}:
                            </span>
                            <strong class="text-base font-extrabold text-white">{{ number_format((float)$unit->area) }} <span class="text-xs font-normal text-slate-400">{{ __('م²') }}</span></strong>
                        </div>
                    </div>

                    {{-- Sample Payment Breakdown Chips --}}
                    <div class="space-y-2 border-y border-white/10 py-4 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">{{ __('مقدم التعاقد (10%):') }}</span>
                            <strong class="text-white font-bold">{{ number_format($downPaymentAmount) }} {{ __('ج.م') }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">{{ __('القسط الربع سنوي (8 سنوات):') }}</span>
                            <strong class="text-emerald-400 font-bold">{{ number_format($quarterlyInstallment) }} {{ __('ج.م') }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">{{ __('خصم الدفع الفوري كاش (15%):') }}</span>
                            <strong class="text-amber-300 font-bold">{{ number_format($cashDiscountAmount) }} {{ __('ج.م') }}</strong>
                        </div>
                    </div>

                    {{-- Direct Quick Actions --}}
                    <div class="space-y-2.5">
                        {{-- 1. Calculate Installment Button --}}
                        <a
                            href="{{ route('installments.index', ['unit_id' => $unit->id]) }}"
                            class="app-button w-full justify-center gap-2 py-3.5 text-sm shadow-lg shadow-brand-600/30"
                        >
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="2" stroke-width="1.8"/><path d="M8 7h8M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 18.5h.01M12 18.5h.01M16 18.5h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ __('احسب خطة التقسيط المخصصة') }}
                            </span>
                            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M5 12h14M13 6l6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>

                        {{-- 2. Share via WhatsApp Button --}}
                        <a
                            href="https://wa.me/?text={{ $whatsappMessage }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="app-button w-full justify-center gap-2 bg-emerald-600 hover:bg-emerald-500 py-3.5 text-sm shadow-lg shadow-emerald-600/30"
                        >
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" stroke-width="1.8" stroke-linejoin="round"/></svg>
                                {{ __('مشاركة التفاصيل عبر الواتساب') }}
                            </span>
                        </a>

                        {{-- 3. Request Details Link --}}
                        <a
                            href="#inquiry"
                            class="app-button--ghost w-full justify-center text-xs py-2.5"
                        >
                            {{ __('طلب تواصل وحجز الوحدة') }}
                        </a>
                    </div>
                </div>

                {{-- Delivery Guarantee Badge Card --}}
                <div class="app-card flex items-center gap-3 p-4 bg-emerald-500/10 border-emerald-500/20">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="5" width="18" height="16" rx="2" stroke-width="1.8"/><path d="M8 3v4M16 3v4M3 10h18" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <div>
                        <p class="text-xs font-bold text-emerald-300">{{ __('موعد الاستلام المخطط') }}</p>
                        <p class="text-sm font-semibold text-white">{{ $deliveryDate }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. Technical & Engineering Specifications --}}
        <section class="app-card space-y-6 p-6 sm:p-8">
            <div>
                <p class="mobile-section-title">{{ __('المواصفات الفنية والهندسية') }}</p>
                <h2 class="mt-1 text-2xl font-bold text-white">{{ __('تفاصيل المساحة والتقسيم الهندسي') }}</h2>
            </div>

            {{-- Main Specs Grid --}}
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="touch-card space-y-1">
                    <p class="touch-card__label">{{ __('المساحة الكلية') }}</p>
                    <p class="text-2xl font-extrabold text-white">{{ number_format((float)$unit->area) }} <span class="text-xs font-normal text-slate-400">{{ __('m²') }}</span></p>
                </div>
                <div class="touch-card space-y-1">
                    <p class="touch-card__label">{{ __('غرف النوم') }}</p>
                    <p class="text-2xl font-extrabold text-white">{{ $unit->bedrooms ?? 3 }} <span class="text-xs font-normal text-slate-400">{{ __('غرف') }}</span></p>
                </div>
                <div class="touch-card space-y-1">
                    <p class="touch-card__label">{{ __('الحمامات') }}</p>
                    <p class="text-2xl font-extrabold text-white">{{ $unit->bathrooms ?? 2 }} <span class="text-xs font-normal text-slate-400">{{ __('حمامات') }}</span></p>
                </div>
                <div class="touch-card space-y-1">
                    <p class="touch-card__label">{{ __('مستوى الدور') }}</p>
                    <p class="text-2xl font-extrabold text-white">
                        @if ($unit->floor)
                            @if ($unit->floor->number === 0)
                                {{ __('Ground floor') }}
                            @else
                                {{ __('Floor :number', ['number' => $unit->floor->number]) }}
                            @endif
                        @else
                            {{ __('Repeated floor') }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Sub-areas Specifications Grid --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 border-t border-white/10 pt-4">
                @if ($unit->garden_area)
                    <div class="rounded-2xl bg-white/5 p-3">
                        <p class="text-[11px] font-semibold uppercase text-slate-400">{{ __('مساحة الحديقة') }}</p>
                        <p class="mt-1 text-lg font-bold text-emerald-400">{{ number_format((float)$unit->garden_area) }} {{ __('m²') }}</p>
                    </div>
                @endif
                @if ($unit->roof_area)
                    <div class="rounded-2xl bg-white/5 p-3">
                        <p class="text-[11px] font-semibold uppercase text-slate-400">{{ __('مساحة الروف') }}</p>
                        <p class="mt-1 text-lg font-bold text-amber-300">{{ number_format((float)$unit->roof_area) }} {{ __('m²') }}</p>
                    </div>
                @endif
                @if ($unit->balcony_area || $unit->terrace_count)
                    <div class="rounded-2xl bg-white/5 p-3">
                        <p class="text-[11px] font-semibold uppercase text-slate-400">{{ __('مساحة التراس') }}</p>
                        @if ($unit->balcony_area)
                            <p class="mt-1 text-lg font-bold text-brand-300">{{ number_format((float)$unit->balcony_area) }} {{ __('m²') }}</p>
                        @endif
                        @if ($unit->terrace_count)
                            <p class="mt-1 text-xs font-semibold text-slate-300">{{ __('عدد التراس') }}: {{ $unit->terrace_count }}</p>
                        @endif
                    </div>
                @endif
                <div class="rounded-2xl bg-white/5 p-3">
                    <p class="text-[11px] font-semibold uppercase text-slate-400">{{ __('المطور العقاري') }}</p>
                    <p class="mt-1 text-xs font-bold text-white truncate">{{ $devName }}</p>
                </div>
            </div>
        </section>

        {{-- 3. Sample Payment Plan & Financial Options Table --}}
        <section class="app-card space-y-6 p-6 sm:p-8">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="mobile-section-title">{{ __('أنظمة السداد والدفع') }}</p>
                    <h2 class="mt-1 text-2xl font-bold text-white">{{ __('جدول عينة خطة السداد الافتراضية') }}</h2>
                </div>
                <a href="{{ route('installments.index', ['unit_id' => $unit->id]) }}" class="link-arrow text-xs">
                    {{ __('تخصيص نسبة المقدم وسنوات التقسيط') }} ←
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                {{-- Payment Option 1: Standard Installments --}}
                <div class="rounded-3xl border border-brand-500/30 bg-brand-500/10 p-5 space-y-3 relative overflow-hidden">
                    <span class="absolute top-3 left-3 rounded-full bg-brand-600 px-2.5 py-0.5 text-[10px] font-bold text-white">{{ __('Best value') }}</span>
                    <p class="text-xs font-bold uppercase text-brand-300">{{ __('خطة التقسيط المباشر') }}</p>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-400">{{ __('مقدم 10% والباقي على 8 سنوات') }}</p>
                        <p class="text-2xl font-extrabold text-white">{{ number_format($quarterlyInstallment) }} <span class="text-xs font-normal text-slate-300">{{ __('ج.م / ربع سنوي') }}</span></p>
                    </div>
                    <div class="border-t border-brand-500/20 pt-2 text-xs space-y-1 text-slate-300">
                        <div class="flex justify-between"><span>{{ __('قيمة المقدم:') }}</span><strong>{{ number_format($downPaymentAmount) }} ج.م</strong></div>
                        <div class="flex justify-between"><span>{{ __('عدد الأقساط:') }}</span><strong>{{ __("32 قسط ربع سنوي") }}</strong></div>
                    </div>
                </div>

                {{-- Payment Option 2: Monthly Installments --}}
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 space-y-3">
                    <p class="text-xs font-bold uppercase text-slate-400">{{ __('خطة الأقساط الشهرية') }}</p>
                    <div class="space-y-1">
                        <p class="text-xs text-slate-400">{{ __('مقدم 10% على 96 شهر') }}</p>
                        <p class="text-2xl font-extrabold text-emerald-400">{{ number_format($monthlyInstallment) }} <span class="text-xs font-normal text-slate-300">{{ __('ج.م / شهرياً') }}</span></p>
                    </div>
                    <div class="border-t border-white/10 pt-2 text-xs space-y-1 text-slate-300">
                        <div class="flex justify-between"><span>{{ __('قيمة المقدم:') }}</span><strong>{{ number_format($downPaymentAmount) }} ج.م</strong></div>
                        <div class="flex justify-between"><span>{{ __('مدة السداد:') }}</span><strong>{{ __("8 سنوات (96 شهر)") }}</strong></div>
                    </div>
                </div>

                {{-- Payment Option 3: Immediate Cash Purchase --}}
                <div class="rounded-3xl border border-amber-500/30 bg-amber-500/10 p-5 space-y-3">
                    <p class="text-xs font-bold uppercase text-amber-300">{{ __('خصم الشراء الفوري (كاش)') }}</p>
                    <div class="space-y-1">
                        <p class="text-xs text-amber-200/70">{{ __('وفّر 15% عند السداد الفوري') }}</p>
                        <p class="text-2xl font-extrabold text-amber-300">{{ number_format($cashPrice) }} <span class="text-xs font-normal text-slate-300">{{ __('ج.م') }}</span></p>
                    </div>
                    <div class="border-t border-amber-500/20 pt-2 text-xs space-y-1 text-amber-200/80">
                        <div class="flex justify-between"><span>{{ __('إجمالي الوفر:') }}</span><strong>{{ number_format($cashDiscountAmount) }} EGP</strong></div>
                        <div class="flex justify-between"><span>{{ __('نسبة الخصم:') }}</span><strong>{{ __('15% cash discount') }}</strong></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 4. Features & Amenities List --}}
        <section class="app-card space-y-6 p-6 sm:p-8">
            <div>
                <p class="mobile-section-title">{{ __('المميزات والخدمات الخاصة') }}</p>
                <h2 class="mt-1 text-2xl font-bold text-white">{{ __('مواصفات التشطيب والخدمات الإضافية') }}</h2>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($unitFeatures as $feat)
                    <div class="flex items-start gap-3.5 rounded-2xl border border-white/10 bg-white/5 p-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-500/15 text-brand-300">
                            {!! \App\Support\Features::iconSvg($feat['icon'] ?? 'sparkles') !!}
                        </span>
                        <div class="space-y-0.5">
                            <h3 class="text-sm font-bold text-white">{{ $feat['title'] ?? '' }}</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ $feat['desc'] ?? '' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-white/10 bg-white/5 p-8 text-center text-sm text-slate-400">
                        {{ __('لا توجد مواصفات إضافية محددة لهذه الوحدة حالياً.') }}
                    </div>
                @endforelse
            </div>
        </section>

        {{-- 5. Inquiry & Sales Form + Location Map --}}
        <div class="grid grid-cols-1 items-stretch gap-6 {{ $hasMap ? 'lg:grid-cols-2' : '' }}">
            <section id="inquiry" class="app-card space-y-6 p-6 sm:p-8 {{ $hasMap ? '' : 'lg:max-w-2xl' }}">
                <div>
                    <p class="mobile-section-title">{{ __('تواصل مع فريق المبيعات') }}</p>
                    <h2 class="mt-1 text-2xl font-bold text-white">{{ __('هل ترغب في حجز هذه الوحدة أو استفسار؟') }}</h2>
                    <p class="mt-1 text-xs text-slate-400">{{ __('Our team will contact you shortly about this unit.') }}</p>
                </div>

                <form method="POST" action="{{ route('public.inquiries.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    <input type="hidden" name="unit_id" value="{{ $unit->id }}">
                    <input type="hidden" name="project_id" value="{{ $unit->project_id }}">
                    <input type="hidden" name="source" value="unit_details_page">

                    <label class="space-y-1.5 sm:col-span-2">
                        <span class="text-xs font-semibold text-slate-300">{{ __('الاسم بالكامل') }} <span class="text-rose-400">*</span></span>
                        <input class="app-input" name="name" placeholder="{{ __('أدخل اسمك') }}" required value="{{ old('name') }}">
                    </label>

                    <label class="space-y-1.5">
                        <span class="text-xs font-semibold text-slate-300">{{ __('البريد الإلكتروني') }} <span class="text-slate-500">({{ __('اختياري') }})</span></span>
                        <input class="app-input" type="email" inputmode="email" name="email" placeholder="{{ __('name@domain.com') }}" value="{{ old('email') }}">
                    </label>

                    <label class="space-y-1.5">
                        <span class="text-xs font-semibold text-slate-300">{{ __('رقم الهاتف') }} <span class="text-rose-400">*</span></span>
                        <input class="app-input" type="tel" inputmode="tel" name="phone" placeholder="{{ __('01xxxxxxxxx') }}" required value="{{ old('phone') }}">
                    </label>

                    <label class="space-y-1.5 sm:col-span-2">
                        <span class="text-xs font-semibold text-slate-300">{{ __('الموضوع') }} <span class="text-rose-400">*</span></span>
                        <input class="app-input" name="subject" placeholder="{{ __('أدخل موضوع الاستفسار') }}" required value="{{ old('subject') }}">
                    </label>

                    <label class="space-y-1.5 sm:col-span-2">
                        <span class="text-xs font-semibold text-slate-300">{{ __('الرسالة') }} <span class="text-rose-400">*</span></span>
                        <textarea class="app-input min-h-24" name="message" placeholder="{{ __('أود حجز موعد للمعاينة أو استفسار عن خصم النقدية...') }}" required>{{ old('message') }}</textarea>
                    </label>

                    <button type="submit" class="app-button sm:col-span-2 justify-center py-3.5 text-sm">
                        {{ __('إرسال طلب الاستفسار والحجز') }}
                    </button>
                </form>
            </section>

            @if ($hasMap)
                <section class="app-card flex flex-col space-y-6 p-6 sm:p-8">
                    <div>
                        <p class="mobile-section-title">{{ __('الموقع على الخريطة') }}</p>
                        <h2 class="mt-1 text-2xl font-bold text-white">{{ __('موقع الوحدة على خرائط جوجل') }}</h2>
                    </div>

                    <div class="relative min-h-72 flex-1 overflow-hidden rounded-3xl border border-white/10 bg-slate-900 shadow-inner">
                        <iframe
                            src="https://www.google.com/maps?q={{ $unit->map_lat }},{{ $unit->map_lng }}&z=16&output=embed"
                            class="absolute inset-0 h-full w-full"
                            style="border: 0"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                            title="{{ __('الموقع على الخريطة') }}"
                        ></iframe>
                    </div>

                    <a
                        href="https://www.google.com/maps?q={{ $unit->map_lat }},{{ $unit->map_lng }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="app-button--ghost w-full justify-center gap-2 py-3 text-sm"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" stroke-width="1.8"/><circle cx="12" cy="10" r="2.5" stroke-width="1.8"/></svg>
                        {{ __('فتح في خرائط جوجل') }}
                        <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M13 6l6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </section>
            @endif
        </div>
    </div>
@endsection
