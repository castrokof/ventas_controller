{{--
    resources/views/admin/v2/_partials/mobile-styles.blade.php
    CSS compartido para todas las vistas V2 — optimizado para móvil.
    Incluir dentro de @section('styles') de cada index.
--}}
<style>
/* ════════════════════════════════════════════════════════
   MOBILE-FIRST — V2 shared styles
   ════════════════════════════════════════════════════════ */

/* ── Prevenir zoom en iOS al enfocar inputs ───────────── */
input, select, textarea, .select2-selection__rendered {
    font-size: 16px !important;
}

/* ── Touch targets mínimos ────────────────────────────── */
.btn           { min-height: 44px; }
.btn-sm        { min-height: 36px; }
.form-control,
.custom-select { min-height: 44px; }

/* ── Modal pantalla completa en móvil ─────────────────── */
@media (max-width: 767.98px) {
    .modal-dialog {
        margin: 0 !important;
        width:  100vw    !important;
        max-width: 100vw !important;
    }
    .modal-content {
        border-radius: 0;
        min-height: 100dvh;
        display: flex;
        flex-direction: column;
    }
    .modal-header { border-radius: 0; }
    .modal-body {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 1rem;
    }
    .modal-footer {
        position: sticky;
        bottom: 0;
        z-index: 10;
        background: #fff;
        border-top: 1px solid #dee2e6;
        box-shadow: 0 -3px 12px rgba(0,0,0,.12);
        padding: .75rem 1rem;
    }
    .modal-footer .btn { flex: 1; min-height: 48px; font-size: 15px; }
    /* Ocultar scroll X de cards en modal */
    .card { overflow: hidden; }
}

/* ── FAB (Floating Action Button) ────────────────────── */
.v2-fab {
    position: fixed;
    bottom: 1.5rem;
    right:  1.5rem;
    z-index: 1040;
    width: 58px; height: 58px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 6px 22px rgba(0,0,0,.35);
    transition: transform .2s, box-shadow .2s;
    -webkit-tap-highlight-color: transparent;
    cursor: pointer;
}
.v2-fab:hover,
.v2-fab:focus  { transform: scale(1.1); box-shadow: 0 10px 30px rgba(0,0,0,.4); outline: none; }
.v2-fab:active { transform: scale(.95); }

/* ── Cards mobile (lista de registros) ───────────────── */
.v2-mobile-list { padding-bottom: 5rem; /* espacio para el FAB */ }

.v2-mcard {
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,.1);
    margin-bottom: .75rem;
    overflow: hidden;
    background: #fff;
    border: none;
}
.v2-mcard-header {
    padding: .65rem 1rem;
    font-weight: 700;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.v2-mcard-body {
    padding: .6rem 1rem;
    font-size: 13px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .3rem .75rem;
}
.v2-mcard-body .v2-lbl {
    font-size: 10px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em; color: #888;
    margin-bottom: 1px;
}
.v2-mcard-body .v2-val { color: #333; }
.v2-mcard-footer {
    background: #f8f9fa;
    padding: .5rem .75rem;
    display: flex; gap: .5rem;
}
.v2-mcard-footer .btn {
    flex: 1; font-size: 12px; min-height: 40px; border-radius: 8px;
}

/* ── Spinner/skeleton ─────────────────────────────────── */
.skeleton-cell {
    display: inline-block; height: 13px; border-radius: 3px;
    background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

.v2-loader {
    display: none; position: absolute; inset: 0;
    background: rgba(255,255,255,.85); z-index: 10;
    place-items: center;
}
.v2-loader.active { display: grid; }

/* ── Botones acción en tabla ──────────────────────────── */
.btn-v2-action {
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50px; padding: 5px 12px; font-size: 11px;
    transition: transform .15s, box-shadow .15s;
    box-shadow: 0 3px 8px rgba(0,0,0,.2);
    min-height: 34px;
}
.btn-v2-action:hover  { transform: translateY(-1px); box-shadow: 0 5px 12px rgba(0,0,0,.25); }
.btn-v2-action:active { transform: translateY(0); }

/* ── DataTable oculta en móvil, cards se muestran ────── */
@media (max-width: 767.98px) {
    .v2-dt-wrapper      { display: none !important; }
    .v2-mobile-list     { display: block !important; }
    /* Cabecera del card simplificada */
    .card-tools .btn    { min-height: 36px; }
}
@media (min-width: 768px) {
    .v2-mobile-list     { display: none !important; }
    .v2-dt-wrapper      { display: block !important; }
    /* FAB no tan necesario en desktop pero mantenemos */
    .v2-fab             { bottom: 2rem; right: 2rem; }
}

/* ── Tablas compactas ─────────────────────────────────── */
#tabla-empleados th, #tabla-empleados td,
#tabla-clientes  th, #tabla-clientes  td,
#tabla-gastos    th, #tabla-gastos    td,
#tabla-prestamos th, #tabla-prestamos td {
    white-space: nowrap; font-size: 12px;
}
</style>
