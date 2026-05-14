const VERSION = 'v1';
const STATIC_CACHE = `digiproper-static-${VERSION}`;
const RUNTIME_CACHE = `digiproper-runtime-${VERSION}`;

const PRECACHE_URLS = [
    '/offline.html',
    '/manifest.webmanifest',
    '/favicon.svg',
    '/brand/digiproper-icon@256.png',
    '/brand/digiproper-icon@512.png',
    '/brand/digiproper-square@512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key !== STATIC_CACHE && key !== RUNTIME_CACHE)
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

function isFontHost(url) {
    return url.hostname === 'fonts.bunny.net';
}

async function networkFirst(request) {
    try {
        return await fetch(request);
    } catch (err) {
        const cache = await caches.open(STATIC_CACHE);
        const offline = await cache.match('/offline.html');
        return offline || new Response('Offline', { status: 503, statusText: 'Offline' });
    }
}

async function cacheFirst(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    if (cached) {
        return cached;
    }
    const response = await fetch(request);
    if (response && response.ok) {
        cache.put(request, response.clone());
    }
    return response;
}

async function staleWhileRevalidate(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    const network = fetch(request)
        .then((response) => {
            if (response && response.ok) {
                cache.put(request, response.clone());
            }
            return response;
        })
        .catch(() => cached);
    return cached || network;
}

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);
    const sameOrigin = url.origin === self.location.origin;

    if (request.mode === 'navigate') {
        event.respondWith(networkFirst(request));
        return;
    }

    if (sameOrigin) {
        if (url.pathname.startsWith('/build/')) {
            event.respondWith(cacheFirst(request, RUNTIME_CACHE));
            return;
        }

        if (url.pathname.startsWith('/brand/') || url.pathname.startsWith('/favicon')) {
            event.respondWith(staleWhileRevalidate(request, RUNTIME_CACHE));
            return;
        }

        return;
    }

    if (isFontHost(url)) {
        event.respondWith(staleWhileRevalidate(request, RUNTIME_CACHE));
    }
});
