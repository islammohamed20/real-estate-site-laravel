const CACHE_NAME = 'real-estate-pwa-v1';

export function registerPwa() {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    window.addEventListener('load', async () => {
        try {
            await navigator.serviceWorker.register('/sw.js');
        } catch (error) {
            console.error('Service worker registration failed', error);
        }
    });
}

export function setupInstallPrompt() {
    let deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', (event) => {
        // Store the event for later use, but DON'T preventDefault
        // so the browser can show its native install banner if desired.
        // If you want a custom install button, call deferredPrompt.prompt() on click.
        deferredPrompt = event;
        window.dispatchEvent(new CustomEvent('pwa-install-ready'));
    });

    // Expose a global function for custom install buttons to use
    window.pwaPromptInstall = async () => {
        if (!deferredPrompt) {
            return false;
        }
        deferredPrompt.prompt();
        await deferredPrompt.userChoice;
        deferredPrompt = null;
        return true;
    };
}

export function setupOfflineDetection() {
    const syncStatus = () => {
        document.documentElement.dataset.online = navigator.onLine ? 'true' : 'false';
    };

    window.addEventListener('online', syncStatus);
    window.addEventListener('offline', syncStatus);
    syncStatus();
}

export function initPwa() {
    registerPwa();
    setupInstallPrompt();
    setupOfflineDetection();
}
