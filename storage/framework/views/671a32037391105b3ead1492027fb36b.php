<?php ($hideSplash = true); ?>


<?php $__env->startSection('content'); ?>
    <div class="relative mx-auto mt-10 max-w-md sm:mt-16">
        
        <div class="pointer-events-none absolute -inset-10 -z-10" aria-hidden="true">
            <div class="animate-float-slow absolute -top-12 right-0 h-44 w-44 rounded-full bg-brand-500/15 blur-3xl"></div>
            <div class="animate-float-slower absolute -bottom-14 -left-8 h-52 w-52 rounded-full bg-violet-500/15 blur-3xl"></div>
            <div class="animate-float-slow absolute top-1/3 -right-6 h-28 w-28 rounded-full bg-sky-500/10 blur-2xl" style="animation-delay:1.2s"></div>
        </div>

        <div class="stagger-item app-card space-y-6 p-6 sm:p-8 transition-all duration-500 hover:border-brand-500/30">
            <div class="text-center">
                <span class="animate-glow-pulse mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-gradient-to-br from-brand-500 to-violet-600 shadow-lg shadow-brand-500/30">
                    <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8.5Z" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                </span>
                <h1 class="stagger-item mt-4 text-2xl font-bold tracking-tight text-white" style="animation-delay:120ms"><?php echo e(__('Welcome back')); ?></h1>
                <p class="stagger-item mt-2 text-sm text-slate-400" style="animation-delay:200ms"><?php echo e(__('Internal access for administrators and sales teams.')); ?></p>
            </div>

            <form method="POST" action="/real-statement-control/login" class="space-y-4">
                <?php echo csrf_field(); ?>

                <label class="stagger-item block space-y-2" style="animation-delay:280ms">
                    <span class="text-sm font-medium text-slate-300"><?php echo e(__('Email')); ?></span>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required inputmode="email" autocomplete="email" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="<?php echo e(__('you@company.com')); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="block text-sm text-rose-400"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>

                <label class="stagger-item block space-y-2" style="animation-delay:360ms">
                    <span class="text-sm font-medium text-slate-300"><?php echo e(__('Password')); ?></span>
                    <input type="password" name="password" required autocomplete="current-password" class="app-input transition-all duration-300 focus:scale-[1.01]" placeholder="••••••••">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="block text-sm text-rose-400"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>

                <label class="stagger-item flex items-center gap-3 rounded-2xl bg-white/5 px-4 py-3 text-sm text-slate-300 transition-colors duration-300 hover:bg-white/10" style="animation-delay:440ms">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-700 bg-slate-900 text-brand-500 focus:ring-brand-500">
                    <?php echo e(__('Remember me')); ?>

                </label>

                <button type="submit" class="stagger-item app-button w-full transition-transform duration-300 hover:scale-[1.02] active:scale-[0.97]" style="animation-delay:520ms"><?php echo e(__('Sign in')); ?></button>
            </form>
        </div>

        <p class="stagger-item mt-6 text-center text-sm text-slate-500" style="animation-delay:600ms">
            <a href="<?php echo e(route('home')); ?>" class="transition hover:text-slate-300">← <?php echo e(__('Back to website')); ?></a>
        </p>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const TOKEN_TTL = 1000 * 60 * 110; // Refresh before the 120-minute session expires
        let lastRefresh = Date.now();
        let lastActivity = Date.now();

        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const tokenInput = document.querySelector('input[name="_token"]');

        function updateToken(token) {
            if (tokenMeta) tokenMeta.setAttribute('content', token);
            if (tokenInput) tokenInput.value = token;
        }

        function refreshToken() {
            fetch('<?php echo e(route('auth.refresh-token')); ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => {
                    if (!response.ok) throw new Error('Token refresh failed');
                    return response.json();
                })
                .then(data => {
                    if (data.token) {
                        updateToken(data.token);
                        lastRefresh = Date.now();
                    }
                })
                .catch(() => {
                    // If the session is gone, reload to get a fresh token
                    window.location.reload();
                });
        }

        function onActivity() {
            lastActivity = Date.now();
        }

        ['click', 'keydown', 'touchstart', 'scroll'].forEach(event => {
            document.addEventListener(event, onActivity, { passive: true });
        });

        // Refresh token while the tab is active and the page has not been reloaded
        setInterval(() => {
            if (document.hidden) return;
            if (Date.now() - lastRefresh > TOKEN_TTL) refreshToken();
        }, 60000);

        // If the tab returns after being hidden too long, reload; otherwise refresh token
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) return;
            if (Date.now() - lastActivity > TOKEN_TTL) {
                window.location.reload();
                return;
            }
            refreshToken();
        });
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/real-estate-site/resources/views/auth/login.blade.php ENDPATH**/ ?>