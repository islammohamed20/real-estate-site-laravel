<?php
    $hasLogos = $companyProfile?->logo_light_path || $companyProfile?->logo_dark_path || $companyProfile?->logo_path;
    $size = $size ?? 'desktop';
    $height = $size === 'mobile'
        ? (int) ($companyProfile?->logo_height_mobile ?? 36)
        : (int) ($companyProfile?->logo_height_desktop ?? 40);
    $logoStyle = "height: {$height}px";
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasLogos): ?>
    <span class="flex items-center gap-2">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($companyProfile?->logo_light_path || $companyProfile?->logo_path): ?>
            <img
                src="<?php echo e($companyProfile->logo_light_path ?? $companyProfile->logo_path); ?>"
                alt="<?php echo e($companyProfile->name ?? config('app.name')); ?>"
                class="block h-auto w-auto object-contain dark:hidden"
                style="<?php echo e($logoStyle); ?>"
            >
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($companyProfile?->logo_dark_path || $companyProfile?->logo_path): ?>
            <img
                src="<?php echo e($companyProfile->logo_dark_path ?? $companyProfile->logo_path); ?>"
                alt="<?php echo e($companyProfile->name ?? config('app.name')); ?>"
                class="hidden h-auto w-auto object-contain dark:block"
                style="<?php echo e($logoStyle); ?>"
            >
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </span>
<?php else: ?>
    <span class="leading-tight">
        <strong class="block text-base font-bold text-white"><?php echo e(config('app.name')); ?></strong>
        <span class="block text-[11px] font-medium text-brand-300"><?php echo e(__('Real estate management system')); ?></span>
    </span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /var/www/real-estate-site/resources/views/partials/company-logo.blade.php ENDPATH**/ ?>