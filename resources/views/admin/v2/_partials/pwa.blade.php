{{-- resources/views/admin/v2/_partials/pwa.blade.php --}}
{{-- Incluido SOLO en vistas V2. Registra el SW, el manifest y el indicador offline. --}}

{{-- ── Meta + manifest (en head via @stack('pwa')) ────────────────────────── --}}
<link rel="manifest" href="{{ url('manifest.json') }}">
<meta name="theme-color" content="#6366f1">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Coll-System">
<link rel="apple-touch-icon" href="{{ url('pwa/icon/180') }}">

{{-- ── Barra flotante "sin conexión" ──────────────────────────────────────── --}}
<div id="pwa-bar" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:9990;
     background:#1e1b4b;color:#fff;font-size:12px;padding:7px 14px;
     align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
  <span>
    &#128225; Sin conexión &mdash;
    <strong id="pwa-cnt">0</strong> pago(s) guardado(s) localmente
  </span>
  <button onclick="pwaSyncNow()"
          style="background:#6366f1;color:#fff;border:none;border-radius:6px;
                 padding:4px 12px;font-size:11px;cursor:pointer;font-weight:600;">
    Sincronizar
  </button>
</div>

{{-- ── Service Worker + lógica PWA ───────────────────────────────────────── --}}
<script>
(function () {
    if (!('serviceWorker' in navigator)) return;

    /* ── Registro del SW ──────────────────────────────────────────── */
    navigator.serviceWorker.register('{{ url("sw.js") }}', {
        scope: '{{ url("/") }}/'
    }).catch(function (err) {
        console.warn('[PWA] registro falló:', err);
    });

    /* ── Mensajes entrantes del SW ────────────────────────────────── */
    navigator.serviceWorker.addEventListener('message', function (e) {
        var d = e.data; if (!d) return;

        if (d.type === 'PAGO_QUEUED') {
            pwaAddCount(1);
            /* Si había señal, mostrar el toast info */
            if (navigator.onLine) {
                /* No debería ocurrir, pero por si acaso */
            }
        }

        if (d.type === 'SYNC_RESULT') {
            pwaRefreshCount();
            if (d.ok > 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success',
                        title: d.ok + ' pago(s) sincronizado(s)',
                        showConfirmButton: false, timer: 2500 });
                }
            }
            if (d.fail > 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'warning',
                        title: d.fail + ' pago(s) no pudieron sincronizarse',
                        text: 'Verifica tu conexión e intenta de nuevo.',
                        showConfirmButton: true });
                }
            }
            if (d.csrf > 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'info',
                        title: 'Sesión caducada',
                        text: d.csrf + ' pago(s) pendiente(s). Recarga la página e intenta sincronizar.',
                        showConfirmButton: true });
                }
            }
        }
    });

    /* ── Indicador online / offline ───────────────────────────────── */
    function pwaSetBar(show) {
        var bar = document.getElementById('pwa-bar');
        if (!bar) return;
        bar.style.display = show ? 'flex' : 'none';
        if (show) pwaRefreshCount();
    }

    window.addEventListener('offline', function () { pwaSetBar(true); });
    window.addEventListener('online',  function () {
        pwaSyncNow();   /* Sincroniza al recuperar señal */
        /* Mantener barra visible hasta confirmar 0 pendientes */
        pwaRefreshCount();
    });

    if (!navigator.onLine) pwaSetBar(true);

    /* Verificar pendientes al cargar (por si quedaron de sesión anterior) */
    pwaRefreshCount();
})();

/* Consultar conteo de pagos en cola al SW */
function pwaRefreshCount() {
    var ctrl = navigator.serviceWorker && navigator.serviceWorker.controller;
    if (!ctrl) return;
    var mc = new MessageChannel();
    mc.port1.onmessage = function (e) {
        var n = e.data.n || 0;
        var el = document.getElementById('pwa-cnt');
        if (el) el.textContent = n;
        var bar = document.getElementById('pwa-bar');
        if (!bar) return;
        /* Mostrar barra si hay pendientes aunque haya señal */
        if (n > 0) bar.style.display = 'flex';
        else if (navigator.onLine) bar.style.display = 'none';
    };
    ctrl.postMessage({ type: 'GET_COUNT' }, [mc.port2]);
}

/* Incrementar contador visual sin esperar al SW */
function pwaAddCount(delta) {
    var el = document.getElementById('pwa-cnt');
    if (!el) return;
    el.textContent = Math.max(0, parseInt(el.textContent || '0') + delta);
    var bar = document.getElementById('pwa-bar');
    if (bar) bar.style.display = 'flex';
}

/* Disparar sincronización */
function pwaSyncNow() {
    var ctrl = navigator.serviceWorker && navigator.serviceWorker.controller;
    if (!ctrl) return;

    /* Background Sync API — Android Chrome */
    if ('SyncManager' in window) {
        navigator.serviceWorker.ready.then(function (reg) {
            reg.sync.register('sync-pagos').catch(function () {
                /* Fallback si Background Sync falla */
                pwaSyncManual(ctrl);
            });
        });
        return;
    }

    /* Fallback: mensaje directo al SW — iOS Safari y otros */
    pwaSyncManual(ctrl);
}

function pwaSyncManual(ctrl) {
    var mc = new MessageChannel();
    /* El SW responderá con SYNC_RESULT a todos los clientes */
    ctrl.postMessage({ type: 'SYNC_NOW' }, [mc.port2]);
}
</script>
