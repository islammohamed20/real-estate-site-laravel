const CACHE_NAME = 'real-estate-pwa-v5';
const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/manifest.webmanifest',
  '/icons/icon.svg',
  '/icons/icon-maskable.svg',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(async (cache) => {
      await Promise.all(STATIC_ASSETS.map(async (asset) => {
        try {
          const response = await fetch(asset, { cache: 'reload' });
          if (response.ok) {
            await cache.put(asset, response);
          }
        } catch (error) {
          console.warn('Skipping PWA asset during install:', asset, error);
        }
      }));
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(
      keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
    ))
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((response) => {
        const cloned = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(event.request, cloned));
        return response;
      })
      .catch(async () => {
        const cached = await caches.match(event.request);
        if (cached) {
          return cached;
        }

        const offline = await caches.match('/offline.html');
        return offline || new Response('Offline', { status: 503, headers: { 'Content-Type': 'text/plain' } });
      })
  );
});
