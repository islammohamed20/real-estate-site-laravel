const PushManager = {
    subscription: null,
    vapidPublicKey: null,
    async init() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return false;
        try {
            const reg = await navigator.serviceWorker.ready;
            this.subscription = await reg.pushManager.getSubscription();
            this.updateUI();
            return true;
        } catch (e) { return false; }
    },
    async fetchVapidKey() {
        if (this.vapidPublicKey) return this.vapidPublicKey;
        try {
            const res = await fetch('/real-statement-control/push/vapid-key', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            this.vapidPublicKey = data.publicKey;
            return this.vapidPublicKey;
        } catch (e) { return null; }
    },
    async subscribe() {
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') { window.toast?.warning('Notification permission denied'); return false; }
        const key = await this.fetchVapidKey();
        if (!key) return false;
        const reg = await navigator.serviceWorker.ready;
        try {
            this.subscription = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(key),
            });
            const sub = this.subscription.toJSON();
            const res = await fetch('/real-statement-control/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ endpoint: sub.endpoint, keys: sub.keys }),
            });
            const data = await res.json();
            if (data.success) { window.toast?.success('Push notifications enabled'); this.updateUI(); return true; }
            return false;
        } catch (e) { window.toast?.danger('Failed to enable push'); return false; }
    },
    async unsubscribe() {
        if (!this.subscription) return true;
        try {
            await this.subscription.unsubscribe();
            await fetch('/real-statement-control/push/unsubscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ endpoint: this.subscription.endpoint }),
            });
            this.subscription = null;
            window.toast?.success('Push notifications disabled');
            this.updateUI();
            return true;
        } catch (e) { return false; }
    },
    isEnabled() { return this.subscription !== null; },
    updateUI() { document.dispatchEvent(new CustomEvent('push-status-changed', { detail: { enabled: this.isEnabled() } })); },
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
        return outputArray;
    },
};
document.addEventListener('DOMContentLoaded', () => PushManager.init());
