/* public/sw.js — Coll-System V2 PWA Service Worker */
'use strict';

const CACHE_V  = 'coll-v2-3';
const OFFLINE  = 'offline.html';

/* ═══ Install ════════════════════════════════════════════════════════════════ */
self.addEventListener('install', e =>
    e.waitUntil(
        caches.open(CACHE_V)
            .then(c => c.add(new Request(self.registration.scope + OFFLINE)))
            .then(() => self.skipWaiting())
    )
);

/* ═══ Activate ═══════════════════════════════════════════════════════════════ */
self.addEventListener('activate', e =>
    e.waitUntil(
        caches.keys()
            .then(ks => Promise.all(ks.filter(k => k !== CACHE_V).map(k => caches.delete(k))))
            .then(() => self.clients.claim())
    )
);

/* ═══ Fetch ══════════════════════════════════════════════════════════════════ */
self.addEventListener('fetch', e => {
    const req = e.request;
    const url = new URL(req.url);

    /* Ignorar peticiones cross-origin (CDN, Google Fonts, etc.) */
    if (url.origin !== location.origin) return;

    /* POST pago-card/guardar → cola offline */
    if (req.method === 'POST' && url.pathname.includes('pago-card/guardar')) {
        e.respondWith(pagoGuardar(req));
        return;
    }

    /* Assets estáticos (.css .js .woff .png …) → Cache First */
    if (/\.(css|js|woff2?|ttf|png|jpg|jpeg|gif|svg|ico)(\?.*)?$/.test(url.pathname)) {
        e.respondWith(cacheFirst(req));
        return;
    }

    /* Navegación HTML → Network First, fallback caché → página offline */
    if (req.mode === 'navigate') {
        e.respondWith(navHandler(req));
        return;
    }

    /* AJAX GET → Network First, fallback caché */
    e.respondWith(networkFirst(req));
});

/* ── Estrategia: Cache First ─────────────────────────────────────────────── */
async function cacheFirst(req) {
    /* NO usar ignoreSearch: el query ?v=filemtime es el cache-busting de los assets */
    const hit = await caches.match(req);
    if (hit) return hit;
    try {
        const res = await fetch(req);
        if (res.ok) (await caches.open(CACHE_V)).put(req, res.clone());
        return res;
    } catch {
        return new Response('', { status: 503 });
    }
}

/* ── Estrategia: Network First ──────────────────────────────────────────── */
async function networkFirst(req) {
    try {
        const res = await fetch(req);
        if (res.ok) (await caches.open(CACHE_V)).put(req, res.clone());
        return res;
    } catch {
        return (await caches.match(req)) ||
            new Response(JSON.stringify({ offline: true }),
                { headers: { 'Content-Type': 'application/json' } });
    }
}

/* ── Estrategia: Navigation ──────────────────────────────────────────────── */
async function navHandler(req) {
    try {
        const res = await fetch(req);
        if (res.ok) (await caches.open(CACHE_V)).put(req, res.clone());
        return res;
    } catch {
        return (await caches.match(req)) ||
               (await caches.match(self.registration.scope + OFFLINE)) ||
               new Response('<h1>Sin conexión</h1>', { headers: { 'Content-Type': 'text/html' } });
    }
}

/* ── POST guardar pago → intento de red, si falla → cola IndexedDB ────────── */
async function pagoGuardar(req) {
    try {
        return await fetch(req.clone());
    } catch {
        const body = await req.text();
        await idbPush({ url: req.url, body, ts: Date.now() });
        /* Notificar a todos los clientes abiertos */
        (await self.clients.matchAll()).forEach(c =>
            c.postMessage({ type: 'PAGO_QUEUED' })
        );
        return new Response(
            JSON.stringify({ offline_queued: true }),
            { status: 200, headers: { 'Content-Type': 'application/json' } }
        );
    }
}

/* ═══ Background Sync (Android Chrome) ══════════════════════════════════════ */
self.addEventListener('sync', e => {
    if (e.tag === 'sync-pagos') e.waitUntil(flushQueue());
});

/* ═══ Mensajes desde los clientes ═══════════════════════════════════════════ */
self.addEventListener('message', e => {
    const port = e.ports[0];
    if (e.data?.type === 'SYNC_NOW')  flushQueue().then(r => port?.postMessage({ type: 'SYNC_DONE', ...r }));
    if (e.data?.type === 'GET_COUNT') idbAll().then(items => port?.postMessage({ type: 'COUNT', n: items.length }));
});

/* ═══ Cola IndexedDB ═════════════════════════════════════════════════════════ */
const IDB_NAME  = 'coll-offline';
const IDB_STORE = 'pagos';

function idbOpen() {
    return new Promise((ok, fail) => {
        const r = indexedDB.open(IDB_NAME, 1);
        r.onupgradeneeded = ev => ev.target.result.createObjectStore(IDB_STORE, { keyPath: 'id', autoIncrement: true });
        r.onsuccess = ev => ok(ev.target.result);
        r.onerror   = ()  => fail(r.error);
    });
}
async function idbPush(data) {
    const db = await idbOpen();
    return new Promise((ok, fail) => {
        const tx = db.transaction(IDB_STORE, 'readwrite');
        tx.objectStore(IDB_STORE).add(data);
        tx.oncomplete = ok; tx.onerror = () => fail(tx.error);
    });
}
async function idbAll() {
    const db = await idbOpen();
    return new Promise((ok, fail) => {
        const r = db.transaction(IDB_STORE, 'readonly').objectStore(IDB_STORE).getAll();
        r.onsuccess = () => ok(r.result); r.onerror = () => fail(r.error);
    });
}
async function idbRemove(id) {
    const db = await idbOpen();
    return new Promise((ok, fail) => {
        const tx = db.transaction(IDB_STORE, 'readwrite');
        tx.objectStore(IDB_STORE).delete(id);
        tx.oncomplete = ok; tx.onerror = () => fail(tx.error);
    });
}

/* ── Enviar cola al servidor ─────────────────────────────────────────────── */
async function flushQueue() {
    const items = await idbAll();
    if (!items.length) return { ok: 0, fail: 0 };

    let ok = 0, fail = 0, csrf = 0;

    for (const item of items) {
        try {
            const res = await fetch(item.url, {
                method: 'POST', body: item.body,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                credentials: 'include',
            });
            if (res.status === 419) { csrf++; }           /* CSRF expirado → no borrar */
            else if (res.status < 500) { await idbRemove(item.id); ok++; }
            else fail++;
        } catch { fail++; }
    }

    const clients = await self.clients.matchAll();
    clients.forEach(c => c.postMessage({ type: 'SYNC_RESULT', ok, fail, csrf, total: items.length }));
    return { ok, fail, csrf };
}
