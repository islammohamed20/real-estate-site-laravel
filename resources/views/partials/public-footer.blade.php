@php($companyProfile = \App\Models\CompanyProfile::query()->first())
<footer class="relative mt-20 overflow-hidden border-t border-white/10 bg-slate-950/90">
    {{-- Decorative gradient line + ambient glows --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-brand-500/70 to-transparent"></div>
    <div class="pointer-events-none absolute -top-28 start-1/4 h-72 w-72 rounded-full bg-brand-600/10 blur-[110px]"></div>
    <div class="pointer-events-none absolute -bottom-28 end-1/4 h-72 w-72 rounded-full bg-violet-600/10 blur-[110px]"></div>

    <div class="relative mx-auto w-full max-w-7xl px-4 pt-14 pb-8 lg:px-6">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Brand --}}
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    @if ($companyProfile?->logo_light_path || $companyProfile?->logo_path)
                        <img src="{{ asset($companyProfile->logo_light_path ?? $companyProfile->logo_path) }}" alt="{{ $companyProfile?->name ?? config('app.name') }}" class="h-11 w-auto object-contain">
                    @else
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 via-indigo-600 to-violet-600 shadow-lg shadow-brand-500/30">
                            <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z" stroke-width="1.8" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    @endif
                    <span class="leading-tight">
                        <strong class="block text-lg font-bold text-white">{{ $companyProfile?->name ?? 'Venecia Developments' }}</strong>
                        <span class="block text-xs text-brand-300">{{ __('Real estate management system') }}</span>
                    </span>
                </a>
                <p class="max-w-sm text-sm leading-relaxed text-slate-400">
                    {{ __('رواد في صناعة التطوير العقاري والمجتمعات السكنية الفاخرة. نقدم حلولا معمارية متكاملة وتصاميم أيقونية بأرقى المواقع وأسهل أنظمة السداد.') }}
                </p>

                {{-- Social / contact icons --}}
                <div class="flex items-center gap-2 pt-1">
                    @if ($companyProfile?->phone)
                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $companyProfile->phone) }}" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:border-emerald-500/40 hover:bg-emerald-500/10 hover:text-emerald-300" title="{{ __('WhatsApp') }}">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                        </a>
                    @endif
                    @if ($companyProfile?->facebook_url)
                        <a href="{{ $companyProfile->facebook_url }}" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:border-blue-500/40 hover:bg-blue-500/10 hover:text-blue-300" title="{{ __('Facebook') }}">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 3.667H9.101V23.691h.001z"/></svg>
                        </a>
                    @endif
                    @if ($companyProfile?->instagram_url)
                        <a href="{{ $companyProfile->instagram_url }}" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:border-pink-500/40 hover:bg-pink-500/10 hover:text-pink-300" title="{{ __('Instagram') }}">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                        </a>
                    @endif
                    @if ($companyProfile?->email)
                        <a href="mailto:{{ $companyProfile->email }}" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:border-brand-500/40 hover:bg-brand-500/10 hover:text-brand-300" title="{{ __('Email') }}">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    @endif
                    @if ($companyProfile?->website)
                        <a href="{{ $companyProfile->website }}" target="_blank" rel="noopener noreferrer" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-300 transition hover:border-sky-500/40 hover:bg-sky-500/10 hover:text-sky-300" title="{{ __('Website') }}">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" stroke-linecap="round"/></svg>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Quick links --}}
            <div class="space-y-3">
                <p class="text-sm font-bold uppercase tracking-wider text-slate-200">{{ __('Quick Links') }}</p>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    <li>
                        <a href="{{ route('public.projects.index') }}" class="group inline-flex items-center gap-1.5 transition hover:text-white">
                            <svg class="h-3.5 w-3.5 text-brand-400 transition-transform group-hover:-translate-x-0.5 rtl:rotate-180 rtl:group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 18 6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('المشاريع والوحدات المتاحة') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('installments.index') }}" class="group inline-flex items-center gap-1.5 transition hover:text-white">
                            <svg class="h-3.5 w-3.5 text-brand-400 transition-transform group-hover:-translate-x-0.5 rtl:rotate-180 rtl:group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 18 6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('حاسبة الأقساط') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.about') }}" class="group inline-flex items-center gap-1.5 transition hover:text-white">
                            <svg class="h-3.5 w-3.5 text-brand-400 transition-transform group-hover:-translate-x-0.5 rtl:rotate-180 rtl:group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 18 6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('من نحن') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.contact') }}" class="group inline-flex items-center gap-1.5 transition hover:text-white">
                            <svg class="h-3.5 w-3.5 text-brand-400 transition-transform group-hover:-translate-x-0.5 rtl:rotate-180 rtl:group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 18 6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('اتصل بنا') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}" class="group inline-flex items-center gap-1.5 transition hover:text-white">
                            <svg class="h-3.5 w-3.5 text-brand-400 transition-transform group-hover:-translate-x-0.5 rtl:rotate-180 rtl:group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m9 18 6-6-6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ __('بوابة مبيعات الشركة') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Contact --}}
            <div class="space-y-4">
                <p class="text-sm font-bold uppercase tracking-wider text-slate-200">{{ __('تواصل معنا') }}</p>
                <p class="text-sm leading-relaxed text-slate-400">
                    {{ __('احسب خطة سدادك بنفسك أو تواصل مع مسؤولي المبيعات للحصول على استشارة عقارية مخصصة.') }}
                </p>
                <ul class="space-y-3 text-sm text-slate-400">
                    @if ($companyProfile?->address)
                        <li class="flex items-start gap-2.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>{{ $companyProfile->address }}</span>
                        </li>
                    @endif
                    @if ($companyProfile?->phone)
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <a href="tel:{{ $companyProfile->phone }}" dir="ltr" class="transition hover:text-white">{{ $companyProfile->phone }}</a>
                        </li>
                    @endif
                    @if ($companyProfile?->email)
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <a href="mailto:{{ $companyProfile->email }}" class="transition hover:text-white">{{ $companyProfile->email }}</a>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- CTA --}}
            <div class="space-y-4">
                <p class="text-sm font-bold uppercase tracking-wider text-slate-200">{{ __('ابدأ الآن') }}</p>
                <div class="relative overflow-hidden rounded-3xl border border-brand-500/25 bg-gradient-to-br from-brand-900/50 via-slate-900 to-brand-950 p-5">
                    <div class="pointer-events-none absolute -end-8 -top-8 h-24 w-24 rounded-full bg-brand-500/20 blur-2xl"></div>
                    <svg class="h-8 w-8 text-brand-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="5" y="3" width="14" height="18" rx="2" stroke-width="1.8"/><path d="M8 7h8M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 18.5h.01M12 18.5h.01M16 18.5h.01" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <h3 class="mt-3 text-base font-bold text-white">{{ __('احسب خطة سدادك') }}</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-400">{{ __('خطة تقسيط مباشرة تبدأ بمقدم 10% وسداد يصل إلى 8 سنوات.') }}</p>
                    <a href="{{ route('installments.index') }}" class="app-button mt-4 w-full justify-center text-xs py-2.5">
                        {{ __('افتح الحاسبة') }} ←
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/10 pt-6 sm:flex-row">
            <p class="text-xs text-slate-500">
                © {{ date('Y') }} <strong class="text-slate-300">{{ $companyProfile?->name ?? 'Venecia Developments' }}</strong> — {{ __('All rights reserved.') }}
            </p>
            <p class="flex items-center gap-1.5 text-xs text-slate-500">
                <svg class="h-3.5 w-3.5 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                {{ __('صُنِعَ لأعلى مستويات الفخامة والتميز.') }}
            </p>
        </div>
    </div>
</footer>
