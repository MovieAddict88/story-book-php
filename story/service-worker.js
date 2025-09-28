const STATIC_CACHE_NAME = 'story-time-static-v1';
const DYNAMIC_CACHE_NAME = 'story-time-dynamic-v1';

// App shell files to be cached on install
const urlsToCache = [
    '/',
    '/index.html',
    '/css/styles.css',
    '/js/app.js',
    '/manifest.json',
    '/images/icon-192.png',
    '/images/icon-512.png'
];

// Install the service worker and cache the app shell
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(STATIC_CACHE_NAME)
            .then(cache => {
                console.log('Opened static cache');
                return cache.addAll(urlsToCache);
            })
    );
});

// Clean up old caches on activation
self.addEventListener('activate', event => {
    const cacheWhitelist = [STATIC_CACHE_NAME, DYNAMIC_CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Intercept fetch requests
self.addEventListener('fetch', event => {
    const requestUrl = new URL(event.request.url);

    // Strategy: Cache-first, then network for API and image calls
    // This makes the app offline-capable for content the user has already viewed.
    if (requestUrl.href.includes('/api/')) {
        event.respondWith(
            caches.open(DYNAMIC_CACHE_NAME).then(cache => {
                return cache.match(event.request).then(response => {
                    const fetchPromise = fetch(event.request).then(networkResponse => {
                        cache.put(event.request, networkResponse.clone());
                        return networkResponse;
                    });
                    // Return cached response if available, otherwise wait for network
                    return response || fetchPromise;
                });
            })
        );
        return;
    }

    // Strategy: Stale-while-revalidate for static assets
    // This ensures the app loads fast, while also getting updates in the background.
    event.respondWith(
        caches.match(event.request).then(cachedResponse => {
            const fetchPromise = fetch(event.request).then(networkResponse => {
                return caches.open(STATIC_CACHE_NAME).then(cache => {
                    cache.put(event.request, networkResponse.clone());
                    return networkResponse;
                });
            });
            return cachedResponse || fetchPromise;
        })
    );
});