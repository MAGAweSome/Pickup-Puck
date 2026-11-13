/*
 * Pickup Puck Service Worker
 * Version: 1.0.0
 * Provides offline caching, network-first strategy for dynamic pages,
 * stale-while-revalidate for static assets, and full PWA offline support.
 */

const CACHE_VERSION = 'v1.0.0';
const STATIC_CACHE_NAME = `pickup-puck-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE_NAME = `pickup-puck-dynamic-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline.html';

// Critical core assets to pre-cache on install
const PRECACHE_ASSETS = [
    OFFLINE_URL,
    '/manifest.json',
    '/site.webmanifest',
    '/icons/icon-48x48.png',
    '/icons/icon-72x72.png',
    '/icons/icon-96x96.png',
    '/icons/icon-128x128.png',
    '/icons/icon-144x144.png',
    '/icons/icon-152x152.png',
    '/icons/icon-192x192.png',
    '/icons/icon-256x256.png',
    '/icons/icon-384x384.png',
    '/icons/icon-512x512.png',
    '/icons/maskable-icon-192x192.png',
    '/icons/maskable-icon-512x512.png',
    '/icons/apple-touch-icon.png',
    '/icons/apple-touch-icon-180x180.png',
    '/icons/favicon-32x32.png',
    '/icons/favicon-16x16.png'
];

// Service Worker Install
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[PWA] Pre-caching partial failure:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// Service Worker Activation & Cache Cleanup
self.addEventListener('activate', (event) => {
    const currentCaches = [STATIC_CACHE_NAME, DYNAMIC_CACHE_NAME];
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName.startsWith('pickup-puck-') && !currentCaches.includes(cacheName)) {
                        console.log('[PWA] Deleting obsolete cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Helper: Check if request is an HTML navigation
function isNavigationRequest(request) {
    return request.mode === 'navigate' || 
           (request.method === 'GET' && request.headers.get('accept')?.includes('text/html'));
}

// Helper: Check if request is a static asset
function isStaticAsset(url) {
    return url.pathname.startsWith('/icons/') ||
           url.pathname.startsWith('/photos/') ||
           url.pathname.startsWith('/build/') ||
           url.pathname.endsWith('.css') ||
           url.pathname.endsWith('.js') ||
           url.pathname.endsWith('.woff') ||
           url.pathname.endsWith('.woff2') ||
           url.pathname.endsWith('.ttf') ||
           url.pathname.endsWith('.svg') ||
           url.pathname.endsWith('.png') ||
           url.pathname.endsWith('.jpg') ||
           url.pathname.endsWith('.jpeg') ||
           url.pathname.endsWith('.ico');
}

// Fetch Event
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Only process GET requests from same origin (or CDN fonts)
    if (request.method !== 'GET') {
        return;
    }

    // Ignore browser extensions or non-http(s) schemes
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // 1. Navigation requests (HTML pages): Network-First with Cache Fallback and Offline Page
    if (isNavigationRequest(request)) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(DYNAMIC_CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // Try to match the page from dynamic cache
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // If page is not in cache, return the offline fallback page
                    const offlinePage = await caches.match(OFFLINE_URL);
                    return offlinePage || new Response('Offline - No connection available', {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: { 'Content-Type': 'text/html' }
                    });
                })
        );
        return;
    }

    // 2. Static Assets (CSS, JS, Fonts, Images, Icons): Stale-While-Revalidate
    if (isStaticAsset(url) || url.origin.includes('fonts.googleapis.com') || url.origin.includes('fonts.gstatic.com') || url.origin.includes('cdnjs.cloudflare.com')) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                const fetchPromise = fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        const targetCache = isStaticAsset(url) ? STATIC_CACHE_NAME : DYNAMIC_CACHE_NAME;
                        caches.open(targetCache).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                }).catch(() => {
                    // Ignore background fetch failure when offline
                });

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // 3. All other requests: Network-first
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});

// Handle messages from the client
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// ==========================================
// Web Push Notifications
// ==========================================
self.addEventListener('push', (event) => {
    let payload = {
        title: 'Pickup Puck Alert',
        body: 'You have a new update from Pickup Puck.',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/favicon-32x32.png',
        data: { url: '/home' }
    };

    if (event.data) {
        try {
            payload = Object.assign(payload, event.data.json());
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    const options = {
        body: payload.body,
        icon: payload.icon || '/icons/icon-192x192.png',
        badge: payload.badge || '/icons/favicon-32x32.png',
        vibrate: [200, 100, 200],
        data: payload.data || { url: payload.url || '/home' },
        actions: [
            { action: 'open', title: 'View Roster' }
        ],
        tag: payload.tag || 'pickup-puck-alert',
        renotify: true
    };

    event.waitUntil(
        self.registration.showNotification(payload.title, options)
    );
});

// Handle clicking on notification
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/home';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            for (let i = 0; i < windowClients.length; i++) {
                const client = windowClients[i];
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

