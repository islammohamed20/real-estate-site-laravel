@php
    $slides = $banners->map(fn ($banner) => [
        'title' => $banner->title,
        'subtitle' => $banner->subtitle,
        'image' => $banner->image_path ? asset('storage/'.$banner->image_path) : null,
        'link' => $banner->link_url,
    ])->filter(fn ($slide) => $slide['image'] !== null)->values();
    $effect = $banners->first()?->effect ?? 'fade';
    $duration = max(2, (int) ($banners->first()?->slide_duration ?? 5));
@endphp

@if ($slides->isNotEmpty())
    <section class="w-full" aria-label="{{ __('Promotional banners') }}">
        <div
            x-data="{
                count: {{ $slides->count() }},
                current: 0,
                effect: @js($effect),
                duration: {{ $duration }},
                timer: null,
                init() { this.play(); },
                play() { clearInterval(this.timer); this.timer = setInterval(() => this.next(), this.duration * 1000); },
                pause() { clearInterval(this.timer); },
                resume() { this.play(); },
                next() { this.current = (this.current + 1) % this.count; },
                prev() { this.current = (this.current - 1 + this.count) % this.count; },
                go(i) { this.current = i; },
            }"
            class="group relative w-full overflow-hidden bg-slate-950 shadow-2xl"
            @mouseenter="pause()"
            @mouseleave="resume()"
        >
            <div class="relative h-[480px] w-full sm:h-[560px] lg:h-[680px]">
                {{-- Slides --}}
                @foreach ($slides as $i => $slide)
                    <div
                        class="absolute inset-0 transition-all duration-1000 ease-in-out {{ $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                        :class="current === {{ $i }}
                            ? 'opacity-100 z-10'
                            : (effect === 'slide'
                                ? 'opacity-0 -translate-x-12 z-0'
                                : (effect === 'zoom' ? 'opacity-0 scale-110 z-0' : 'opacity-0 z-0'))"
                    >
                        <a href="{{ $slide['link'] ?: '#' }}" target="{{ $slide['link'] && str_starts_with($slide['link'], 'http') ? '_blank' : '' }}" rel="{{ $slide['link'] && str_starts_with($slide['link'], 'http') ? 'noopener noreferrer' : '' }}" class="block h-full w-full">
                            <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] ?? '' }}" class="hero-kenburns h-full w-full object-cover" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">

                            {{-- Layered cinematic overlays: subtle dark in light mode, strong dark in dark mode --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/15 to-transparent dark:from-slate-950/85 dark:via-slate-950/30 dark:to-slate-950/10"></div>
                            <div class="absolute inset-0 bg-gradient-to-r from-black/25 via-transparent to-black/25 dark:from-slate-950/40 dark:via-transparent dark:to-slate-950/40"></div>
                            <div class="pointer-events-none absolute -top-24 left-1/2 h-64 w-[520px] -translate-x-1/2 rounded-full bg-brand-500/20 blur-[110px]"></div>

                            {{-- Slide content --}}
                            <div class="absolute inset-x-4 bottom-16 z-10 space-y-4 text-center sm:inset-x-12 sm:bottom-20">
                                @if ($slide['title'])
                                    <h3 class="text-balance mx-auto max-w-3xl text-3xl font-extrabold leading-tight tracking-tight text-[#f8fafc] drop-shadow-2xl sm:text-5xl">
                                        {{ $slide['title'] }}
                                    </h3>
                                @endif
                                @if ($slide['subtitle'])
                                    <p class="mx-auto max-w-2xl text-sm leading-relaxed text-[#e2e8f0] drop-shadow-lg sm:text-lg">
                                        {{ $slide['subtitle'] }}
                                    </p>
                                @endif
                                @if ($slide['link'])
                                    <span class="mt-2 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-brand-600 to-violet-600 px-8 py-3.5 text-xs font-bold text-white shadow-2xl shadow-brand-600/40 transition-all duration-300 hover:scale-105 hover:shadow-brand-500/50 sm:text-sm">
                                        {{ __('View more') }}
                                        <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 12h14M13 6l6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach

                {{-- Always-visible glass arrows --}}
                <button type="button" @click="prev()" class="absolute top-1/2 left-3 z-30 -translate-y-1/2 flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-black/40 text-[#f8fafc] shadow-xl backdrop-blur-md transition-all duration-300 hover:scale-110 hover:bg-brand-600 hover:border-brand-500 dark:bg-slate-950/50 sm:left-5 sm:h-12 sm:w-12" aria-label="{{ __('Previous banner') }}">
                    <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15 18l-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button type="button" @click="next()" class="absolute top-1/2 right-3 z-30 -translate-y-1/2 flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-black/40 text-[#f8fafc] shadow-xl backdrop-blur-md transition-all duration-300 hover:scale-110 hover:bg-brand-600 hover:border-brand-500 dark:bg-slate-950/50 sm:right-5 sm:h-12 sm:w-12" aria-label="{{ __('Next banner') }}">
                    <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>

                {{-- Bottom bar: progress + dots + counter --}}
                <div class="absolute inset-x-0 bottom-0 z-30 flex flex-col">
                    {{-- Autoplay progress bar --}}
                    <div class="h-[3px] w-full bg-white/15">
                        <div
                            class="hero-progress-fill h-full rounded-r-full"
                            :key="current"
                            :style="'--hero-duration: ' + duration + 's'"
                        ></div>
                    </div>

                    <div class="flex items-center justify-between gap-4 bg-gradient-to-t from-black/70 to-transparent px-5 pb-5 pt-4 dark:from-slate-950/80 sm:px-8">
                        {{-- Dots --}}
                        <div class="flex items-center gap-2">
                            @foreach ($slides as $i => $slide)
                                <button type="button" @click="go({{ $i }})" class="h-2 rounded-full transition-all duration-300" :class="current === {{ $i }} ? 'w-8 bg-brand-400 shadow-[0_0_12px_rgb(var(--brand-500)/0.8)]' : 'w-2 bg-white/40 hover:bg-white/70'" :aria-label="'{{ __('Go to banner') }} {{ $i + 1 }}'"></button>
                            @endforeach
                        </div>

                        {{-- Counter --}}
                        <span class="rounded-full border border-white/15 bg-black/50 px-3.5 py-1.5 text-[11px] font-bold tabular-nums text-[#f8fafc]/80 backdrop-blur-md dark:bg-slate-950/50">
                            <span x-text="current + 1"></span> / {{ $slides->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
