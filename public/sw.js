const CACHE_NAME = 'bpjs-pwa-v6';
const OFFLINE_URL = '/offline';

const PRECACHE_URLS = [
  '/',
  OFFLINE_URL,
  '/manifest.json',
  '/web/css/style.css',
  '/web/plugins/bootstrap/bootstrap.min.css',
  '/web/plugins/fontawesome/css/all.min.css',
  '/web/plugins/jQuery/jquery.min.js',
  '/web/plugins/bootstrap/bootstrap.min.js',
  '/web/js/script.js',
  '/assets/css/soft-ui-dashboard.css',
  '/assets/js/soft-ui-dashboard.min.js',
  '/assets/js/core/bootstrap.min.js',
  '/assets/js/core/popper.min.js',
  '/pwa/icons/icon-192x192.png',
  '/pwa/icons/icon-512x512.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(PRECACHE_URLS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(
      keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
    )).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  if (request.method !== 'GET') {
    return;
  }

  if (url.pathname.startsWith('/paneladmin')) {
    event.respondWith(fetch(request));
    return;
  }

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          const copy = response.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
          return response;
        })
        .catch(() => caches.match(request).then((cached) => cached || caches.match(OFFLINE_URL)))
    );
    return;
  }

  event.respondWith(
    caches.match(request).then((cached) => {
      const networkFetch = fetch(request)
        .then((response) => {
          if (response && response.status === 200) {
            const copy = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
          }

          return response;
        })
        .catch(() => cached);

      return cached || networkFetch;
    })
  );
});
