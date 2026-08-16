<!doctype html>
<?php
    $companyProfile = \App\Models\CompanyProfile::first();
    $isCustomerAuth = auth('customer')->check();
    $isAdminAuth = auth()->check();
?>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e(app()->getLocale() === 'ar' ? 'rtl' : 'ltr'); ?>" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="theme-color" content="#0b1120">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?php echo e(config('app.name')); ?>">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="<?php echo e($companyProfile?->favicon_path ?? '/icons/icon-maskable.svg'); ?>">
    <link rel="apple-touch-icon" href="<?php echo e($companyProfile?->favicon_path ?? '/icons/icon-maskable.svg'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title><?php echo e($title ?? config('app.name')); ?></title>
    <script>
        (function () {
            try {
                const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                if (theme === 'dark') document.documentElement.classList.add('dark');
                document.querySelector('meta[name="theme-color"]').setAttribute('content', theme === 'dark' ? '#0b1120' : '#f8fafc');
            } catch (e) {}
        })();
    </script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="flex min-h-screen flex-col bg-slate-950 text-slate-100" x-data="{ menuOpen: false }">
    <?php ($currentRoute = request()->route()?->getName()); ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentRoute === 'home'): ?>
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
                <span class="app-loader__logo"><?php echo $__env->make('partials.company-logo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></span>
            </div>
            <div class="app-loader__bar"><span></span></div>
            <p class="app-loader__label"><?php echo e(__('Loading...')); ?></p>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-48 left-1/2 h-[480px] w-[820px] -translate-x-1/2 rounded-full bg-brand-600/15 blur-[130px]"></div>
        <div class="absolute top-1/3 -right-40 h-96 w-96 rounded-full bg-violet-600/10 blur-[110px]"></div>
        <div class="absolute -left-40 bottom-0 h-96 w-96 rounded-full bg-sky-500/10 blur-[110px]"></div>
        <div class="bg-grid absolute inset-0"></div>
    </div>

    <header class="sticky top-0 z-40 border-b border-white/10 bg-slate-950/80 backdrop-blur-xl">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-3 px-4 py-3 lg:px-6">
            <div class="flex items-center gap-3">
                <button type="button" class="touch-target inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/5 md:hidden" @click="menuOpen = true" aria-label="<?php echo e(__('Open menu')); ?>">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </button>
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3">
                    <?php echo $__env->make('partials.company-logo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </a>
            </div>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Primary navigation">
                <a href="<?php echo e(route('home')); ?>" class="nav-link <?php echo e($currentRoute === 'home' ? 'is-active' : ''); ?>"><?php echo e(__('Home')); ?></a>
                <a href="<?php echo e(route('public.projects.index')); ?>" class="nav-link <?php echo e($currentRoute === 'public.projects.index' || str_starts_with((string) $currentRoute, 'public.projects') || $currentRoute === 'public.units.show' ? 'is-active' : ''); ?>"><?php echo e(__('Projects')); ?></a>
                <a href="<?php echo e(route('installments.index')); ?>" class="nav-link <?php echo e($currentRoute === 'installments.index' || $currentRoute === 'installments.calculate' ? 'is-active' : ''); ?>"><?php echo e(__('Calculator')); ?></a>
                <a href="<?php echo e(route('public.about')); ?>" class="nav-link <?php echo e($currentRoute === 'public.about' ? 'is-active' : ''); ?>"><?php echo e(__('About')); ?></a>
                <a href="<?php echo e(route('public.contact')); ?>" class="nav-link <?php echo e($currentRoute === 'public.contact' ? 'is-active' : ''); ?>"><?php echo e(__('Contact Us')); ?></a>
            </nav>

            <div class="flex items-center gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCustomerAuth): ?>
                    <a href="<?php echo e(route('customer.account')); ?>" class="app-button--ghost hidden sm:inline-flex"><?php echo e(__('My Account')); ?></a>
                    <form method="POST" action="<?php echo e(route('customer.logout')); ?>" class="hidden sm:inline-flex">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="app-button bg-rose-600 hover:bg-rose-700"><?php echo e(__('Sign out')); ?></button>
                    </form>
                <?php elseif($isAdminAuth): ?>
                    <a href="<?php echo e(route('dashboard.home')); ?>" class="app-button--ghost hidden sm:inline-flex"><?php echo e(__('Dashboard')); ?></a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="hidden sm:inline-flex">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="app-button bg-rose-600 hover:bg-rose-700"><?php echo e(__('Logout')); ?></button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo e(route('customer.login')); ?>" class="app-button hidden sm:inline-flex"><?php echo e(__('Sign in')); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="hidden lg:block">
                    <?php echo $__env->make('partials.language-switcher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
                <button type="button" class="touch-target inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-white/5" onclick="window.theme.toggle()" aria-label="<?php echo e(__('Toggle theme')); ?>">
                    <svg class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="5" stroke-width="1.8"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <svg class="hidden h-5 w-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-28 lg:px-6 lg:pb-12">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
            <script>window.__flash = { message: <?php echo json_encode(session('status'), 15, 512) ?>, type: 'success' };</script>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('partials.public-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <div class="h-[calc(4rem+env(safe-area-inset-bottom))] lg:hidden" aria-hidden="true"></div>
    <?php echo $__env->make('partials.public-mobile-bottom-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div x-cloak x-show="menuOpen" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm lg:hidden" @click="menuOpen = false"></div>
    <aside x-cloak x-show="menuOpen" x-transition class="mobile-drawer">
        <div class="flex items-center justify-between gap-3 border-b border-white/10 pb-4">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400"><?php echo e(__('Menu')); ?></p>
                <p class="text-lg font-semibold"><?php echo e(config('app.name')); ?></p>
            </div>
            <button type="button" class="touch-target rounded-full bg-white/5" @click="menuOpen = false" aria-label="<?php echo e(__('Close menu')); ?>">
                <svg class="mx-auto h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="mt-4 space-y-2">
            <a class="mobile-drawer__link <?php echo e($currentRoute === 'home' ? 'is-active' : ''); ?>" href="<?php echo e(route('home')); ?>"><?php echo e(__('Home')); ?> <span>→</span></a>
            <a class="mobile-drawer__link <?php echo e(str_starts_with((string) $currentRoute, 'public.projects') ? 'is-active' : ''); ?>" href="<?php echo e(route('public.projects.index')); ?>"><?php echo e(__('Projects')); ?> <span>→</span></a>
            <a class="mobile-drawer__link <?php echo e(str_starts_with((string) $currentRoute, 'installments') ? 'is-active' : ''); ?>" href="<?php echo e(route('installments.index')); ?>"><?php echo e(__('Calculator')); ?> <span>→</span></a>
            <a class="mobile-drawer__link <?php echo e($currentRoute === 'public.about' ? 'is-active' : ''); ?>" href="<?php echo e(route('public.about')); ?>"><?php echo e(__('About')); ?> <span>→</span></a>
            <a class="mobile-drawer__link <?php echo e($currentRoute === 'public.contact' ? 'is-active' : ''); ?>" href="<?php echo e(route('public.contact')); ?>"><?php echo e(__('Contact Us')); ?> <span>→</span></a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCustomerAuth): ?>
                <a class="mobile-drawer__link" href="<?php echo e(route('customer.account')); ?>"><?php echo e(__('My Account')); ?> <span>→</span></a>
                <form method="POST" action="<?php echo e(route('customer.logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="mobile-drawer__link w-full"><?php echo e(__('Sign out')); ?> <span>→</span></button>
                </form>
            <?php elseif($isAdminAuth): ?>
                <a class="mobile-drawer__link <?php echo e(str_starts_with((string) $currentRoute, 'dashboard.') ? 'is-active' : ''); ?>" href="<?php echo e(route('dashboard.home')); ?>"><?php echo e(__('Dashboard')); ?> <span>→</span></a>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="mobile-drawer__link w-full"><?php echo e(__('Logout')); ?> <span>→</span></button>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('customer.login')); ?>" class="app-button mt-6 w-full"><?php echo e(__('Sign in')); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="mt-4 flex items-center justify-between gap-3 border-t border-white/10 pt-4">
                <span class="text-sm font-semibold text-slate-300"><?php echo e(__('Language')); ?></span>
                <?php echo $__env->make('partials.language-switcher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>

    <?php echo $__env->make('partials.app-toast', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.app-confirm-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /var/www/real-estate-site/resources/views/layouts/public.blade.php ENDPATH**/ ?>