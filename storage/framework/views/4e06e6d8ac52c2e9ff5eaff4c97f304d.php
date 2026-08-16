<div id="app-confirm-modal" class="app-modal hidden" role="dialog" aria-modal="true" aria-labelledby="app-confirm-title">
    <div class="app-modal__overlay" data-modal-close></div>
    <div class="app-modal__panel">
        <div class="app-modal__header">
            <h3 id="app-confirm-title" class="app-modal__title" data-modal-title><?php echo e(__('Confirm action')); ?></h3>
            <button type="button" class="app-modal__close" data-modal-close aria-label="<?php echo e(__('Close')); ?>">&times;</button>
        </div>
        <div class="app-modal__body" data-modal-body>
            <?php echo e(__('Are you sure?')); ?>

        </div>
        <div class="app-modal__footer">
            <button type="button" class="app-button" data-modal-confirm><?php echo e(__('Confirm')); ?></button>
            <button type="button" class="app-button--ghost" data-modal-cancel><?php echo e(__('Cancel')); ?></button>
        </div>
    </div>
</div>
<?php /**PATH /var/www/real-estate-site/resources/views/partials/app-confirm-modal.blade.php ENDPATH**/ ?>