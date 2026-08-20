@php($images = collect($images ?? [])->filter()->values()->all())
@if (count($images) > 0)
    <section class="public-gallery min-w-0" data-gallery data-images="{{ json_encode($images, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}" data-alt="{{ $alt ?? '' }}">

        <div class="app-card min-w-0 overflow-hidden p-4 sm:p-6">
            <div class="mb-5 flex items-end justify-between gap-3 px-1">
                <div class="min-w-0">
                    <p class="mobile-section-title">{{ $eyebrow ?? __('معرض الصور') }}</p>
                    <h2 class="mt-2 truncate text-2xl font-bold tracking-tight text-white">{{ $title ?? __('صور') }}</h2>
                </div>
                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-400">
                    <svg class="h-4 w-4 text-brand-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 8V5a1 1 0 0 1 1-1h3M16 4h3a1 1 0 0 1 1 1v3M20 16v3a1 1 0 0 1-1 1h-3M8 20H5a1 1 0 0 1-1-1v-3" stroke-linecap="round"/><circle cx="12" cy="12" r="3"/></svg>
                    {{ count($images) }} {{ __('صور') }}
                </span>
            </div>

            <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-slate-950/70 shadow-2xl">
                <button type="button" class="public-gallery__main group relative block h-[270px] w-full cursor-zoom-in overflow-hidden sm:h-[390px] lg:h-[430px]" data-gallery-open aria-label="{{ __('تكبير الصورة') }}">
                    <img src="{{ $images[0] }}" alt="{{ $alt ?? '' }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]" loading="eager">
                    <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-slate-950/20"></span>
                    <span class="pointer-events-none absolute bottom-4 end-4 z-10 flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-black/35 text-white backdrop-blur-md transition group-hover:scale-105 group-hover:bg-brand-600/80">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3" stroke-linecap="round"/></svg>
                    </span>
                    <span class="pointer-events-none absolute bottom-4 start-4 z-10 rounded-full border border-white/15 bg-black/35 px-3 py-1.5 text-xs font-bold text-white backdrop-blur-md"><span data-gallery-counter>1</span> / {{ count($images) }}</span>
                </button>
            </div>

            <div class="mt-3 flex items-center gap-2">
                <button type="button" data-gallery-prev class="flex h-10 w-9 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10 hover:text-white sm:h-12 sm:w-10" aria-label="{{ __('السابق') }}">
                    <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="flex min-w-0 flex-1 gap-2 overflow-x-auto pb-1 [scrollbar-width:thin]">
                    @foreach ($images as $index => $image)
                        <button type="button" data-gallery-thumb="{{ $index }}" class="relative h-16 w-24 shrink-0 overflow-hidden rounded-xl border-2 transition sm:h-20 sm:w-28 {{ $index === 0 ? 'border-brand-400 ring-2 ring-brand-500/25' : 'border-transparent opacity-60 hover:border-white/30 hover:opacity-100' }}" aria-label="{{ __('الصورة') }} {{ $index + 1 }}">
                            <img src="{{ $image }}" alt="{{ $alt ?? '' }}" class="h-full w-full object-cover" loading="lazy">
                            <span data-gallery-active-indicator class="absolute inset-x-0 bottom-0 h-1 bg-brand-400 {{ $index === 0 ? '' : 'hidden' }}"></span>
                        </button>
                    @endforeach
                </div>
                <button type="button" data-gallery-next class="flex h-10 w-9 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10 hover:text-white sm:h-12 sm:w-10" aria-label="{{ __('التالي') }}">
                    <svg class="h-5 w-5 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>

        {{-- Lightbox — SIBLING of the card (NOT inside .app-card) so position:fixed covers the whole viewport.
             .app-card has backdrop-blur which creates a containing block for fixed descendants. --}}
        <div data-gallery-modal class="fixed inset-0 z-[9999] hidden" role="dialog" aria-modal="true" aria-label="{{ $title ?? __('صور') }}">
            <div data-gallery-backdrop class="absolute inset-0 bg-black/95 backdrop-blur-xl shadow-[inset_0_0_140px_rgba(0,0,0,0.95)]"></div>

            <div class="relative z-10 flex h-full w-full flex-col p-3 sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1.5 text-xs font-bold text-white backdrop-blur-md"><span data-gallery-modal-counter>1</span> / {{ count($images) }}</span>
                    <button type="button" data-gallery-close class="flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white shadow-lg shadow-black/40 transition hover:scale-105 hover:bg-white/20 hover:text-brand-300" aria-label="{{ __('إغلاق') }}">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <div class="relative flex min-h-0 flex-1 items-center justify-center py-4 sm:py-6">
                    <button type="button" data-gallery-modal-prev class="absolute start-1 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white shadow-lg shadow-black/40 transition hover:scale-105 hover:bg-brand-600/70 sm:start-4" aria-label="{{ __('السابق') }}">
                        <svg class="h-6 w-6 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button type="button" data-gallery-modal-next class="absolute end-1 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white shadow-lg shadow-black/40 transition hover:scale-105 hover:bg-brand-600/70 sm:end-4" aria-label="{{ __('التالي') }}">
                        <svg class="h-6 w-6 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 18 6-6-6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <img data-gallery-modal-image src="{{ $images[0] }}" alt="{{ $alt ?? '' }}" class="max-h-full max-w-[calc(100vw-5rem)] rounded-2xl object-contain shadow-2xl shadow-black/60 sm:max-w-[calc(100vw-9rem)]">
                </div>

                <div class="flex justify-center gap-2 overflow-x-auto pb-2">
                    @foreach ($images as $index => $image)
                        <button type="button" data-gallery-modal-thumb="{{ $index }}" class="h-12 w-16 shrink-0 overflow-hidden rounded-lg border-2 transition {{ $index === 0 ? 'border-brand-400' : 'border-transparent opacity-50 hover:opacity-100' }}" aria-label="{{ __('الصورة') }} {{ $index + 1 }}">
                            <img src="{{ $image }}" alt="{{ $alt ?? '' }}" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (() => {
                const initGallery = (gallery) => {
                    if (gallery.dataset.galleryReady) return;
                    gallery.dataset.galleryReady = 'true';
                    const images = JSON.parse(gallery.dataset.images || '[]');
                    const alt = gallery.dataset.alt || '';
                    const main = gallery.querySelector('.public-gallery__main img');
                    const counter = gallery.querySelector('[data-gallery-counter]');
                    const thumbs = [...gallery.querySelectorAll('[data-gallery-thumb]')];
                    const modal = gallery.querySelector('[data-gallery-modal]');
                    const modalImage = gallery.querySelector('[data-gallery-modal-image]');
                    const modalCounter = gallery.querySelector('[data-gallery-modal-counter]');
                    const modalThumbs = [...gallery.querySelectorAll('[data-gallery-modal-thumb]')];
                    let active = 0;
                    let modalIndex = 0;

                    const syncThumbStyles = (selectedIndex) => {
                        thumbs.forEach((thumb, i) => {
                            thumb.classList.toggle('border-brand-400', i === selectedIndex);
                            thumb.classList.toggle('ring-2', i === selectedIndex);
                            thumb.classList.toggle('ring-brand-500/25', i === selectedIndex);
                            thumb.classList.toggle('border-transparent', i !== selectedIndex);
                            thumb.classList.toggle('opacity-60', i !== selectedIndex);
                            thumb.querySelector('[data-gallery-active-indicator]').classList.toggle('hidden', i !== selectedIndex);
                        });
                    };

                    const syncModalThumbStyles = (selectedIndex) => {
                        modalThumbs.forEach((thumb, i) => {
                            thumb.classList.toggle('border-brand-400', i === selectedIndex);
                            thumb.classList.toggle('border-transparent', i !== selectedIndex);
                            thumb.classList.toggle('opacity-50', i !== selectedIndex);
                            thumb.classList.toggle('hover:opacity-100', i !== selectedIndex);
                        });
                    };

                    const setMain = (index) => {
                        active = (index + images.length) % images.length;
                        main.src = images[active];
                        main.alt = alt;
                        counter.textContent = active + 1;
                        syncThumbStyles(active);
                    };

                    const setModalImage = (index) => {
                        modalIndex = (index + images.length) % images.length;
                        modalImage.src = images[modalIndex];
                        modalImage.alt = alt;
                        modalCounter.textContent = modalIndex + 1;
                        syncModalThumbStyles(modalIndex);
                    };

                    const openModal = (index) => {
                        setModalImage(index);
                        // Portal: move the modal to <body> so position:fixed is relative
                        // to the viewport (ancestors with transform/backdrop-filter
                        // otherwise become containing blocks and clip the overlay).
                        if (modal.parentElement !== document.body) {
                            document.body.appendChild(modal);
                        }
                        modal.classList.remove('hidden');
                        document.body.classList.add('overflow-hidden');
                    };

                    const closeModal = () => {
                        modal.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    };

                    thumbs.forEach((thumb) => {
                        thumb.addEventListener('click', () => openModal(Number(thumb.dataset.galleryThumb)));
                    });

                    modalThumbs.forEach((thumb) => {
                        thumb.addEventListener('click', () => setModalImage(Number(thumb.dataset.galleryModalThumb)));
                    });

                    gallery.querySelector('[data-gallery-prev]').addEventListener('click', (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        setMain(active - 1);
                    });

                    gallery.querySelector('[data-gallery-next]').addEventListener('click', (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        setMain(active + 1);
                    });

                    gallery.querySelector('[data-gallery-open]').addEventListener('click', (event) => {
                        event.preventDefault();
                        openModal(active);
                    });

                    gallery.querySelector('[data-gallery-close]').addEventListener('click', closeModal);
                    gallery.querySelector('[data-gallery-modal-prev]').addEventListener('click', () => setModalImage(modalIndex - 1));
                    gallery.querySelector('[data-gallery-modal-next]').addEventListener('click', () => setModalImage(modalIndex + 1));

                    // Clicking anywhere outside the image closes the lightbox,
                    // while interactive controls (arrows, thumbnails, close) keep working.
                    modal.addEventListener('click', (event) => {
                        if (event.target.closest('button')) return;
                        const image = modal.querySelector('[data-gallery-modal-image]');
                        if (event.target === image || image.contains(event.target)) return;
                        closeModal();
                    });
                    document.addEventListener('keydown', (event) => {
                        if (modal.classList.contains('hidden')) return;
                        if (event.key === 'Escape') closeModal();
                        if (event.key === 'ArrowLeft') setModalImage(modalIndex - 1);
                        if (event.key === 'ArrowRight') setModalImage(modalIndex + 1);
                    });
                };
                document.querySelectorAll('[data-gallery]').forEach(initGallery);
            })();
        </script>
    @endpush
@endif
