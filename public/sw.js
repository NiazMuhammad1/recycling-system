const CACHE_NAME = 'collection-app-cache-v1';
const OFFLINE_ASSETS = [
    // Add the specific static asset paths you want cached offline
    '/css/app.css', 
    '/js/app.js',
];

// 1. INSTALL EVENT (Combines file caching + instant activation)
self.addEventListener('install', (e) => {
    self.skipWaiting(); // Forces the waiting service worker to become active instantly
    e.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(OFFLINE_ASSETS).catch(() => console.log('Asset caching skipped'));
        })
    );
});

// 2. ACTIVATE EVENT (Forces the service worker to control all open browser tabs immediately)
self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim()); 
});

// 3. FETCH EVENT (Intercepts network requests to save pages or load them offline)
self.addEventListener('fetch', (event) => {
    // Only intercept standard page/asset requests (GET)
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // If we get a valid response from the net, clone it into cache dynamically
                if (response.status === 200) {
                    const resClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, resClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Network failed (entirely offline)! Serve from browser cache fallback
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Fallback if nothing matches the requested URL
                });
            })
    );
});