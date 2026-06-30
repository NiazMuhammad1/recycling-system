const CACHE_NAME = 'itad-offline-v1';
const FILES_TO_CACHE = [
  '/js/app.js',      // check the actual path on live
  '/css/app.css',    // check the actual path on live
  '/images/icon-192.png',
  '/images/icon-512.png'
];

// Install event
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(FILES_TO_CACHE);
    }).catch(err => console.warn('SW cache addAll failed:', err))
  );
  self.skipWaiting();
});