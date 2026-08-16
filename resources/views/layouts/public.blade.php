<!doctype html>
@php
    $companyProfile = \App\Models\CompanyProfile::first();
    $isCustomerAuth = auth('customer')->check();
    $isAdminAuth = auth()->check();
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0b1120">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="{{ $companyProfile?->favicon_path ?? '/icons/icon-maskable.svg' }}">
    <link rel="apple-touch-icon" href="{{ $companyProfile?->favicon_path ?? '/icons/icon-maskable.svg' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>{{ $title ?? config('app.name') }}</title>
    <script>
        (function () {
            try {
                const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                if (theme === 'dark') document.documentElement.classList.add('dark');
                document.querySelector('meta[name="theme-color"]').setAttribute('content', theme === 'dark' ? '#0b1120' : '#f8fafc');
            } catch (e) {}
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-950 text-slate-100" x-data="{ menuOpen: false }">
    @php($currentRoute = request()->route()?->getName())

    {{-- Professional 1.5s splash loader: homepage only, once per 24h (localStorage) --}}
    @if ($currentRoute === 'home')
    <div id="app-loader" aria-hidden="true">
        <style>
            #app-loader{position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;background:rgb(var(--slate-950));overflow:hidden;transition:opacity .5s ease,visibility .5s ease}
            #app-loader.app-loader--done{opacity:0;visibility:hidden;pointer-events:none}
            .app-loader__orb{position:absolute;border-radius:9999px;filter:blur(70px);opacity:.45}
            .app-loader__orb--1{top:-60px;right:-40px;width:280px;height:280px;background:rgb(var(--brand-600)/.55);animation:app-loader-float-a 7s ease-in-out infinite}
            .app-loader__orb--2{bottom:-70px;left:-50px;width:300px;height:300px;background:rgb(var(--violet-600)/.5);animation:app-loader-float-b 9s ease-in-out infinite}
            .app-loader__orb--3{top:45%;left:55%;width:180px;height:180px;background:rgb(var(--sky-500)/.4);animation:app-loader-float-a 11s ease-in-out infinite}
            .app-loader__grid{position:absolute;inset:0;background-image:linear-gradient(to right,rgb(var(--slate-400)/.05) 1px,transparent 1px),linear-gradient(to bottom,rgb(var(--slate-400)/.05) 1px,transparent 1px);background-size:56px 56px;mask-image:radial-gradient(ellipse 80% 60% at 50% 40%,black 25%,transparent 100%);-webkit-mask-image:radial-gradient(ellipse 80% 60% at 50% 40%,black 25%,transparent 100%)}
            .app-loader__inner{position:relative;display:flex;flex-direction:column;align-items:center;gap:22px;padding:0 24px}
            .app-loader__ring{position:relative;width:104px;height:104px;display:flex;align-items:center;justify-content:center}
            .app-loader__ring::before{content:'';position:absolute;inset:0;border-radius:9999px;background:conic-gradient(from 0deg,transparent 8%,rgb(var(--brand-500)/.95) 32%,transparent 62%,rgb(var(--violet-500)/.9) 88%,transparent 100%);animation:app-loader-spin 1.05s linear infinite;-webkit-mask:radial-gradient(farthest-side,transparent calc(100% - 5px),#000 calc(100% - 4px));mask:radial-gradient(farthest-side,transparent calc(100% - 5px),#000 calc(100% - 4px))}
            .app-loader__ring::after{content:'';position:absolute;inset:0;border-radius:9999px;border:1px solid rgb(var(--white)/.08)}
            .app-loader__logo{position:relative;z-index:1;display:flex;align-items:center;justify-content:center;animation:app-loader-pulse 1.5s ease-in-out infinite}
            .app-loader__bar{width:190px;height:4px;border-radius:9999px;background:rgb(var(--white)/.08);overflow:hidden}
            .app-loader__bar>span{display:block;height:100%;width:0;border-radius:9999px;background:linear-gradient(90deg,rgb(var(--brand-500)),rgb(var(--violet-500)));animation:app-loader-fill 1.15s cubic-bezier(.65,.05,.36,1) forwards}
            .app-loader__label{font-size:11px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:rgb(var(--slate-400))}
            @keyframes app-loader-spin{to{transform:rotate(360deg)}}
            @keyframes app-loader-pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.07)}}
            @keyframes app-loader-fill{from{width:0}to{width:100%}}
            @keyframes app-loader-float-a{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(-30px,26px) scale(1.1)}}
            @keyframes app-loader-float-b{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(28px,-22px) scale(1.08)}}
            @media (prefers-reduced-motion:reduce){#app-loader .app-loader__ring::before,.app-loader__orb,.app-loader__logo,.app-loader__bar>span{animation:none!important}.app-loader__bar>span{width:100%}}
        </style>
        <div class="app-loader__orb app-loader__orb--1"></div>
        <div class="app-loader__orb app-loader__orb--2"></div>
        <div class="app-loader__orb app-loader__orb--3"></div>
        <div class="app-loader__grid"></div>
        <div class="app-loader__inner">
            <div class="app-loader__ring">
                <span class="app-loader__logo">@include('partials.company-logo')</span>
            </div>
            <div class="app-loader__bar"><span></span></div>
            <p class="app-loader__label">{{ __('Loading...') }}</p>
        </div>
    </div>
    <script>
        (function () {
            var loader = document.getElementById('app-loader');
            if (!loader) return;

            // Show the splash only once per 24 hours (first visit on this browser).
            var KEY = 'venecia_splash_seen_at';
            var DAY = 24 * 60 * 60 * 1000;
            try {
                var last = parseInt(localStorage.getItem(KEY) || '0', 10);
                if (last && Date.now() - last < DAY) {
                    if (loader.parentNode) loader.parentNode.removeChild(loader);
                    return;
                }
            } catch (e) { /* storage unavailable — show the loader */ }

            var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var timer = setTimeout(function () {
                loader.classList.add('app-loader--done');
                try { localStorage.setItem(KEY, String(Date.now())); } catch (e) {}
                setTimeout(function () { if (loader.parentNode) loader.parentNode.removeChild(loader); }, 550);
            }, reduce ? 0 : 1500);
            window.addEventListener('pageshow', function (e) {
                if (e.persisted) { clearTimeout(timer); if (loader.parentNode) loader.parentNode.removeChild(loader); }
            });
        })();
    </script>
    @endif

    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-48 left-1/2 h-[480px] w-[820px] -translate-x-1/2 rounded-full bg-brand-600/15 blur-[130px]"></div>
        <div class="absolute top-1/3 -right-40 h-96 w-96 rounded-full bg-violet-600/10 blur-[110px]"></div>
        <div class="absolute -left-40 bottom-0 h-96 w-96 rounded-full bg-sky-500/10 blur-[110px]"></div>
        <div class="bg-grid absolute inset-0"></div>
    </div>

    <header class="sticky top-0 z-40 border-b border-white/10 bg-slate-950/80 backdrop-blur-xl">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 py-3 lg:px-6">
            <div class="flex items-center gap-3">
                <button type="button" class="touch-target inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/5 md:hidden" @click="menuOpen = true" aria-label="{{ __('Open menu') }}">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </button>
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    @include('partials.company-logo')
                </a>
            </div>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Primary navigation">
                <a href="{{ route('home') }}" class="nav-link {{ $currentRoute === 'home' ? 'is-active' : '' }}">{{ __('Home') }}</a>
                <a href="{{ route('public.projects.index') }}" class="nav-link {{ $currentRoute === 'public.projects.index' || str_starts_with((string) $currentRoute, 'public.projects') || $currentRoute === 'public.units.show' ? 'is-active' : '' }}">{{ __('Projects') }}</a>
                <a href="{{ route('installments.index') }}" class="nav-link {{ $currentRoute === 'installments.index' || $currentRoute === 'installments.calculate' ? 'is-active' : '' }}">{{ __('Calculator') }}</a>
                <a href="{{ route('public.about') }}" class="nav-link {{ $currentRoute === 'public.about' ? 'is-active' : '' }}">{{ __('About') }}</a>
                <a href="{{ route('public.contact') }}" class="nav-link {{ $currentRoute === 'public.contact' ? 'is-active' : '' }}">{{ __('Contact Us') }}</a>
            </nav>

            <div class="flex items-center gap-2">
                @if ($isCustomerAuth)
                    <a href="{{ route('customer.account') }}" class="app-button--ghost hidden sm:inline-flex">{{ __('My Account') }}</a>
                    <form method="POST" action="{{ route('customer.logout') }}" class="hidden sm:inline-flex">
                        @csrf
                        <button type="submit" class="app-button bg-rose-600 hover:bg-rose-700">{{ __('Sign out') }}</button>
                    </form>
                @elseif ($isAdminAuth)
                    <a href="{{ route('dashboard.home') }}" class="app-button--ghost hidden sm:inline-flex">{{ __('Dashboard') }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline-flex">
                        @csrf
                        <button type="submit" class="app-button bg-rose-600 hover:bg-rose-700">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('customer.login') }}" class="app-button hidden sm:inline-flex">{{ __('Sign in') }}</a>
                @endif
                <div class="hidden lg:block">
                    @include('partials.language-switcher')
                </div>
                <button type="button" class="touch-target inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/5" onclick="window.theme.toggle()" aria-label="{{ __('Toggle theme') }}">
                    <svg class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="5" stroke-width="1.8"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <svg class="hidden h-5 w-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-28 lg:px-6 lg:pb-12">
        @if (session('status'))
            <script>window.__flash = { message: @json(session('status')), type: 'success' };</script>
        @endif

        @yield('content')
    </main>

    @include('partials.public-footer')
    {{-- Clear the fixed mobile bottom nav so it never covers the footer --}}
    <div class="h-[calc(4rem+env(safe-area-inset-bottom))] lg:hidden" aria-hidden="true"></div>
    @include('partials.public-mobile-bottom-nav')

    <div x-cloak x-show="menuOpen" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm lg:hidden" @click="menuOpen = false"></div>
    <aside x-cloak x-show="menuOpen" x-transition class="mobile-drawer">
        <div class="flex items-center justify-between gap-3 border-b border-white/10 pb-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('Menu') }}</p>
                <p class="text-lg font-semibold">{{ config('app.name') }}</p>
            </div>
            <button type="button" class="touch-target rounded-full bg-white/5" @click="menuOpen = false" aria-label="{{ __('Close menu') }}">
                <svg class="mx-auto h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="mt-4 space-y-2">
            <a class="mobile-drawer__link {{ $currentRoute === 'home' ? 'is-active' : '' }}" href="{{ route('home') }}">{{ __('Home') }} <span>→</span></a>
            <a class="mobile-drawer__link {{ str_starts_with((string) $currentRoute, 'public.projects') ? 'is-active' : '' }}" href="{{ route('public.projects.index') }}">{{ __('Projects') }} <span>→</span></a>
            <a class="mobile-drawer__link {{ str_starts_with((string) $currentRoute, 'installments') ? 'is-active' : '' }}" href="{{ route('installments.index') }}">{{ __('Calculator') }} <span>→</span></a>
            <a class="mobile-drawer__link {{ $currentRoute === 'public.about' ? 'is-active' : '' }}" href="{{ route('public.about') }}">{{ __('About') }} <span>→</span></a>
            <a class="mobile-drawer__link {{ $currentRoute === 'public.contact' ? 'is-active' : '' }}" href="{{ route('public.contact') }}">{{ __('Contact Us') }} <span>→</span></a>
            @if ($isCustomerAuth)
                <a class="mobile-drawer__link" href="{{ route('customer.account') }}">{{ __('My Account') }} <span>→</span></a>
                <form method="POST" action="{{ route('customer.logout') }}">
                    @csrf
                    <button type="submit" class="mobile-drawer__link w-full">{{ __('Sign out') }} <span>→</span></button>
                </form>
            @elseif ($isAdminAuth)
                <a class="mobile-drawer__link {{ str_starts_with((string) $currentRoute, 'dashboard.') ? 'is-active' : '' }}" href="{{ route('dashboard.home') }}">{{ __('Dashboard') }} <span>→</span></a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mobile-drawer__link w-full">{{ __('Logout') }} <span>→</span></button>
                </form>
            @else
                <a href="{{ route('customer.login') }}" class="app-button mt-6 w-full">{{ __('Sign in') }}</a>
            @endif

            <div class="mt-4 flex items-center justify-between gap-3 border-t border-white/10 pt-4">
                <span class="text-sm font-semibold text-slate-300">{{ __('Language') }}</span>
                @include('partials.language-switcher')
            </div>
        </div>

    @include('partials.app-toast')
    @include('partials.app-confirm-modal')
    @stack('scripts')
</body>
</html>
