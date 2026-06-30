const CACHE_NAME = 'collection-app-cache-v1';
const OFFLINE_ASSETS = [
    // Add the specific URLs or asset paths you want cached offline
    '/css/app.css', 
    '/js/app.js',
    // You can also cache the edit page if the URL structure allows it
];

// Install the Service Worker and cache essential files
self.addEventListener('install', (e) => {
    e.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(OFFLINE_ASSETS).catch(() => console.log('Asset caching skipped'));
        })
    );
});

// Intercept network requests
self.addEventListener('fetch', (event) => {
    // Only intercept standard page/asset requests (GET)
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // If we get a valid response from the net, clone it into cache
                if (response.status === 200) {
                    const resClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, resClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Network failed (offline)! Try to serve from browser cache
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Fallback if nothing matches
                });
            })
    );
});