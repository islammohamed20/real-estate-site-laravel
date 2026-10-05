@extends('layouts.public')

@section('content')
    @if(!$hero || $hero->is_active)
        {{-- Hero Section --}}
    <section class="relative -mx-4 w-[calc(100%+2rem)] overflow-hidden bg-slate-950 text-center shadow-2xl lg:-mx-6 lg:w-[calc(100%+3rem)]">
        {{-- Promotional Banners Slider (replaces headline) --}}
        @include('public.partials.banner-slider', ['banners' => $banners ?? collect()])

        {{-- Hero body --}}
        <div class="relative z-10 overflow-hidden bg-gradient-to-b from-slate-950/0 via-slate-950/30 to-slate-950/50 px-6 py-12 dark:via-slate-950/70 dark:to-slate-950/90 sm:py-16">
            <div class="pointer-events-none absolute inset-0 bg-grid opacity-20"></div>
            <div class="pointer-events-none absolute -top-40 left-1/2 h-96 w-[600px] -translate-x-1/2 rounded-full bg-brand-500/20 blur-[120px]"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-24 h-72 w-72 rounded-full bg-violet-600/10 blur-[100px] animate-float-slow"></div>
            <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-brand-500/10 blur-[100px] animate-float-slower"></div>

            <div class="relative z-10 mx-auto max-w-4xl space-y-7">
                {{-- Brand Badge --}}
                <span class="inline-flex items-center gap-2.5 rounded-full border border-amber-400/30 bg-gradient-to-r from-amber-500/15 to-brand-600/15 px-6 py-2.5 text-xs font-bold text-amber-300 shadow-lg shadow-amber-500/10 backdrop-blur-md sm:text-sm">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg> {{ __('شركة فينسيا للاستثمار والتطوير العقاري') }}
                </span>

                {{-- Subtitle --}}
                <p class="text-balance mx-auto max-w-2xl text-base leading-relaxed text-slate-200 sm:text-lg sm:leading-9">
                    {{ $hero?->content ?? __('نبتكر حلولاُ معمارية متكاملة تدمج بين الرفاهية والسكن الراقي في أرقى المواقع الحيوية، مع أنظمة سداد مرنة وتقسيط مباشر يصل إلى 5 سنوات بدون فوائد.') }}
                </p>

                {{-- CTA buttons --}}
                <div class="flex flex-col items-center justify-center gap-3 pt-2 sm:flex-row">
                    <a href="{{ route('public.projects.index') }}" class="app-button w-full max-w-xs gap-2 px-8 py-3.5 text-sm shadow-lg shadow-brand-600/30 sm:w-auto">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z" stroke-width="1.8" stroke-linejoin="round"/></svg>
                        {{ __('استكشف المشاريع والوحدات') }}
                    </a>
                    <a href="{{ route('installments.index') }}" class="app-button app-button--ghost w-full max-w-xs gap-2 px-8 py-3.5 text-sm sm:w-auto">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="2" stroke-width="1.8"/><path d="M8 7h8M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 18.5h.01M12 18.5h.01M16 18.5h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                        {{ __('احسب خطتك بالتقسيط') }}
                    </a>
                </div>

                {{-- Stats Counters --}}
                <dl class="mx-auto mt-8 grid max-w-3xl grid-cols-2 gap-3 border-t border-white/10 pt-8 sm:grid-cols-4 sm:gap-4">
                    <div class="group/stat space-y-1.5 rounded-2xl border border-white/5 bg-white/[0.03] p-4 backdrop-blur-md transition-all duration-300 hover:border-brand-500/25 hover:bg-white/[0.06]">
                        <dt class="bg-gradient-to-r from-white to-slate-400 bg-clip-text text-3xl font-extrabold text-transparent sm:text-4xl">+20</dt>
                        <dd class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('عاماُ من الخبرة والتميز') }}</dd>
                    </div>
                    <div class="group/stat space-y-1.5 rounded-2xl border border-white/5 bg-white/[0.03] p-4 backdrop-blur-md transition-all duration-300 hover:border-brand-500/25 hover:bg-white/[0.06]">
                        <dt class="bg-gradient-to-r from-brand-400 to-brand-300 bg-clip-text text-3xl font-extrabold text-transparent sm:text-4xl">{{ $projectCount }}</dt>
                        <dd class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('مجتمعات سكنية وتجارية') }}</dd>
                    </div>
                    <div class="group/stat space-y-1.5 rounded-2xl border border-white/5 bg-white/[0.03] p-4 backdrop-blur-md transition-all duration-300 hover:border-brand-500/25 hover:bg-white/[0.06]">
                        <dt class="bg-gradient-to-r from-white to-slate-400 bg-clip-text text-3xl font-extrabold text-transparent sm:text-4xl">{{ $unitCount }}</dt>
                        <dd class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('وحدة سكنية وتجارية') }}</dd>
                    </div>
                    <div class="group/stat space-y-1.5 rounded-2xl border border-white/5 bg-white/[0.03] p-4 backdrop-blur-md transition-all duration-300 hover:border-emerald-500/25 hover:bg-white/[0.06]">
                        <dt class="bg-gradient-to-r from-emerald-400 to-emerald-300 bg-clip-text text-3xl font-extrabold text-transparent sm:text-4xl">{{ $availableUnitCount }}</dt>
                        <dd class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('متاحة للتعاقد المباشر') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>
    @endif

    @if(!$pillars || $pillars->is_active)
    {{-- Venecia Strategic Pillars / Values Section --}}
    <section class="mt-16">
        @if($pillars?->title || $pillars?->subtitle || $pillars?->content)
            <div class="mb-8 max-w-2xl">
                @if($pillars?->subtitle)
                    <p class="mobile-section-title">{{ $pillars->subtitle }}</p>
                @endif
                @if($pillars?->title)
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ $pillars->title }}</h2>
                @endif
                @if($pillars?->content)
                    <p class="mt-2 text-sm leading-relaxed text-slate-400 sm:text-base">{{ $pillars->content }}</p>
                @endif
            </div>
        @endif
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
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
                {{ __('Installment plans start with a :percent% down payment and offer payment terms up to 5 years.', ['percent' => number_format($defaultDownPaymentPercent, 0)]) }}
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
        </div>
    </section>
    @endif

    @if(!$projectsSection || $projectsSection->is_active)
    {{-- Featured Projects Showcase --}}
    <section class="mt-16">
        <div class="mb-8 flex items-end justify-between gap-4">
            <div>
                <p class="mobile-section-title">{{ $projectsSection?->subtitle ?? __('Our Developments') }}</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ $projectsSection?->title ?? __('Integrated Communities for Modern Living') }}</h2>
                @if($projectsSection?->content)
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-400">{{ $projectsSection->content }}</p>
                @endif
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
                    $projectStatus = match ($project->status) {
                        'active' => __('Active'),
                        'launching' => __('Launching'),
                        'sold' => __('Sold Out'),
                        default => __('Draft'),
                    };
                @endphp

                <article class="app-card card-hover group flex flex-col overflow-hidden p-0 transition-all duration-300">
                    <div class="relative h-48 w-full overflow-hidden bg-slate-900">
                        <img src="{{ $pImg }}" alt="{{ $project->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <div class="absolute top-3 right-3 flex flex-wrap gap-1.5">
                            <span class="badge badge-brand">{{ $projectStatus }}</span>
                            @if ($project->current_phase)
                                <span class="badge badge-success">{{ $project->current_phase }}</span>
                            @endif
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
    @endif

    {{-- Featured Available Units Showcase --}}
    @if ((isset($featuredUnitsByProject) && $featuredUnitsByProject->isNotEmpty()) && ($unitsSection === null || $unitsSection->is_active))
        <section class="mt-16" x-data="{ activeProject: '{{ $featuredUnitsByProject->first()?->first()?->project_id }}' }">
            <div class="mb-8 text-center">
                <p class="mobile-section-title">{{ $unitsSection?->subtitle ?? __('Featured opportunities') }}</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ $unitsSection?->title ?? __('Selected Units for Your Next Move') }}</h2>
                @if($unitsSection?->content)
                    <p class="mx-auto mt-2 max-w-2xl text-sm leading-relaxed text-slate-400">{{ $unitsSection->content }}</p>
                @endif
            </div>

            <div class="mb-8 flex flex-wrap items-center justify-center gap-2.5">
                @foreach ($featuredUnitsByProject as $projectUnits)
                    @php
                        $project = $projectUnits->first()->project;
                    @endphp
                    @if ($project)
                        <button type="button" @click="activeProject = '{{ $project->id }}'" :class="activeProject === '{{ $project->id }}' ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/25 ring-2 ring-brand-400/50' : 'bg-white/5 text-slate-300 hover:bg-white/10'" class="rounded-full border border-white/10 px-5 py-2.5 text-sm font-semibold transition-all">
                            {{ $project->name }}
                        </button>
                    @endif
                @endforeach
            </div>

            @foreach ($featuredUnitsByProject as $projectUnits)
                @if ($projectUnits->first()?->project)
                <div class="relative" x-data="{
                            scroller: null,
                            timer: null,
                            paused: false,
                            init() {
                                this.scroller = $el.querySelector('[data-scroller]');
                                this.start();
                                $el.addEventListener('mouseenter', () => this.pause());
                                $el.addEventListener('mouseleave', () => this.start());
                            },
                            start() {
                                this.paused = false;
                                if (this.timer) clearInterval(this.timer);
                                this.timer = setInterval(() => {
                                    if (this.paused || ! this.scroller) return;
                                    const max = this.scroller.scrollWidth - this.scroller.clientWidth;
                                    if (this.scroller.scrollLeft >= max - 1) {
                                        this.scroller.scrollTo({ left: 0, behavior: 'smooth' });
                                    } else {
                                        this.scroller.scrollBy({ left: 320, behavior: 'smooth' });
                                    }
                                }, 3500);
                            },
                            pause() { this.paused = true; },
                            next() { this.scroller && this.scroller.scrollBy({ left: 320, behavior: 'smooth' }); },
                            prev() { this.scroller && this.scroller.scrollBy({ left: -320, behavior: 'smooth' }); },
                         }">
                    <div x-show="activeProject === '{{ $projectUnits->first()->project->id }}'" x-cloak
                         data-scroller
                         class="flex snap-x snap-mandatory gap-5 overflow-x-auto px-1 pb-4 [scrollbar-width:thin] scroll-smooth">
                        @foreach ($projectUnits as $fUnit)
                @php
                    $uPrice = (float) $fUnit->current_price;
                    $uInstallmentYears = max(1, (int) ($fUnit->project?->max_installment_years ?? 5));
                    $uEstQuarterly = $uPrice > 0 ? round(($uPrice * 0.9) / ($uInstallmentYears * 4)) : 0;
                    $uDelivery = $fUnit->delivery_date?->year;

                    $unitImage = (is_string($fUnit->thumbnail) && $fUnit->thumbnail !== '')
                        ? $fUnit->thumbnail
                        : collect($fUnit->images ?? [])->filter(fn ($img) => is_string($img))->first();
                    $uImg = $unitImage
                        ? (filter_var($unitImage, FILTER_VALIDATE_URL)
                            ? $unitImage
                            : asset('storage/'.ltrim((string) preg_replace('#^/?storage/#', '', $unitImage), '/')))
                        : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';
                @endphp

                    <article class="app-card card-hover group flex w-[280px] shrink-0 snap-start flex-col overflow-hidden p-0 transition-all duration-300 sm:w-[340px]">
                        <div class="relative h-48 w-full overflow-hidden bg-slate-900">
                            <img src="{{ $uImg }}" alt="{{ __($fUnit->unit_type) }} {{ $fUnit->unit_number }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                            <div class="absolute top-3 right-3 flex gap-2">
                                <span class="rounded-lg bg-indigo-950/90 px-2.5 py-1 text-[11px] font-bold text-indigo-200 backdrop-blur-md border border-indigo-400/20">
                                    {{ __($fUnit->unit_type) }}
                                </span>
                                <span class="rounded-lg bg-amber-500/90 px-2.5 py-1 text-[11px] font-bold text-slate-950 backdrop-blur-md">
                                    {{ __('مميز ★') }}
                                </span>
                            </div>
                            <div class="absolute bottom-3 right-3 text-xs text-white">
                                <span class="rounded-lg bg-slate-900/80 px-2.5 py-1 font-medium backdrop-blur-md">
                                    {{ __('استلام:') }} <strong class="text-brand-300">{{ $uDelivery ?? __('Planned delivery is calculated from the contract date.') }}</strong>
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-5 space-y-3">
                            <p class="text-xs font-semibold text-brand-400">
                                {{ $projectUnits->first()->project->name ?? __('Featured projects') }}
                            </p>
                            <h3 class="text-lg font-bold text-white group-hover:text-brand-300 transition-colors">
                                <a href="{{ route('public.units.show', $fUnit->id) }}">
                                    {{ __($fUnit->unit_type) }} {{ __('نموذج') }} {{ $fUnit->unit_number }}
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
                                <a href="{{ route('public.units.show', $fUnit->id) }}" class="app-button--ghost text-xs justify-center py-2 min-h-9">
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
                    {{-- Auto-slider navigation arrows --}}
                    <button type="button" @click="prev()" class="absolute top-1/2 -translate-y-1/2 -start-2 z-10 flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-slate-900/90 text-white shadow-lg backdrop-blur-md transition hover:bg-brand-600/80" aria-label="{{ __('السابق') }}">
                        <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button type="button" @click="next()" class="absolute top-1/2 -translate-y-1/2 -end-2 z-10 flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-slate-900/90 text-white shadow-lg backdrop-blur-md transition hover:bg-brand-600/80" aria-label="{{ __('التالي') }}">
                        <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
                @endif
            @endforeach
        </section>
    @endif

    @if(!$calculator || $calculator->is_active)
    {{-- Venecia Calculator Banner --}}
    <section class="mt-16 overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-brand-900/40 via-slate-900 to-brand-950 p-8 text-center sm:p-12 shadow-2xl relative">
        <div class="pointer-events-none absolute inset-0 bg-grid opacity-20"></div>
        <div class="relative z-10 mx-auto max-w-2xl space-y-4">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-4 py-1 text-xs font-bold text-emerald-300">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="2" stroke-width="1.8"/><path d="M8 7h8M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 18.5h.01M12 18.5h.01M16 18.5h.01" stroke-width="1.8" stroke-linecap="round"/></svg> {{ $calculator?->subtitle ?? __('حاسبة الأقساط التفاعلية المباشرة') }}
            </span>
            <h2 class="text-2xl font-bold tracking-tight text-white sm:text-4xl">
                {{ $calculator?->title ?? __('احسب خطة التقسيط المناسبة لك في ثوانٍ') }}
            </h2>
            <p class="text-sm leading-relaxed text-slate-300 sm:text-base">
                {{ $calculator?->content ?? __('اختر مقدم التعاقد وسنوات السداد المناسبة لظروفك الاستثمارية مع معاينة حية للأقساط وإمكانية طباعة ملف PDF معتمد.') }}
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
    @endif
@endsection
