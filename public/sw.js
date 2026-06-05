const CACHE_NAME = 'binapersadajs-v5';
const OFFLINE_URL = '/offline';

const PRECACHE_URLS = [
  OFFLINE_URL,
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
  '/favicon.ico',
  '/icons/favicon-32x32.png',
  '/icons/apple-touch-icon.png'
];

self.skipWaiting();

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => Promise.allSettled(
        PRECACHE_URLS.map((url) => cache.add(url))
      ))
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

self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});

function isAdminRequest(url) {
  return url.pathname === '/paneladmin' || url.pathname.startsWith('/paneladmin/');
}

function isPwaMetadataRequest(url) {
  return url.pathname === '/manifest.json'
    || url.pathname === '/site.webmanifest'
    || url.pathname.startsWith('/icons/icon-')
    || url.pathname.startsWith('/icons/maskable-')
    || url.pathname.startsWith('/icons/maskable-icon-')
    || url.pathname.startsWith('/icons/android-chrome-');
}

self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  if (!['http:', 'https:'].includes(url.protocol)) {
    return;
  }

  if (request.method !== 'GET') {
    return;
  }

  if (isAdminRequest(url)) {
    event.respondWith(
      fetch(request, { cache: 'no-store' })
        .catch(() => caches.match(request))
    );
    return;
  }

  if (isPwaMetadataRequest(url)) {
    event.respondWith(fetch(request, { cache: 'reload' }));
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
