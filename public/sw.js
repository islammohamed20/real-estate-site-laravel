const CACHE_NAME = 'real-estate-pwa-v8';
const STATIC_ASSETS = [
  '/offline.html',
  '/manifest.webmanifest',
  '/icons/icon.svg',
  '/icons/icon-maskable.svg',
];

/* ── Install: cache shell assets ── */
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      await Promise.all(STATIC_ASSETS.map(async (asset) => {
        try {
          const response = await fetch(asset, { cache: 'reload' });
          if (response.ok) await cache.put(asset, response);
        } catch (_) {}
      }));
    })
  );
  self.skipWaiting();
});

/* ── Activate: prune old caches ── */
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

/* ── Fetch: cache static assets only, never HTML pages ── */
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);
  if (request.method !== 'GET' || !['http:', 'https:'].includes(url.protocol)) return;

  // HTML documents (navigations) must always come from the network: they carry
  // CSRF tokens and user-specific data, and stale HTML breaks form actions.
  const isNavigation = request.mode === 'navigate'
    || (request.headers.get('accept') || '').includes('text/html');
  const isStaticAsset = /\.(?:css|js|mjs|png|jpe?g|gif|svg|webp|avif|ico|woff2?|ttf|otf|map)$/i.test(url.pathname)
    || url.pathname.startsWith('/build/')
    || url.pathname.startsWith('/icons/');

  if (isNavigation || !isStaticAsset) {
    event.respondWith(
      fetch(request).catch(async () => {
        if (isNavigation) {
          const offline = await caches.match('/offline.html');
          if (offline) return offline;
        }
        return new Response('Offline', { status: 503, headers: { 'Content-Type': 'text/plain' } });
      })
    );
    return;
  }

  event.respondWith(
    fetch(request)
      .then((response) => {
        if (response.ok && url.origin === self.location.origin) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then((c) => c.put(request, clone)).catch(() => {});
        }
        return response;
      })
      .catch(async () => {
        const cached = await caches.match(request);
        if (cached) return cached;
        return new Response('Offline', { status: 503, headers: { 'Content-Type': 'text/plain' } });
      })
  );
});

/* ══════════════════════════════════════════════════════════════
   PUSH NOTIFICATIONS
   ══════════════════════════════════════════════════════════════ */

self.addEventListener('push', (event) => {
  if (!event.data) return;

  let payload;
  try {
    payload = event.data.json();
  } catch (_) {
    payload = { title: 'EstateFlow', body: event.data.text() };
  }

  const title = payload.title || 'EstateFlow';
  const options = {
    body: payload.body || '',
    icon: payload.icon || '/icons/icon.svg',
    badge: payload.badge || '/icons/icon.svg',
    tag: payload.tag || 'estateflow-notification',
    renotify: payload.renotify !== false,
    vibrate: [100, 50, 100],
    data: payload.data || {},
    actions: payload.actions || [],
  };

  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  const url = event.notification.data?.url || '/real-statement-control/dashboard';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      // Focus existing window if open
      for (const client of clientList) {
        if (client.url.includes(self.location.origin) && 'focus' in client) {
          client.navigate(url);
          return client.focus();
        }
      }
      // Otherwise open new window
      if (clients.openWindow) {
        return clients.openWindow(url);
      }
    })
  );
});
