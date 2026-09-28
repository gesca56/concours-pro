// Service worker Concours-Pro : rend le site installable (PWA).
// Les pages restent toujours chargées depuis le réseau (données à jour, sessions),
// seule une page hors ligne est servie en cas d'absence de connexion.
const CACHE = 'concours-pro-v1';
const OFFLINE_URL = '/offline.html';

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((cache) => cache.addAll([OFFLINE_URL, '/icons/icon-192.png'])));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.mode !== 'navigate') return;

    event.respondWith(fetch(event.request).catch(() => caches.match(OFFLINE_URL)));
});
