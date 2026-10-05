@extends('layouts.public')

@section('content')
<div class="space-y-16 lg:space-y-24">
    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-brand-950 via-slate-900 to-slate-950 px-6 py-16 text-center shadow-2xl sm:py-24 lg:px-12">
        <div class="pointer-events-none absolute inset-0 bg-grid opacity-30"></div>
        <div class="pointer-events-none absolute -top-40 left-1/2 h-96 w-[700px] -translate-x-1/2 rounded-full bg-brand-500/20 blur-[130px]"></div>
        <div class="pointer-events-none absolute -bottom-28 -right-28 h-80 w-80 rounded-full bg-violet-600/15 blur-[110px]"></div>
        <div class="pointer-events-none absolute -bottom-28 -left-28 h-80 w-80 rounded-full bg-amber-500/10 blur-[110px]"></div>

        <div class="relative z-10 mx-auto max-w-4xl space-y-6">
            {{-- Badges --}}
            <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-500/10 px-4 py-1.5 text-xs font-bold text-amber-300 backdrop-blur-md shadow-lg shadow-amber-500/5">
                    <svg class="h-4 w-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ __('تحالف استثماري عقاري رائد') }}
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3.5 py-1.5 text-xs font-semibold text-slate-300 backdrop-blur-md">
                    <svg class="h-3.5 w-3.5 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="4" width="18" height="18" rx="2" stroke-width="1.8"/>
                        <path d="M16 2v4M8 2v4M3 10h18" stroke-width="1.8"/>
                    </svg>
                    {{ __('تأسست 29 يناير 2024') }}
                </span>
            </div>

            {{-- Headline --}}
            <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl leading-tight">
                {{ __('سابقة أعمال') }}
                <span class="bg-gradient-to-r from-amber-300 via-brand-400 to-violet-400 bg-clip-text text-transparent">
                    {{ __('فينيسيا للتنمية العمرانية') }}
                </span>
            </h1>

            {{-- Description --}}
            <p class="mx-auto max-w-3xl text-sm leading-relaxed text-slate-300 sm:text-base sm:leading-8">
                {{ __('شركة فينيسيا للتنمية والتخطيط العمراني هي تحالف استثماري استراتيجي بين 4 من كبرى الكيانات العقارية والاستشارية الرائدة بمصر، بخبرات متراكمة واستثمارات تتجاوز 10 مليارات جنيه مصري عبر أكثر من 3,500 وحدة سكنية وتجارية وإدارية وطبية وصروح تعليمية ومشروعات استصلاح زراعي.') }}
            </p>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center justify-center gap-3 pt-4">
                <a href="#alliance-projects" class="app-button gap-2 px-6 py-3.5 text-sm shadow-xl shadow-brand-600/30">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M19 14l-7 7m0 0l-7-7m7 7V3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ __('مشروعات التحالف الحالية') }}
                </a>
                <a href="{{ route('public.portfolio.pdf') }}" target="_blank" class="app-button app-button--ghost gap-2 px-6 py-3.5 text-sm">
                    <svg class="h-4 w-4 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/>
                        <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    {{ __('تحميل البيان الرسمي (PDF)') }}
                </a>
                <a href="{{ route('public.projects.index') }}" class="app-button app-button--ghost gap-2 px-6 py-3.5 text-sm">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                    {{ __('استكشف المشروعات المتاحة') }}
                </a>
            </div>
        </div>
    </section>

    {{-- Aggregate Stats Section --}}
    <section class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6 sm:gap-4">
        <div class="app-card app-card--gradient group space-y-1 p-5 text-center transition hover:border-amber-500/30">
            <p class="text-2xl font-black text-amber-400 sm:text-3xl lg:text-4xl">+10 {{ __('مليار') }}</p>
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('إجمالي الاستثمارات (ج.م)') }}</p>
        </div>
        <div class="app-card app-card--gradient group space-y-1 p-5 text-center transition hover:border-brand-500/30">
            <p class="text-2xl font-black text-brand-400 sm:text-3xl lg:text-4xl">+3,500</p>
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('وحدة سكنية وتجارية وطبية') }}</p>
        </div>
        <div class="app-card app-card--gradient group space-y-1 p-5 text-center transition hover:border-violet-500/30">
            <p class="text-2xl font-black text-violet-400 sm:text-3xl lg:text-4xl">4</p>
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('شركات تحالف كبرى') }}</p>
        </div>
        <div class="app-card app-card--gradient group space-y-1 p-5 text-center transition hover:border-emerald-500/30">
            <p class="text-2xl font-black text-emerald-400 sm:text-3xl lg:text-4xl">+72,000 {{ __('م²') }}</p>
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('مساحة أحدث المشروعات') }}</p>
        </div>
        <div class="app-card app-card--gradient group space-y-1 p-5 text-center transition hover:border-sky-500/30">
            <p class="text-2xl font-black text-sky-400 sm:text-3xl lg:text-4xl">+470 {{ __('فدان') }}</p>
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('استصلاح وتنمية زراعية') }}</p>
        </div>
        <div class="app-card app-card--gradient group space-y-1 p-5 text-center transition hover:border-pink-500/30">
            <p class="text-2xl font-black text-pink-400 sm:text-3xl lg:text-4xl">+9</p>
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ __('مدن ومحافظات بمصر') }}</p>
        </div>
    </section>

    {{-- Current Flagship Projects under Venecia Alliance --}}
    <section id="alliance-projects" class="space-y-8 scroll-mt-24">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-400">{{ __('المشروعات الجارية') }}</span>
                <h2 class="mt-1 text-2xl font-black text-white sm:text-3xl lg:text-4xl">
                    {{ __('المشاريع الحالية باسم تحالف فينيسيا') }}
                </h2>
                <p class="mt-2 text-sm text-slate-400 max-w-2xl">
                    {{ __('مشروعات استراتيجية كبرى أطلقتها شركة فينيسيا للتنمية والتخطيط العمراني رسمياً تجمع بين التميز الطبي والمجمعات السكنية التجارية الذكية.') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ __('قيد التطوير والتنفيذ') }}
                </span>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Project 1: Mixed-Use Assiut --}}
            <div class="app-card app-card--gradient relative flex flex-col justify-between overflow-hidden border border-white/10 p-6 sm:p-8 transition hover:border-brand-500/40">
                <div class="pointer-events-none absolute -top-24 -left-24 h-64 w-64 rounded-full bg-brand-500/15 blur-3xl"></div>
                <div class="space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/10 pb-4">
                        <span class="rounded-xl bg-brand-500/15 px-3 py-1 text-xs font-bold text-brand-300">
                            {{ __('سكني · إداري · تجاري مختلط') }}
                        </span>
                        <span class="text-xs font-semibold text-slate-400">{{ __('مدينة أسيوط الجديدة') }}</span>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-white sm:text-2xl">
                            {{ __('المشروع السكني الإداري التجاري المختلط بالمنطقة الإقليمية') }}
                        </h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-300">
                            {{ __('صرح عمراني واستثماري متكامل يُقام على مساحة ضخمة بمنطقة خدمات المنطقة الإقليمية بمدينة أسيوط الجديدة، يدمج بين السكن العصري والمراكز التجارية ومقرات الأعمال والمرافق الترفيهية لخدمة مجتمع الصعيد الجديد.') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 pt-2">
                        <div class="rounded-2xl border border-white/5 bg-white/[0.03] p-3.5">
                            <span class="block text-[10px] font-semibold uppercase text-slate-400">{{ __('المساحة الإجمالية') }}</span>
                            <strong class="mt-1 block text-lg font-extrabold text-white">70,230 {{ __('م²') }}</strong>
                            <span class="text-[11px] text-slate-500">(~16.7 {{ __('فدان') }})</span>
                        </div>
                        <div class="rounded-2xl border border-white/5 bg-white/[0.03] p-3.5">
                            <span class="block text-[10px] font-semibold uppercase text-slate-400">{{ __('التكلفة الاستثمارية') }}</span>
                            <strong class="mt-1 block text-lg font-extrabold text-brand-400">~3 {{ __('مليار جنيه') }}</strong>
                            <span class="text-[11px] text-slate-500">{{ __('استثمار مباشر') }}</span>
                        </div>
                        <div class="col-span-2 sm:col-span-1 rounded-2xl border border-white/5 bg-white/[0.03] p-3.5">
                            <span class="block text-[10px] font-semibold uppercase text-slate-400">{{ __('رقم قطعة الأرض') }}</span>
                            <strong class="mt-1 block text-sm font-bold text-slate-200">4-34, 3-34, 9-34, 8-34, 6-34, 5-34</strong>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/5 bg-slate-950/40 p-4 text-xs text-slate-400 space-y-1.5">
                        <div class="flex items-center gap-2 text-slate-300 font-semibold">
                            <svg class="h-4 w-4 text-brand-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z" stroke-width="1.8"/>
                                <circle cx="12" cy="10" r="3" stroke-width="1.8"/>
                            </svg>
                            <span>{{ __('الموقع الاستراتيجي:') }}</span>
                        </div>
                        <p class="pr-6 leading-relaxed">{{ __('منطقة خدمات المنطقة الإقليمية – مدينة أسيوط الجديدة – موقع حيوي يرتبط بشبكة المحاور الرئيسية ومجتمعات التنمية الجديدة.') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-white/10 pt-4">
                    <span class="text-xs font-semibold text-emerald-400 flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        {{ __('تحالف شركة فينيسيا للتنمية والتخطيط العمراني') }}
                    </span>
                    <a href="{{ route('public.contact') }}" class="app-button--ghost text-xs">
                        {{ __('طلب تفاصيل المشروع') }} →
                    </a>
                </div>
            </div>

            {{-- Project 2: Medical Project 6th of October --}}
            <div class="app-card app-card--gradient relative flex flex-col justify-between overflow-hidden border border-white/10 p-6 sm:p-8 transition hover:border-violet-500/40">
                <div class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rounded-full bg-violet-500/15 blur-3xl"></div>
                <div class="space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/10 pb-4">
                        <span class="rounded-xl bg-violet-500/15 px-3 py-1 text-xs font-bold text-violet-300">
                            {{ __('طبي تخصصي متكامل') }}
                        </span>
                        <span class="text-xs font-semibold text-slate-400">{{ __('مدينة 6 أكتوبر') }}</span>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-white sm:text-2xl">
                            {{ __('المشروع الطبي التخصصي – خلف مول العرب') }}
                        </h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-300">
                            {{ __('مجمع طبي ذكي ومتخصص يضم عيادات ومراكز تشخيصية وعلاجية بأحدث المعايير الطبية الدولية، يقع في أرقى مناطق الخدمات الاستراتيجية بالتوسعات الشمالية بمدينة 6 أكتوبر مباشرة خلف مول العرب.') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 pt-2">
                        <div class="rounded-2xl border border-white/5 bg-white/[0.03] p-3.5">
                            <span class="block text-[10px] font-semibold uppercase text-slate-400">{{ __('مساحة المشروع') }}</span>
                            <strong class="mt-1 block text-lg font-extrabold text-white">2,365 {{ __('م²') }}</strong>
                            <span class="text-[11px] text-slate-500">{{ __('مسطح متكامل') }}</span>
                        </div>
                        <div class="rounded-2xl border border-white/5 bg-white/[0.03] p-3.5">
                            <span class="block text-[10px] font-semibold uppercase text-slate-400">{{ __('التكلفة الاستثمارية') }}</span>
                            <strong class="mt-1 block text-lg font-extrabold text-violet-400">~400 {{ __('مليون جنيه') }}</strong>
                            <span class="text-[11px] text-slate-500">{{ __('استثمار مباشر') }}</span>
                        </div>
                        <div class="col-span-2 sm:col-span-1 rounded-2xl border border-white/5 bg-white/[0.03] p-3.5">
                            <span class="block text-[10px] font-semibold uppercase text-slate-400">{{ __('رقم قطعة الأرض') }}</span>
                            <strong class="mt-1 block text-base font-bold text-slate-200">{{ __('قطعة رقم (2)') }}</strong>
                            <span class="text-[11px] text-slate-500">{{ __('مركز الخدمات الرئيسي') }}</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/5 bg-slate-950/40 p-4 text-xs text-slate-400 space-y-1.5">
                        <div class="flex items-center gap-2 text-slate-300 font-semibold">
                            <svg class="h-4 w-4 text-violet-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z" stroke-width="1.8"/>
                                <circle cx="12" cy="10" r="3" stroke-width="1.8"/>
                            </svg>
                            <span>{{ __('الموقع الدقيق:') }}</span>
                        </div>
                        <p class="pr-6 leading-relaxed">{{ __('قطعة (2) بالمركز الرئيسي للخدمات – المنطقة العمرانية الأولى – التوسعات الشمالية – خلف مول العرب – مدينة 6 أكتوبر.') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-white/10 pt-4">
                    <span class="text-xs font-semibold text-violet-400 flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-violet-400"></span>
                        {{ __('تحالف شركة فينيسيا للتنمية والتخطيط العمراني') }}
                    </span>
                    <a href="{{ route('public.contact') }}" class="app-button--ghost text-xs">
                        {{ __('طلب تفاصيل المشروع') }} →
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- The 4 Founding Partners / Alliance Entities --}}
    <section class="space-y-8">
        <div>
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-amber-400">{{ __('قوة التحالف والشركاء') }}</span>
            <h2 class="mt-1 text-2xl font-black text-white sm:text-3xl lg:text-4xl">
                {{ __('سابقة أعمال الكيانات المؤسسة لتحالف فينيسيا') }}
            </h2>
            <p class="mt-2 text-sm text-slate-400 max-w-3xl">
                {{ __('تستند شركة فينيسيا إلى سجل حافل من الإنجازات والخبرات الهندسية والاستثمارية لشركائها الأربعة، الذين تركوا بصمات بارزة في كبرى محافظات ومدن الجمهورية.') }}
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Partner 1: Al-Adham --}}
            <div class="app-card app-card--gradient flex flex-col justify-between p-6 sm:p-8 transition hover:border-amber-500/30">
                <div class="space-y-5">
                    <div class="flex items-start justify-between gap-3 border-b border-white/10 pb-4">
                        <div>
                            <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-amber-400">{{ __('الكيان الشريك الأول') }}</span>
                            <h3 class="mt-1 text-xl font-bold text-white sm:text-2xl">{{ __('شركة الأدهم للاستثمار والتطوير العقاري') }}</h3>
                            <p class="mt-1 text-xs text-slate-400">
                                {{ __('المالك والمؤسس:') }} <strong class="text-slate-200">{{ __('السيد / محمد عبد السلام محمد الشافعي') }}</strong>
                            </p>
                        </div>
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-400 font-black text-sm">
                            01
                        </span>
                    </div>

                    <p class="text-sm leading-relaxed text-slate-300">
                        {{ __('أدارت ونفذت محفظة مشاريع سكنية وتجارية وخدمية متقدمة في مدن أسيوط الجديدة و6 أكتوبر ومحافظة الجيزة.') }}
                    </p>

                    <div class="space-y-3">
                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                                    {{ __('مول الخان بمدينة أسيوط الجديدة') }}
                                </h4>
                                <span class="badge badge-brand text-[11px]">+500 {{ __('مليون ج.م') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('مسطح في حدود 3,000 م² يضم أكثر من 120 وحدة تجارية وإدارية بتكلفة استثمارية تتعدى 500 مليون جنيه.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                                    {{ __('أكثر من 20 عمارة سكنية ومبانٍ متكاملة') }}
                                </h4>
                                <span class="badge badge-success text-[11px]">+1 {{ __('مليار ج.م') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('موزعة بين مدينة 6 أكتوبر، حي العجوزة، شارع البحر الأعظم بالجيزة، وأسيوط الجديدة (المقر الرئيسي)، بإجمالي يتجاوز 500 وحدة سكنية.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-2 border-t border-white/10 pt-4 text-xs text-slate-400">
                    <span>{{ __('المناطق:') }} <strong class="text-slate-300">{{ __('6 أكتوبر · العجوزة · البحر الأعظم · أسيوط الجديدة') }}</strong></span>
                    <span class="text-amber-400 font-bold">+1.5 {{ __('مليار جنيه استثمارات') }}</span>
                </div>
            </div>

            {{-- Partner 2: Al-Huda --}}
            <div class="app-card app-card--gradient flex flex-col justify-between p-6 sm:p-8 transition hover:border-brand-500/30">
                <div class="space-y-5">
                    <div class="flex items-start justify-between gap-3 border-b border-white/10 pb-4">
                        <div>
                            <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-brand-400">{{ __('الكيان الشريك الثاني') }}</span>
                            <h3 class="mt-1 text-xl font-bold text-white sm:text-2xl">{{ __('شركة الهدى للاستثمار والتطوير العقاري') }}</h3>
                            <p class="mt-1 text-xs text-slate-400">
                                {{ __('الشريك والمؤسس:') }} <strong class="text-slate-200">{{ __('السيد / أحمد عبد الإله أحمد عبد الإله') }}</strong>
                            </p>
                        </div>
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-brand-500/15 text-brand-400 font-black text-sm">
                            02
                        </span>
                    </div>

                    <p class="text-sm leading-relaxed text-slate-300">
                        {{ __('أدارت ونفذت مشروعات سكنية وتجارية وخدمية وتعليمية كبرى بقلب مدينة أسيوط ومحافظة القاهرة ومدينة أسيوط الجديدة.') }}
                    </p>

                    <div class="space-y-3">
                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-brand-400"></span>
                                    {{ __('مول الهدى بقلب مدينة أسيوط') }}
                                </h4>
                                <span class="badge badge-brand text-[11px]">+700 {{ __('مليون ج.م') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('أول مول تجاري إداري حديث بأسيوط، سلالم كهربائية، تكييف مركزي، +100 وحدة تجارية، 40 مكتب إداري وعيادة ومراكز تحاليل وأشعة.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-brand-400"></span>
                                    {{ __('+25 برج سكني وعمارة مكرم عبيد') }}
                                </h4>
                                <span class="badge badge-success text-[11px]">{{ __('سكني راقٍ') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('أكثر من 25 برجاً سكنياً بمدينة أسيوط وعمارة سكنية مميزة بشارع مكرم عبيد بمدينة نصر بالقاهرة.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-brand-400"></span>
                                    {{ __('مجمع مدارس طيبة الخاصة بأسيوط الجديدة') }}
                                </h4>
                                <span class="badge badge-brand text-[11px]">+250 {{ __('مليون ج.م') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('صرح تعليمي بحي رجال الأعمال على مسطح ~6,000 م² بتكلفة استثمارية تتعدى 250 مليون جنيه.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-2 border-t border-white/10 pt-4 text-xs text-slate-400">
                    <span>{{ __('المناطق:') }} <strong class="text-slate-300">{{ __('مدينة أسيوط · مدينة نصر (مكرم عبيد) · أسيوط الجديدة') }}</strong></span>
                    <span class="text-brand-400 font-bold">+1 {{ __('مليار جنيه استثمارات') }}</span>
                </div>
            </div>

            {{-- Partner 3: Al-Emam / Downtown --}}
            <div class="app-card app-card--gradient flex flex-col justify-between p-6 sm:p-8 transition hover:border-violet-500/30">
                <div class="space-y-5">
                    <div class="flex items-start justify-between gap-3 border-b border-white/10 pb-4">
                        <div>
                            <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-violet-400">{{ __('الكيان الشريك الثالث') }}</span>
                            <h3 class="mt-1 text-xl font-bold text-white sm:text-2xl">{{ __('شركة الإمام والداون تاون والرسالة') }}</h3>
                            <p class="mt-1 text-xs text-slate-400">
                                {{ __('المالك والمؤسس:') }} <strong class="text-slate-200">{{ __('السيد / حسين سمير إسماعيل إبراهيم') }}</strong>
                            </p>
                        </div>
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-violet-500/15 text-violet-400 font-black text-sm">
                            03
                        </span>
                    </div>

                    <p class="text-sm leading-relaxed text-slate-300">
                        {{ __('صاحب شركة الإمام للاستثمار والتطوير العقاري، وشريك بشركة الداون تاون للاستثمار والتطوير، وشريك بشركة الرسالة للخدمات التعليمية.') }}
                    </p>

                    <div class="space-y-3">
                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-violet-400"></span>
                                    {{ __('كمبوند فينيسيا العمراني المتكامل') }}
                                </h4>
                                <span class="badge badge-brand text-[11px]">{{ __('11.5 فدان') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('كمبوند سكني متكامل الخدمات يضم 440 وحدة سكنية راقية ومراكز خدمية وترفيهية.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-violet-400"></span>
                                    {{ __('أكثر من 7 مولات تجارية كبرى') }}
                                </h4>
                                <span class="badge badge-success text-[11px]">+60,000 {{ __('م²') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('مسطحات تجارية تتعدى 60 ألف م² تضم أكثر من 300 وحدة تجارية و 250 وحدة إدارية بمدينة أسيوط الجديدة.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-violet-400"></span>
                                    {{ __('استثمارات تعليمية وخدمية') }}
                                </h4>
                                <span class="badge badge-brand text-[11px]">{{ __('شركة الرسالة') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('شريك بشركة الرسالة للخدمات التعليمية ومشروعات خدمية متطورة بأسيوط الجديدة.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-2 border-t border-white/10 pt-4 text-xs text-slate-400">
                    <span>{{ __('المناطق:') }} <strong class="text-slate-300">{{ __('مدينة أسيوط الجديدة · التجمعات العمرانية الكبرى') }}</strong></span>
                    <span class="text-violet-400 font-bold">+4 {{ __('مليار جنيه استثمارات') }}</span>
                </div>
            </div>

            {{-- Partner 4: Roushdy --}}
            <div class="app-card app-card--gradient flex flex-col justify-between p-6 sm:p-8 transition hover:border-emerald-500/30">
                <div class="space-y-5">
                    <div class="flex items-start justify-between gap-3 border-b border-white/10 pb-4">
                        <div>
                            <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-emerald-400">{{ __('الكيان الشريك الرابع') }}</span>
                            <h3 class="mt-1 text-xl font-bold text-white sm:text-2xl">{{ __('شركة رشدي للاستثمار العقاري والاستشارات الهندسية') }}</h3>
                            <p class="mt-1 text-xs text-slate-400">
                                {{ __('المالك والمؤسس:') }} <strong class="text-slate-200">{{ __('السيد / أحمد رشدي توفيق') }}</strong>
                            </p>
                        </div>
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-400 font-black text-sm">
                            04
                        </span>
                    </div>

                    <p class="text-sm leading-relaxed text-slate-300">
                        {{ __('صرح عقاري وهندسي أدار ونفذ محفظة متنوعة من المشاريع الرائدة سكنياً وتعليمياً وطبياً وزراعياً في مختلف أنحاء الجمهورية.') }}
                    </p>

                    <div class="space-y-3">
                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                    {{ __('كمبوند زايد لاجونز (الشيخ زايد - الثورة الخضراء)') }}
                                </h4>
                                <span class="badge badge-brand text-[11px]">370 {{ __('وحدة') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('كمبوند سكني راقٍ بمدينة الشيخ زايد بمنطقة الثورة الخضراء يضم 370 وحدة سكنية.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                    {{ __('مشروعي إليت (22 فدان) والربوة (33 فدان)') }}
                                </h4>
                                <span class="badge badge-success text-[11px]">420 {{ __('وحدة') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('مشروع إليت (364 وحدة سكنية على 22 فدان) ومشروع الربوة (56 وحدة على 33 فدان).') }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                    {{ __('أبراج سكنية ومشروعات التجمع الخامس ومدينة نصر') }}
                                </h4>
                                <span class="badge badge-brand text-[11px]">+1,400 {{ __('وحدة') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('أبراج سكنية بأسيوط تتجاوز 1,000 وحدة سكنية، ومشروعات بالتجمع الخامس ومدينة نصر وعين شمس تتجاوز 400 وحدة.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-white/5 bg-white/[0.02] p-4 transition hover:bg-white/[0.04]">
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                    {{ __('مشروعات استصلاح وتنمية زراعية كبرى') }}
                                </h4>
                                <span class="badge badge-success text-[11px]">+470 {{ __('فدان') }}</span>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-400">
                                {{ __('مسطحات تتجاوز 470 فدان بغرب المنيا وأبو قرقاص والوادي الجديد.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-2 border-t border-white/10 pt-4 text-xs text-slate-400">
                    <span>{{ __('المناطق:') }} <strong class="text-slate-300">{{ __('الشيخ زايد · التجمع الخامس · مدينة نصر · أسيوط · المنيا') }}</strong></span>
                    <span class="text-emerald-400 font-bold">{{ __('تنوع سكني وطبي وزراعي') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Sector Diversification --}}
    <section class="space-y-8">
        <div class="text-center max-w-3xl mx-auto">
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-400">{{ __('تكامل التخصصات') }}</span>
            <h2 class="mt-1 text-2xl font-black text-white sm:text-3xl lg:text-4xl">
                {{ __('التنوع القطاعي لمحفظة أعمال التحالف') }}
            </h2>
            <p class="mt-2 text-sm text-slate-400">
                {{ __('تغطي مشروعات التحالف شتى القطاعات الحيوية بما يوفر قيمة مضافة مستدامة للمستثمرين والسكان على حد سواء.') }}
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="app-card app-card--gradient space-y-3 p-5 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500/15 text-brand-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/>
                    </svg>
                </span>
                <h3 class="text-base font-bold text-white">{{ __('القطاع السكني') }}</h3>
                <p class="text-xs text-slate-400 leading-relaxed">{{ __('كمبوندات مغلقة وأبراج فاخرة وأكثر من 2,500 وحدة سكنية بمعايير راقية.') }}</p>
            </div>

            <div class="app-card app-card--gradient space-y-3 p-5 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" stroke-width="1.8"/>
                        <path d="M3 6h18M16 10a4 4 0 0 1-8 0" stroke-width="1.8"/>
                    </svg>
                </span>
                <h3 class="text-base font-bold text-white">{{ __('التجاري والمولات') }}</h3>
                <p class="text-xs text-slate-400 leading-relaxed">{{ __('مولات حديثة ومسطحات تتعدى 70,000 م² تضم مئات المحال والماركات التجارية.') }}</p>
            </div>

            <div class="app-card app-card--gradient space-y-3 p-5 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-500/15 text-violet-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <h3 class="text-base font-bold text-white">{{ __('القطاع الطبي') }}</h3>
                <p class="text-xs text-slate-400 leading-relaxed">{{ __('مشروعات ومراكز طبية تخصصية بـ 6 أكتوبر وأسيوط بمعايير الرعاية الصحية الحديثة.') }}</p>
            </div>

            <div class="app-card app-card--gradient space-y-3 p-5 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-500/15 text-sky-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z" stroke-width="1.8"/>
                        <path d="M6 6h10M6 10h10" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <h3 class="text-base font-bold text-white">{{ __('القطاع التعليمي') }}</h3>
                <p class="text-xs text-slate-400 leading-relaxed">{{ __('صروح ومجمعات مدرسية كبرى (مجمع مدارس طيبة) واستثمارات تعليمية رائدة.') }}</p>
            </div>

            <div class="app-card app-card--gradient space-y-3 p-5 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 2a10 10 0 0 1 10 10c0 5.5-4.5 10-10 10S2 17.5 2 12A10 10 0 0 1 12 2Z" stroke-width="1.8"/>
                        <path d="M12 6v6l4 2" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <h3 class="text-base font-bold text-white">{{ __('التنمية الزراعية') }}</h3>
                <p class="text-xs text-slate-400 leading-relaxed">{{ __('استصلاح وتطوير أراضٍ زراعية تتعدى 470 فدان بالمنيا وأبو قرقاص والوادي الجديد.') }}</p>
            </div>
        </div>
    </section>

    {{-- Geographic Footprint --}}
    <section class="app-card app-card--gradient overflow-hidden p-6 sm:p-10 border border-white/10">
        <div class="grid gap-8 lg:grid-cols-[1fr_1.4fr] lg:items-center">
            <div class="space-y-4">
                <span class="text-xs font-bold uppercase tracking-[0.2em] text-brand-400">{{ __('خريطة التواجد والانتشار') }}</span>
                <h2 class="text-2xl font-black text-white sm:text-3xl">{{ __('تواجد استراتيجي يغطي أهم المحاور التنموية') }}</h2>
                <p class="text-sm leading-relaxed text-slate-300">
                    {{ __('يمتد نشاط تحالف فينيسيا عبر محافظات القاهرة الكبرى والجيزة ومحافظات الصعيد والمجتمعات العمرانية الجديدة، مستهدفين أفضل المواقع الحيوية ذات العائد الاستثماري العالي والقيمة المستقبلية.') }}
                </p>
                <div class="pt-2">
                    <a href="{{ route('public.projects.index') }}" class="app-button text-xs sm:text-sm">
                        {{ __('تصفح وحداتنا المتاحة حالياً') }} →
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/5 bg-slate-950/60 p-3.5">
                    <span class="block text-xs font-bold text-white">{{ __('مدينة 6 أكتوبر') }}</span>
                    <span class="mt-1 block text-[11px] text-slate-400">{{ __('التوسعات الشمالية ومول العرب') }}</span>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-950/60 p-3.5">
                    <span class="block text-xs font-bold text-white">{{ __('الشيخ زايد') }}</span>
                    <span class="mt-1 block text-[11px] text-slate-400">{{ __('منطقة الثورة الخضراء') }}</span>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-950/60 p-3.5">
                    <span class="block text-xs font-bold text-white">{{ __('القاهرة الجديدة') }}</span>
                    <span class="mt-1 block text-[11px] text-slate-400">{{ __('التجمع الخامس') }}</span>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-950/60 p-3.5">
                    <span class="block text-xs font-bold text-white">{{ __('محافظة الجيزة') }}</span>
                    <span class="mt-1 block text-[11px] text-slate-400">{{ __('العجوزة والبحر الأعظم') }}</span>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-950/60 p-3.5">
                    <span class="block text-xs font-bold text-white">{{ __('شرق القاهرة') }}</span>
                    <span class="mt-1 block text-[11px] text-slate-400">{{ __('مدينة نصر وعين شمس') }}</span>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-950/60 p-3.5">
                    <span class="block text-xs font-bold text-white">{{ __('مدينة أسيوط الجديدة') }}</span>
                    <span class="mt-1 block text-[11px] text-slate-400">{{ __('المنطقة الإقليمية ورجال الأعمال') }}</span>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-950/60 p-3.5">
                    <span class="block text-xs font-bold text-white">{{ __('قلب مدينة أسيوط') }}</span>
                    <span class="mt-1 block text-[11px] text-slate-400">{{ __('الأبراج السكنية ومول الهدى') }}</span>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-950/60 p-3.5">
                    <span class="block text-xs font-bold text-white">{{ __('محافظة المنيا') }}</span>
                    <span class="mt-1 block text-[11px] text-slate-400">{{ __('غرب المنيا وأبو قرقاص') }}</span>
                </div>
                <div class="rounded-2xl border border-white/5 bg-slate-950/60 p-3.5">
                    <span class="block text-xs font-bold text-white">{{ __('الوادي الجديد') }}</span>
                    <span class="mt-1 block text-[11px] text-slate-400">{{ __('مشروعات الاستصلاح الزراعي') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Official Document Download Section --}}
    <section class="relative overflow-hidden rounded-3xl border border-brand-500/30 bg-gradient-to-r from-brand-950/80 via-slate-900 to-violet-950/80 p-8 sm:p-12 shadow-2xl">
        <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="relative z-10 flex flex-col items-center justify-between gap-6 text-center md:flex-row md:text-start">
            <div class="space-y-3 max-w-2xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-brand-400/30 bg-brand-500/10 px-3.5 py-1 text-xs font-bold text-brand-300">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" stroke-width="1.8"/>
                    </svg>
                    {{ __('مستند رسمي معتمد') }}
                </span>
                <h3 class="text-2xl font-bold text-white sm:text-3xl">
                    {{ __('بيان بسابقة أعمال الشركة موسع (PDF)') }}
                </h3>
                <p class="text-sm text-slate-300 leading-relaxed">
                    {{ __('يمكنك استعراض وتحميل النسخة الرسمية الكاملة الموجهة لجهات الاستثمار والتعمير متضمنة جميع بيانات التحالف والمشروعات وتفاصيل السابقة المعتمدة.') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('public.portfolio.pdf') }}" target="_blank" class="app-button gap-2 px-6 py-3.5 text-sm shadow-xl shadow-brand-600/30">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ __('تحميل البيان بصيغة PDF') }}
                </a>
                <a href="{{ route('public.contact') }}" class="app-button--ghost gap-2 px-6 py-3.5 text-sm">
                    {{ __('تواصل مع الإدارة') }}
                </a>
            </div>
        </div>
    </section>

    {{-- Call to Action --}}
    <section class="rounded-3xl border border-white/10 bg-slate-900/60 px-6 py-12 text-center backdrop-blur-xl sm:py-16">
        <div class="mx-auto max-w-2xl space-y-4">
            <h3 class="text-2xl font-bold text-white sm:text-3xl">{{ __('انضم إلى مجتمعات فينيسيا اليوم') }}</h3>
            <p class="text-sm leading-relaxed text-slate-400">
                {{ __('نقدم لك خيارات سكنية وتجارية وطبية مدروسة بأنظمة سداد مباشرة وتقسيط مرن يصل حتى 5 سنوات بدون فوائد.') }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3 pt-3">
                <a href="{{ route('public.projects.index') }}" class="app-button">{{ __('استعراض الوحدات والمشاريع') }}</a>
                <a href="{{ route('installments.index') }}" class="app-button--ghost">{{ __('حاسبة الأقساط الذكية') }}</a>
                <a href="{{ route('public.contact') }}" class="app-button--ghost">{{ __('تواصل مع فريق المبيعات') }}</a>
            </div>
        </div>
    </section>
</div>
@endsection
