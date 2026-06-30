const CACHE_NAME = 'itad-offline-cache-v1';
const FILES_TO_CACHE = [
    '/',
    '/driver/collections/22/offline',
    '/js/app.js',
    '/css/app.css'
];

// Install event
self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(FILES_TO_CACHE))
    );
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keyList => {
            return Promise.all(
                keyList.map(key => {
                    if (key !== CACHE_NAME) return caches.delete(key);
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event
self.addEventListener('fetch', e => {
    e.respondWith(
        caches.match(e.request).then(resp => resp || fetch(e.request).catch(() => {
            // Offline fallback
            return caches.match('/driver/collections/22/offline');
        }))
    );
});