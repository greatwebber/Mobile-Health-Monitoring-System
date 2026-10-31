const CACHE_VERSION = 'edehealth-static-v1';
const OFFLINE_URL = new URL('./offline.html', self.location.href).href;
const STATIC_ASSETS = [
    OFFLINE_URL,
    new URL('./manifest.webmanifest', self.location.href).href,
    new URL('./icons/icon-192.png', self.location.href).href,
    new URL('./icons/icon-512.png', self.location.href).href,
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_VERSION)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key.startsWith('edehealth-') && key !== CACHE_VERSION)
                    .map((key) => caches.delete(key)),
            ))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET' || url.origin !== self.location.origin) return;

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL)),
        );
        return;
    }

    const isSafeStaticAsset = ['style', 'script', 'image', 'font'].includes(request.destination)
        && (url.pathname.includes('/build/') || url.pathname.includes('/icons/'));

    if (!isSafeStaticAsset) return;

    event.respondWith(
        caches.match(request).then((cached) => {
            const networkResponse = fetch(request).then((response) => {
                if (response.ok && response.type === 'basic') {
                    const copy = response.clone();
                    caches.open(CACHE_VERSION).then((cache) => cache.put(request, copy));
                }

                return response;
            });

            return cached || networkResponse;
        }),
    );
});
