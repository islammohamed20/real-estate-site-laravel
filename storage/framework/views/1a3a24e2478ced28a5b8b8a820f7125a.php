<?php ($companyProfile = \App\Models\CompanyProfile::query()->first()); ?>
<footer class="mt-20 border-t border-white/10 bg-slate-950/80">
    <div class="mx-auto w-full max-w-7xl px-4 py-12 lg:px-6">
        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
            <div class="space-y-4">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($companyProfile?->logo_light_path || $companyProfile?->logo_path): ?>
                        <img src="<?php echo e(asset($companyProfile->logo_light_path ?? $companyProfile->logo_path)); ?>" alt="<?php echo e($companyProfile?->name ?? config('app.name')); ?>" class="h-11 w-auto object-contain">
                    <?php else: ?>
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 via-indigo-600 to-violet-600 shadow-lg shadow-brand-500/30">
                            <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z" stroke-width="1.8" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span class="leading-tight">
                        <strong class="block text-lg font-bold text-white"><?php echo e($companyProfile?->name ?? 'Venecia Developments'); ?></strong>
                        <span class="block text-xs text-brand-300"><?php echo e(__('Real estate management system')); ?></span>
                    </span>
                </a>
                <p class="max-w-md text-sm leading-relaxed text-slate-400">
                    <?php echo e(__('رواد في صناعة التطوير العقاري والمجتمعات السكنية الفاخرة. نقدم حلولا معمارية متكاملة وتصاميم أيقونية بأرقى المواقع وأسهل أنظمة السداد.')); ?>

                </p>
            </div>

            <div class="space-y-3">
                <p class="mobile-section-title"><?php echo e(__('Quick Links')); ?></p>
                <ul class="space-y-2.5 text-sm text-slate-400">
                    <li><a href="<?php echo e(route('public.projects.index')); ?>" class="transition hover:text-white"><?php echo e(__('المشاريع والوحدات المتاحة')); ?></a></li>
                    <li><a href="<?php echo e(route('public.about')); ?>" class="transition hover:text-white"><?php echo e(__('من نحن')); ?></a></li>
                    <li><a href="<?php echo e(route('public.contact')); ?>" class="transition hover:text-white"><?php echo e(__('اتصل بنا')); ?></a></li>
                    <li><a href="<?php echo e(route('login')); ?>" class="transition hover:text-white"><?php echo e(__('بوابة مبيعات الشركة')); ?></a></li>
                </ul>
            </div>

            <div class="space-y-3">
                <p class="mobile-section-title"><?php echo e(__('تواصل معنا')); ?></p>
                <p class="text-sm leading-relaxed text-slate-400">
                    <?php echo e(__('احسب خطة سدادك بنفسك أو تواصل مع مسؤولي المبيعات للحصول على استشارة عقارية مخصصة.')); ?>

                </p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($companyProfile?->address): ?>
                    <p class="text-sm text-slate-400"><?php echo e($companyProfile->address); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 sm:flex-row text-xs text-slate-500">
            <p>© <?php echo e(date('Y')); ?> <strong><?php echo e($companyProfile?->name ?? 'Venecia Developments'); ?></strong> — <?php echo e(__('All rights reserved.')); ?></p>
            <p><?php echo e(__('صُنِعَ لأعلى مستويات الفخامة والتميز.')); ?></p>
        </div>
    </div>
</footer>
<?php /**PATH /var/www/real-estate-site/resources/views/partials/public-footer.blade.php ENDPATH**/ ?>