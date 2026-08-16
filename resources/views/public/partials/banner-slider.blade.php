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
    <section class="mt-6" aria-label="{{ __('Promotional banners') }}">
        <div
            x-data="{
                slides: @js($slides->all()),
                current: 0,
                effect: @js($effect),
                duration: {{ $duration * 1000 }},
                timer: null,
                init() { this.play(); },
                play() { clearInterval(this.timer); this.timer = setInterval(() => this.next(), this.duration); },
                pause() { clearInterval(this.timer); },
                resume() { this.play(); },
                next() { this.current = (this.current + 1) % this.slides.length; },
                prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length; },
                go(i) { this.current = i; },
            }"
            class="group relative overflow-hidden rounded-3xl border border-white/10 bg-slate-900 shadow-2xl"
            @mouseenter="pause()"
            @mouseleave="resume()"
        >
            <div class="relative h-64 sm:h-80 lg:h-96">
                <template x-for="(slide, i) in slides" :key="i">
                    <div
                        class="absolute inset-0 transition-all duration-700 ease-in-out"
                        :class="current === i
                            ? 'opacity-100 translate-x-0 scale-100'
                            : (effect === 'slide'
                                ? 'opacity-0 -translate-x-10'
                                : (effect === 'zoom' ? 'opacity-0 scale-90' : 'opacity-0'))"
                    >
                        <a :href="slide.link || '#'" :target="slide.link && slide.link.startsWith('http') ? '_blank' : ''" :rel="slide.link && slide.link.startsWith('http') ? 'noopener noreferrer' : ''">
                            <img :src="slide.image" :alt="slide.title || ''" class="h-full w-full object-cover" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/30 to-transparent"></div>
                            <div class="absolute inset-x-6 bottom-6 space-y-2 text-center sm:inset-x-12 sm:bottom-10">
                                <template x-if="slide.title">
                                    <h3 class="text-2xl font-extrabold tracking-tight text-white drop-shadow-lg sm:text-4xl" x-text="slide.title"></h3>
                                </template>
                                <template x-if="slide.subtitle">
                                    <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-200 sm:text-base" x-text="slide.subtitle"></p>
                                </template>
                                <template x-if="slide.link">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-brand-600 px-6 py-2.5 text-xs font-bold text-white shadow-lg shadow-brand-600/30 hover:bg-brand-500 transition mt-3">
                                        {{ __('View more') }} <span aria-hidden="true">←</span>
                                    </span>
                                </template>
                            </div>
                        </a>
                    </div>
                </template>

                {{-- Arrows --}}
                <button type="button" @click="prev()" class="absolute top-1/2 left-3 z-10 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-slate-950/70 text-white opacity-0 backdrop-blur transition group-hover:opacity-100 hover:bg-brand-600" aria-label="{{ __('Previous banner') }}">
                    <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M15 18l-6-6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button type="button" @click="next()" class="absolute top-1/2 right-3 z-10 -translate-y-1/2 flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-slate-950/70 text-white opacity-0 backdrop-blur transition group-hover:opacity-100 hover:bg-brand-600" aria-label="{{ __('Next banner') }}">
                    <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 18l6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>

                {{-- Dots --}}
                <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 items-center gap-2">
                    <template x-for="(slide, i) in slides" :key="'dot' + i">
                        <button type="button" @click="go(i)" class="h-2 rounded-full transition-all duration-300" :class="current === i ? 'w-6 bg-brand-500' : 'w-2 bg-white/30 hover:bg-white/60'" :aria-label="'{{ __('Go to banner') }} ' + (i + 1)"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>
@endif
