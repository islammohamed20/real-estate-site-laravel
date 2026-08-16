<!doctype html>
<?php
    $currentRoute = request()->route()?->getName();
    $isCrmRoute = $currentRoute !== null && str_starts_with($currentRoute, 'dashboard.crm');
    $isCrmActive = fn ($key) => $currentRoute !== null && str_starts_with($currentRoute, 'dashboard.crm.' . $key);
    $crmModules = [
        ['key' => 'index', 'label' => __('Overview'), 'route' => 'dashboard.crm.index', 'tint' => 'bg-brand-500/15 text-brand-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="1.8"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="1.8"/></svg>'],
        ['key' => 'whatsapp', 'label' => __('WhatsApp'), 'route' => 'dashboard.whatsapp.index', 'tint' => 'bg-emerald-500/15 text-emerald-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>'],
        ['key' => 'leads', 'label' => __('Leads'), 'route' => 'dashboard.crm.leads.index', 'tint' => 'bg-emerald-500/15 text-emerald-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="8.5" cy="7" r="4" stroke-width="1.8"/><path d="M20 8v6M23 11h-6" stroke-width="1.8" stroke-linecap="round"/></svg>'],
        ['key' => 'customers', 'label' => __('Customers'), 'route' => 'dashboard.crm.customers.index', 'tint' => 'bg-brand-500/15 text-brand-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>'],
        ['key' => 'deals', 'label' => __('Deals'), 'route' => 'dashboard.crm.deals.index', 'tint' => 'bg-violet-500/15 text-violet-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-width="1.8"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-width="1.8"/></svg>'],
        ['key' => 'offers', 'label' => __('Offers'), 'route' => 'dashboard.crm.offers.index', 'tint' => 'bg-amber-500/15 text-amber-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke-width="1.8"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke-width="1.8" stroke-linecap="round"/></svg>'],
        ['key' => 'reservations', 'label' => __('Reservations'), 'route' => 'dashboard.crm.reservations.index', 'tint' => 'bg-rose-500/15 text-rose-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M19 21l-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" stroke-width="1.8" stroke-linejoin="round"/></svg>'],
        ['key' => 'plans', 'label' => __('Plans'), 'route' => 'dashboard.crm.plans.index', 'tint' => 'bg-emerald-500/15 text-emerald-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 4h11a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H9z" stroke-width="1.8"/><path d="M4 20a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v16z" stroke-width="1.8"/><path d="M12 8v4M12 16h.01" stroke-width="1.8" stroke-linecap="round"/></svg>'],
        ['key' => 'documents', 'label' => __('Documents'), 'route' => 'dashboard.crm.documents.index', 'tint' => 'bg-sky-500/15 text-sky-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" stroke-width="1.8" stroke-linejoin="round"/></svg>'],
        ['key' => 'tasks', 'label' => __('Tasks'), 'route' => 'dashboard.crm.tasks.index', 'tint' => 'bg-emerald-500/15 text-emerald-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 11l3 3L22 4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke-width="1.8" stroke-linecap="round"/></svg>'],
        ['key' => 'follow_ups', 'label' => __('Follow-ups'), 'route' => 'dashboard.crm.follow_ups.index', 'tint' => 'bg-sky-500/15 text-sky-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M12 7v5l3 3" stroke-width="1.8" stroke-linecap="round"/></svg>'],
        ['key' => 'reports', 'label' => __('Reports'), 'route' => 'dashboard.crm.reports.index', 'tint' => 'bg-violet-500/15 text-violet-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21.21 15.89A10 10 0 1 1 8 2.83" stroke-width="1.8" stroke-linecap="round"/><path d="M22 12A10 10 0 0 0 12 2v10z" stroke-width="1.8"/></svg>'],
        ['key' => 'search', 'label' => __('Search'), 'route' => 'dashboard.crm.search', 'tint' => 'bg-white/10 text-slate-300', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="1.8"/><path d="m21 21-4.3-4.3" stroke-width="1.8" stroke-linecap="round"/></svg>'],
        ['key' => 'trash', 'label' => __('Trash'), 'route' => 'dashboard.crm.trash.index', 'tint' => 'bg-rose-500/15 text-rose-400', 'icon' => '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>'],
    ];
?>
<?php ($companyProfile = \App\Models\CompanyProfile::first()); ?>
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
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
    <style>body { font-family: 'Cairo', sans-serif !important; }</style>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js']); ?>
</head>
<body class="dash-ui min-h-full bg-slate-950 text-slate-100" x-data="{
    drawerOpen: false,
    sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === '1',
    crmSubmenuOpen: <?php echo e($isCrmRoute ? 'true' : 'false'); ?>,
    crmFlyoutOpen: false,
    flyoutTop: 0,
    flyoutTimer: null,
    openCrmFlyout(el) {
        if (!this.sidebarCollapsed) return;
        clearTimeout(this.flyoutTimer);
        this.flyoutTop = el.getBoundingClientRect().top - el.closest('aside').getBoundingClientRect().top;
        this.crmFlyoutOpen = true;
    },
    closeCrmFlyout() {
        if (!this.sidebarCollapsed) return;
        this.flyoutTimer = setTimeout(() => { this.crmFlyoutOpen = false; }, 200);
    },
    keepCrmFlyoutOpen() {
        clearTimeout(this.flyoutTimer);
        this.crmFlyoutOpen = true;
    }
}" x-init="$watch('sidebarCollapsed', v => localStorage.setItem('sidebarCollapsed', v ? '1' : '0'))">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-48 left-1/2 h-[480px] w-[820px] -translate-x-1/2 rounded-full bg-brand-600/10 blur-[130px]"></div>
        <div class="bg-grid absolute inset-0"></div>
    </div>

    <!-- Desktop Sidebar -->
    <aside class="hidden lg:flex fixed inset-y-0 ltr:left-0 rtl:right-0 flex-col bg-slate-950/50 backdrop-blur-xl border-x border-white/10 z-50 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]" :class="sidebarCollapsed ? 'w-[76px]' : 'w-72'">
        <div class="flex h-16 items-center border-b border-white/10 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]" :class="sidebarCollapsed ? 'justify-center px-2' : 'px-6'">
            <div x-show="!sidebarCollapsed" x-cloak x-transition.opacity.duration.300ms class="w-full overflow-hidden">
                <?php echo $__env->make('partials.company-logo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <div x-show="sidebarCollapsed" x-cloak x-transition.scale.origin.center.duration.300ms>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-600 text-sm font-black text-white shadow-lg shadow-brand-600/30">
                    <?php echo e(mb_strtoupper(mb_substr($companyProfile?->name ?? config('app.name'), 0, 1))); ?>

                </span>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-8">
            <div>
                <p x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="overflow-hidden whitespace-nowrap px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] mb-4"><?php echo e(__('Menu')); ?></p>
                <nav class="space-y-1">
                    <a class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition <?php echo e($currentRoute === 'dashboard.home' ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'); ?>" :class="sidebarCollapsed ? 'justify-center px-0' : ''" :title="sidebarCollapsed ? '<?php echo e(__('Dashboard')); ?>' : ''" href="<?php echo e(route('dashboard.home')); ?>">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="1.8"/><path d="M9 22V12h6v10" stroke-width="1.8"/></svg>
                        <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Dashboard')); ?></span>
                    </a>
                    <div class="relative">
                        <button type="button" @click="crmSubmenuOpen = !crmSubmenuOpen" @mouseenter="openCrmFlyout($el)" @mouseleave="closeCrmFlyout()" :title="sidebarCollapsed ? '<?php echo e(__('CRM')); ?>' : ''" class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition <?php echo e($isCrmRoute ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'); ?>" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                            <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0 flex-none' : 'max-w-52 opacity-100 flex-1'" class="inline-block overflow-hidden whitespace-nowrap text-start transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('CRM')); ?></span>
                            <svg x-show="!sidebarCollapsed" x-cloak class="h-4 w-4 shrink-0 transition-transform duration-300" :class="crmSubmenuOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m6 9 6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>

                        
                        <div x-show="!sidebarCollapsed && crmSubmenuOpen" x-cloak x-transition class="mt-1 space-y-0.5 overflow-y-auto overscroll-contain rounded-2xl border border-white/5 bg-white/5 p-1.5" style="max-height: min(60vh, 26rem); scrollbar-width: thin;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $crmModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route($module['route'])); ?>" class="flex items-center gap-2.5 rounded-xl px-2.5 py-2 text-[13px] font-semibold transition <?php echo e($isCrmActive($module['key']) ? 'bg-brand-600 text-white' : 'text-slate-400 hover:bg-white/10 hover:text-white'); ?>">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg <?php echo e($module['tint']); ?>"><?php echo $module['icon']; ?></span>
                                    <span class="flex-1 truncate"><?php echo e($module['label']); ?></span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                    </div>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition <?php echo e(in_array($currentRoute, ['dashboard.projects.index', 'dashboard.projects.create', 'dashboard.projects.edit', 'dashboard.projects.units.create', 'dashboard.projects.units.edit']) ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'); ?>" :class="sidebarCollapsed ? 'justify-center px-0' : ''" :title="sidebarCollapsed ? '<?php echo e(__('Projects')); ?>' : ''" href="<?php echo e(route('dashboard.projects.index')); ?>">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 21h18M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7M4 21V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v17" stroke-width="1.8"/></svg>
                        <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Projects')); ?></span>
                    </a>
                </nav>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->can('view reports') || auth()->user()->can('manage users') || auth()->user()->can('manage settings') || auth()->user()->hasAnyRole(['Administrator', 'Sales Manager'])): ?>
            <div>
                <p x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="overflow-hidden whitespace-nowrap px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] mb-4"><?php echo e(__('Administration')); ?></p>
                <nav class="space-y-1">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view reports')): ?>
                        <a class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition <?php echo e($currentRoute === 'dashboard.reports.index' ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'); ?>" :class="sidebarCollapsed ? 'justify-center px-0' : ''" :title="sidebarCollapsed ? '<?php echo e(__('Reports')); ?>' : ''" href="<?php echo e(route('dashboard.reports.index')); ?>">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21.21 15.89A10 10 0 1 1 8 2.83" stroke-width="1.8"/><path d="M22 12A10 10 0 0 0 12 2v10z" stroke-width="1.8"/></svg>
                            <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Reports')); ?></span>
                        </a>
                        <a class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition <?php echo e($currentRoute === 'dashboard.analytics.index' ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'); ?>" :class="sidebarCollapsed ? 'justify-center px-0' : ''" :title="sidebarCollapsed ? '<?php echo e(__('Site Analytics')); ?>' : ''" href="<?php echo e(route('dashboard.analytics.index')); ?>">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 3v18h18" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 14l4-4 3 3 5-6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Site Analytics')); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', ['Administrator', 'Sales Manager'])): ?>
                        <a class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition <?php echo e(str_starts_with((string) $currentRoute, 'dashboard.trash') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'); ?>" :class="sidebarCollapsed ? 'justify-center px-0' : ''" :title="sidebarCollapsed ? '<?php echo e(__('Trash')); ?>' : ''" href="<?php echo e(route('dashboard.trash.index')); ?>">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6M5 6v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Trash')); ?></span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage users')): ?>
                        <a class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition <?php echo e($currentRoute === 'dashboard.users.index' ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'); ?>" :class="sidebarCollapsed ? 'justify-center px-0' : ''" :title="sidebarCollapsed ? '<?php echo e(__('Users')); ?>' : ''" href="<?php echo e(route('dashboard.users.index')); ?>">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" stroke-width="1.8"/><circle cx="9" cy="7" r="4" stroke-width="1.8"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-width="1.8"/></svg>
                            <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Users')); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage settings')): ?>
                        <a class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition <?php echo e($currentRoute === 'dashboard.banners.index' || str_starts_with((string) $currentRoute, 'dashboard.banners') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'); ?>" :class="sidebarCollapsed ? 'justify-center px-0' : ''" :title="sidebarCollapsed ? '<?php echo e(__('Banners')); ?>' : ''" href="<?php echo e(route('dashboard.banners.index')); ?>">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.8"/><path d="m21 15-5-5L5 21" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Banners')); ?></span>
                        </a>
                        <a class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition <?php echo e($currentRoute === 'dashboard.settings.index' ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-white'); ?>" :class="sidebarCollapsed ? 'justify-center px-0' : ''" :title="sidebarCollapsed ? '<?php echo e(__('Settings')); ?>' : ''" href="<?php echo e(route('dashboard.settings.index')); ?>">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/></svg>
                            <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Settings')); ?></span>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div>
                <p x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="overflow-hidden whitespace-nowrap px-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500 transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] mb-4"><?php echo e(__('Tools')); ?></p>
                <nav class="space-y-1">
                    <a href="<?php echo e(route('dashboard.installments.index')); ?>" :title="sidebarCollapsed ? '<?php echo e(__('Calculator')); ?>' : ''" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition text-slate-400 hover:bg-white/5 hover:text-white" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                        <svg class="h-5 w-5 shrink-0 text-brand-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="4" y="2" width="16" height="20" rx="2" stroke-width="1.8"/><path d="M8 6h8M8 10h8M8 14h8M8 18h8" stroke-width="1.8"/></svg>
                        <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Calculator')); ?></span>
                    </a>
                </nav>
            </div>
        </div>
        <div class="p-4 mt-auto space-y-2">
            <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" :title="sidebarCollapsed ? '<?php echo e(__('Expand')); ?>' : '<?php echo e(__('Collapse')); ?>'" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-white/5 py-3 text-sm font-bold text-slate-300 hover:bg-white/10 hover:text-white transition">
                <svg class="h-4 w-4 shrink-0 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.8"/><path d="M9 3v18" stroke-width="1.8"/><path d="m13 10-2 2 2 2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Collapse')); ?></span>
            </button>
            <a href="<?php echo e(route('home')); ?>" :title="sidebarCollapsed ? '<?php echo e(__('Public Website')); ?>' : ''" class="flex items-center justify-center gap-2 w-full py-3 rounded-2xl bg-white/5 text-sm font-bold text-white hover:bg-white/10 transition">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="2"/><path d="M9 22V12h6v10" stroke-width="2"/></svg>
                <span x-cloak :class="sidebarCollapsed ? 'max-w-0 opacity-0' : 'max-w-52 opacity-100'" class="inline-block overflow-hidden whitespace-nowrap transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]"><?php echo e(__('Public Website')); ?></span>
            </a>
        </div>

        
        <div x-show="sidebarCollapsed && crmFlyoutOpen" x-cloak x-transition.scale.origin.top.duration.200ms class="absolute z-[60] w-64 ltr:left-full rtl:right-full ltr:pl-2 rtl:pr-2" :style="{ top: flyoutTop + 'px' }" @mouseenter="keepCrmFlyoutOpen()" @mouseleave="closeCrmFlyout()">
            <div class="rounded-2xl border border-white/10 bg-slate-900 p-2 shadow-2xl shadow-black/60 backdrop-blur-xl" style="max-height: 70vh; overflow-y: auto; overscroll-behavior: contain; scrollbar-width: thin;">
                <p class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500"><?php echo e(__('CRM')); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $crmModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route($module['route'])); ?>" class="flex items-center gap-2.5 rounded-xl px-2.5 py-2 text-[13px] font-semibold transition <?php echo e($isCrmActive($module['key']) ? 'bg-brand-600 text-white' : 'text-slate-400 hover:bg-white/10 hover:text-white'); ?>">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg <?php echo e($module['tint']); ?>"><?php echo $module['icon']; ?></span>
                        <span class="flex-1 truncate"><?php echo e($module['label']); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </aside>

    <div class="min-h-screen flex flex-col transition-all duration-300" :class="sidebarCollapsed ? 'ltr:lg:ml-[76px] rtl:lg:mr-[76px]' : 'ltr:lg:ml-72 rtl:lg:mr-72'">
        <header class="sticky top-0 z-40 border-b border-white/10 bg-slate-950/90 px-4 py-2.5 backdrop-blur-xl lg:px-6 lg:py-3">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <button type="button" class="touch-target inline-flex items-center justify-center rounded-2xl bg-white/5 lg:hidden" @click="drawerOpen = true" aria-label="<?php echo e(__('Open menu')); ?>">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                    <div class="hidden lg:block">
                        <?php echo $__env->make('partials.company-logo', ['size' => 'desktop'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <div class="lg:hidden">
                        <?php echo $__env->make('partials.company-logo', ['size' => 'mobile'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                    <div class="relative hidden sm:block lg:ml-4">
                        <form action="<?php echo e(route('dashboard.crm.search')); ?>" method="GET">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/></svg>
                            </div>
                            <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="h-9 w-64 rounded-xl border border-white/5 bg-white/5 pl-9 pr-4 text-sm text-slate-200 placeholder:text-slate-500 transition focus:border-brand-500/50 focus:bg-white/10 focus:ring-0 lg:w-80" placeholder="<?php echo e(__('Search...')); ?>">
                        </form>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('dashboard.crm.search')); ?>" class="touch-target inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-slate-400 transition hover:text-white sm:hidden" aria-label="<?php echo e(__('Search')); ?>">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8" stroke-width="2"/><path d="m21 21-4.3-4.3" stroke-width="2" stroke-linecap="round"/></svg>
                    </a>
                    <div class="flex items-center gap-2">
                        <div class="hidden lg:block">
                            <?php echo $__env->make('partials.language-switcher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                        <button type="button" class="touch-target hidden h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-slate-400 hover:text-white transition lg:inline-flex" onclick="window.theme.toggle()" aria-label="<?php echo e(__('Toggle theme')); ?>">
                            <svg class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="5" stroke-width="2"/><path d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72 1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke-width="2" stroke-linecap="round"/></svg>
                            <svg class="hidden h-5 w-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>

                    <div class="h-6 w-px bg-white/10 mx-1 hidden lg:block"></div>

                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open; if (open) positionUserMenu($el, $refs.panel)" type="button" class="relative touch-target inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-slate-400 transition hover:text-white" aria-label="<?php echo e(__('Notifications')); ?>">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dashboardUnreadCount > 0): ?>
                                <span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white shadow-lg shadow-rose-500/40"><?php echo e($dashboardUnreadCount > 9 ? '9+' : $dashboardUnreadCount); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-cloak x-transition x-ref="panel" class="fixed top-12 z-[70] w-80 max-w-[calc(100vw-1rem)] rounded-2xl border border-white/10 bg-slate-900 shadow-2xl shadow-black/60 backdrop-blur-xl">
                            <div class="flex items-center justify-between gap-2 border-b border-white/5 px-4 py-3">
                                <p class="text-sm font-bold text-white"><?php echo e(__('Notifications')); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dashboardUnreadCount > 0): ?>
                                    <form method="POST" action="<?php echo e(route('dashboard.notifications.read-all')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="text-xs font-semibold text-brand-300 transition hover:text-brand-200"><?php echo e(__('Mark all as read')); ?></button>
                                    </form>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="max-h-96 overflow-y-auto p-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $dashboardNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <a href="<?php echo e($notification->url); ?>" @click="fetch('<?php echo e(route('dashboard.notifications.read', $notification->id)); ?>', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, keepalive: true })" class="flex gap-3 rounded-xl px-3 py-2.5 transition hover:bg-white/5 <?php echo e($notification->unread ? 'bg-brand-500/10' : ''); ?>">
                                        <span class="mt-1.5 flex h-2 w-2 shrink-0 rounded-full <?php echo e($notification->unread ? 'bg-rose-400' : 'bg-transparent'); ?>"></span>
                                        <span class="min-w-0">
                                            <span class="block text-sm font-semibold text-white"><?php echo e($notification->title); ?></span>
                                            <span class="mt-0.5 block text-xs leading-5 text-slate-400"><?php echo e($notification->message); ?></span>
                                            <span class="mt-1 block text-[11px] text-slate-500"><?php echo e($notification->created_at?->diffForHumans()); ?></span>
                                        </span>
                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="px-3 py-8 text-center">
                                        <svg class="mx-auto h-8 w-8 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M13.73 21a2 2 0 0 1-3.46 0" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <p class="mt-2 text-sm text-slate-400"><?php echo e(__('No notifications yet')); ?></p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open; if (open) positionUserMenu($el, $refs.panel)" type="button" class="flex items-center gap-2 rounded-xl bg-white/5 p-1.5 transition hover:bg-white/10">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-xs font-bold text-white shadow-lg shadow-brand-600/20">
                                <?php echo e(mb_strtoupper(mb_substr(auth()->user()?->name ?? 'A', 0, 1))); ?>

                            </span>
                            <span class="hidden text-sm font-semibold text-white lg:block mr-1"><?php echo e(auth()->user()?->name); ?></span>
                            <svg class="hidden h-4 w-4 text-slate-500 lg:block transition" :class="open ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m6 9 6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-cloak x-transition x-ref="panel" class="fixed top-12 z-[70] w-56 max-w-[calc(100vw-1rem)] rounded-2xl border border-white/10 bg-slate-900 p-2 shadow-2xl shadow-black/60 backdrop-blur-xl">
                            <div class="px-3 py-2 border-b border-white/5 mb-1">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500"><?php echo e(__('Account')); ?></p>
                                <p class="text-sm font-semibold text-white truncate"><?php echo e(auth()->user()?->email); ?></p>
                            </div>
                            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke-width="2"/><path d="M9 22V12h6v10" stroke-width="2"/></svg>
                                <?php echo e(__('Public Website')); ?>

                            </a>
                            <a href="<?php echo e(route('dashboard.security')); ?>" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/><path d="m9 12 2 2 4-4"/></svg>
                                <span class="flex items-center gap-2">
                                    <?php echo e(__('Security & 2FA')); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()?->two_factor_enabled): ?>
                                        <span class="rounded-full bg-emerald-500/20 px-2 py-0.5 text-[10px] font-bold text-emerald-300"><?php echo e(__('On')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </a>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-rose-400 hover:bg-rose-500/10 transition text-left">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke-width="2"/><polyline points="16 17 21 12 16 7" stroke-width="2"/><line x1="21" y1="12" x2="9" y2="12" stroke-width="2"/></svg>
                                    <?php echo e(__('Logout')); ?>

                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="w-full flex-1 px-4 pt-4 pb-28 lg:px-8 lg:pt-8 lg:pb-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
                <script>window.__flash = { message: <?php echo json_encode(session('status'), 15, 512) ?>, type: 'success' };</script>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!request()->routeIs('dashboard.whatsapp.index', 'dashboard.whatsapp.reports', 'dashboard.whatsapp.templates.index')): ?>
            <?php echo $__env->make('partials.mobile-fab', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php echo $__env->make('partials.mobile-bottom-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div x-cloak x-show="drawerOpen" class="mobile-drawer__overlay" @click="drawerOpen = false"></div>
        <aside x-cloak x-show="drawerOpen" x-transition class="mobile-drawer">
            <div class="flex items-center justify-between gap-3 border-b border-white/10 pb-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400"><?php echo e(__('Signed in as')); ?></p>
                    <p class="text-lg font-semibold text-white"><?php echo e(auth()->user()?->name); ?></p>
                    <p class="truncate text-xs text-slate-500"><?php echo e(auth()->user()?->email); ?></p>
                </div>
                <button type="button" class="touch-target rounded-full bg-white/5" @click="drawerOpen = false" aria-label="<?php echo e(__('Close menu')); ?>">
                    <svg class="mx-auto h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M6 6l12 12M18 6 6 18" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
            </div>

            <div class="mt-4 space-y-2">
                <a class="mobile-drawer__link <?php echo e($currentRoute === 'dashboard.home' ? 'is-active' : ''); ?>" href="<?php echo e(route('dashboard.home')); ?>"><?php echo e(__('Dashboard')); ?> <span>→</span></a>
                <a class="mobile-drawer__link <?php echo e(in_array($currentRoute, ['dashboard.crm.index', 'dashboard.crm.deals.index', 'dashboard.crm.deals.show']) ? 'is-active' : ''); ?>" href="<?php echo e(route('dashboard.crm.index')); ?>"><?php echo e(__('CRM')); ?> <span>→</span></a>
                <a class="mobile-drawer__link <?php echo e($currentRoute === 'dashboard.crm.deals.index' ? 'is-active' : ''); ?>" href="<?php echo e(route('dashboard.crm.deals.index')); ?>"><?php echo e(__('Deals')); ?> <span>→</span></a>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view reports')): ?>
                    <a class="mobile-drawer__link <?php echo e($currentRoute === 'dashboard.reports.index' ? 'is-active' : ''); ?>" href="<?php echo e(route('dashboard.reports.index')); ?>"><?php echo e(__('Reports & Insights')); ?> <span>→</span></a>
                    <a class="mobile-drawer__link <?php echo e($currentRoute === 'dashboard.analytics.index' ? 'is-active' : ''); ?>" href="<?php echo e(route('dashboard.analytics.index')); ?>"><?php echo e(__('Site Analytics')); ?> <span>→</span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage settings')): ?>
                    <a class="mobile-drawer__link <?php echo e(str_starts_with((string) $currentRoute, 'dashboard.banners') ? 'is-active' : ''); ?>" href="<?php echo e(route('dashboard.banners.index')); ?>"><?php echo e(__('Banners')); ?> <span>→</span></a>
                <?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', ['Administrator', 'Sales Manager'])): ?>
                    <a class="mobile-drawer__link <?php echo e(str_starts_with((string) $currentRoute, 'dashboard.trash') ? 'is-active' : ''); ?>" href="<?php echo e(route('dashboard.trash.index')); ?>"><?php echo e(__('Trash')); ?> <span>→</span></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage users')): ?>
                    <a class="mobile-drawer__link <?php echo e($currentRoute === 'dashboard.users.index' ? 'is-active' : ''); ?>" href="<?php echo e(route('dashboard.users.index')); ?>"><?php echo e(__('Users & Permissions')); ?> <span>→</span></a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage settings')): ?>
                    <a class="mobile-drawer__link <?php echo e($currentRoute === 'dashboard.settings.index' ? 'is-active' : ''); ?>" href="<?php echo e(route('dashboard.settings.index')); ?>"><?php echo e(__('Settings')); ?> <span>→</span></a>
                <?php endif; ?>
                <a class="mobile-drawer__link" href="<?php echo e(route('home')); ?>"><?php echo e(__('Public Website')); ?> <span>→</span></a>
                <a class="mobile-drawer__link" href="<?php echo e(route('dashboard.installments.index')); ?>"><?php echo e(__('Calculator')); ?> <span>→</span></a>
                <div class="mt-4 flex items-center justify-between gap-3 border-t border-white/10 pt-4">
                    <span class="text-sm font-semibold text-slate-300"><?php echo e(__('Language')); ?></span>
                    <?php echo $__env->make('partials.language-switcher', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </aside>
    </div>

    <?php echo $__env->make('partials.app-toast', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.app-confirm-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /var/www/real-estate-site/resources/views/layouts/dashboard.blade.php ENDPATH**/ ?>