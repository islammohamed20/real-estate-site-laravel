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
        event.preventDefault();
        deferredPrompt = event;
        window.dispatchEvent(new CustomEvent('pwa-install-ready'));
    });

    window.addEventListener('appinstall', async () => {
        if (!deferredPrompt) {
            return;
        }

        deferredPrompt.prompt();
        await deferredPrompt.userChoice;
        deferredPrompt = null;
    });
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
