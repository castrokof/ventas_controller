{{-- resources/views/admin/v2/pago_card/index.blade.php --}}
@extends("theme.$theme.layout")

@section('titulo')
    Cobros / Ruta V2
@endsection

@section('styles')
<link href="{{ asset("assets/css/select2-bootstrap.min.css") }}" rel="stylesheet">
<link href="{{ asset("assets/css/select2.min.css") }}" rel="stylesheet">
@include('admin.v2._partials.mobile-styles')
<style>
/* ── Barra de fecha ────────────────────────────────────── */
.fecha-bar {
    background: linear-gradient(135deg,#6366f1 0%,#8b5cf6 100%);
    color:#fff; border-radius:10px; padding:10px 14px; margin-bottom:10px;
}
.fecha-bar .fecha-label { font-weight:700; font-size:1rem; }
.fecha-bar .fecha-sub   { font-size:.75rem; opacity:.85; }

/* ── Resumen del día ───────────────────────────────────── */
.resumen-bar {
    display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px;
}
.res-pill {
    border-radius:20px; padding:4px 12px; font-size:12px;
    font-weight:600; display:inline-flex; align-items:center; gap:4px;
}
.res-pill.pend { background:#fff3cd; color:#856404; border:1px solid #ffc107; }
.res-pill.atra { background:#f8d7da; color:#842029; border:1px solid #dc3545; }
.res-pill.pago { background:#d1e7dd; color:#0f5132; border:1px solid #198754; }
.res-pill.mont { background:#e2e3e5; color:#383d41; border:1px solid #adb5bd; font-weight:700; }

/* ── Filtros ───────────────────────────────────────────── */
.panel-filters { display:flex; gap:4px; flex-wrap:wrap; margin-bottom:8px; }
.filter-btn {
    font-size:11px; padding:3px 11px; border-radius:20px; cursor:pointer;
    border:1px solid #dee2e6; background:#fff; color:#6c757d;
    transition:all .12s; line-height:1.6;
}
.filter-btn.active             { color:#fff; border-color:transparent; }
.filter-btn.fb-all.active      { background:#6366f1; }
.filter-btn.fb-pend.active     { background:#fd7e14; }
.filter-btn.fb-atra.active     { background:#dc3545; }
.filter-btn.fb-pago.active     { background:#198754; }

/* ── Cuota card ────────────────────────────────────────── */
.cuota-card {
    border-radius:8px; border-left:4px solid #dee2e6;
    background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.08);
    padding:10px 12px; margin-bottom:8px;
    transition:box-shadow .12s;
}
.cuota-card:hover  { box-shadow:0 3px 8px rgba(0,0,0,.13); }
.cuota-card.ec-C   { border-left-color:#fd7e14; }
.cuota-card.ec-P   { border-left-color:#198754; opacity:.88; }
.cuota-card.ec-A   { border-left-color:#dc3545; }
.cuota-card.ec-T   { border-left-color:#0dcaf0; opacity:.82; }
.cc-name  { font-weight:700; font-size:13px; color:#212529; }
.cc-meta  { font-size:11px; color:#6c757d; line-height:1.5; }
.cc-value { font-size:15px; font-weight:700; color:#212529; }
.btn-xs   { font-size:11px; padding:3px 10px; border-radius:20px; }

/* ── Mini calendario (colapsable) ─────────────────────── */
.cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; }
.cal-hdr  {
    text-align:center; font-size:9px; font-weight:700;
    text-transform:uppercase; color:#6c757d; padding:3px 1px;
}
.cal-cell {
    position:relative; min-height:48px;
    border:1px solid #e9ecef; border-radius:5px;
    padding:4px 3px 2px; cursor:pointer; background:#fff;
    transition:background .12s, border-color .12s;
}
.cal-cell:hover        { background:#f8f9fa; border-color:#adb5bd; }
.cal-cell.today        { border-color:#6366f1; background:#ede9fe; }
.cal-cell.today .cal-dn{ color:#6366f1; font-weight:800; }
.cal-cell.selected     { border-color:#0d6efd; background:#cfe2ff;
                         box-shadow:0 2px 6px rgba(13,110,253,.25); }
.cal-cell.empty        { background:transparent; border-color:transparent; cursor:default; }
.cal-dn    { font-size:11px; font-weight:700; color:#495057; line-height:1; margin-bottom:2px; }
.cal-badges{ display:flex; flex-wrap:wrap; gap:1px; }
.cb-p { background:#198754; color:#fff; font-size:8px; padding:1px 3px; border-radius:20px; }
.cb-c { background:#fd7e14; color:#fff; font-size:8px; padding:1px 3px; border-radius:20px; }
.cb-a { background:#dc3545; color:#fff; font-size:8px; padding:1px 3px; border-radius:20px; }

/* ── Selección masiva ──────────────────────────────────── */
#btn-modo-masivo.activo {
    background:#ffc107!important; color:#212529!important;
    border-color:#ffc107!important;
}
.cuota-check {
    margin-right:8px; width:17px; height:17px;
    cursor:pointer; flex-shrink:0;
}
.cuota-card.seleccionada { outline:2px solid #ffc107; background:#fffde7; }
body.sel-masivo-on { padding-bottom:62px; }

/* ── Feedback inline ───────────────────────────────────── */
.field-feedback { font-size:11px; margin-top:2px; display:none; }
.field-feedback.show { display:block; }

/* ── Responsive ────────────────────────────────────────── */
@media (max-width:576px) {
    .fecha-bar .fecha-label { font-size:.9rem; }
    .barra-acciones .btn   { padding:4px 8px; font-size:12px; }
}

/* ── Modal cuotas del préstamo ─────────────────────── */
.cc-info-link { cursor:pointer; }
.cc-info-link:hover .cc-name { color:#6366f1; text-decoration:underline; }

.mcp-row {
    display:flex; align-items:center; gap:8px;
    padding:7px 10px; border-radius:7px; margin-bottom:5px;
    border-left:3px solid #dee2e6; background:#fff;
    box-shadow:0 1px 3px rgba(0,0,0,.06); font-size:12px;
}
.mcp-row.ec-C { border-left-color:#fd7e14; }
.mcp-row.ec-A { border-left-color:#dc3545; }
.mcp-row.ec-P { border-left-color:#198754; opacity:.85; }
.mcp-row.ec-T { border-left-color:#0dcaf0; opacity:.75; }
.mcp-row.mcp-sel { outline:2px solid #ffc107; background:#fffde7; }
.mcp-date  { min-width:58px; color:#6c757d; }
.mcp-num   { min-width:44px; color:#6c757d; }
.mcp-val   { font-weight:700; color:#212529; margin-left:auto; }
.mcp-check { width:16px; height:16px; cursor:pointer; flex-shrink:0; }
#mcp-progreso {
    background:#f8f9fa; border-radius:8px; padding:10px;
    text-align:center; font-size:13px; margin-bottom:6px; display:none;
}

/* ── Panel préstamos ──────────────────────────────── */
.prst-card {
    border-radius:8px; border-left:4px solid #dee2e6;
    background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.08);
    padding:10px 12px; margin-bottom:8px; cursor:pointer;
    transition:box-shadow .12s, background .1s;
}
.prst-card:hover       { box-shadow:0 3px 8px rgba(0,0,0,.15); background:#fafafa; }
.prst-card:active      { background:#f1f3f5; }
.prst-card.has-atraso  { border-left-color:#dc3545; }
.prst-card.no-atraso   { border-left-color:#fd7e14; }
.prst-name { font-weight:700; font-size:13px; color:#212529; }
.prst-meta { font-size:11px; color:#6c757d; line-height:1.5; }
.prst-val  { font-size:14px; font-weight:700; color:#212529; }
#btn-toggle-prestamos.activo {
    background:#6366f1!important; color:#fff!important;
    border-color:#6366f1!important;
}
.cal-cell-dim { opacity:.35; }
</style>
@endsection

@section("scriptsPlugins")
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/datatables-responsive/js/dataTables.responsive.min.js")}}"></script>
<script src="{{ asset('assets/js/jquery-select2/select2.min.js') }}"></script>
<script>
window.CAL_BASE = '{{ url("admin/v2/pago-card") }}';
</script>
<script src="{{ asset('assets/pages/scripts/admin/v2/pago_card/calendar.js') }}?v={{ filemtime(public_path('assets/pages/scripts/admin/v2/pago_card/calendar.js')) }}" type="text/javascript"></script>
<script>
/* Cálculo automático préstamo — inline para evitar caché */
$(function () {
    var BASE_PRESTAMO = (window.CAL_BASE || '/admin/v2/pago-card').replace(/\/pago-card$/, '/prestamo');

    function recalcularPrestamo() {
        var monto   = parseFloat($('#montop').val())   || 0;
        var cuotas  = parseInt($('#cuotas').val(), 10) || 0;
        var interes = parseFloat($('#interes').val())  || 0;
        var tipo    = $('#tipo_pagop').val();
        if (!monto || !cuotas) {
            $('#monto_totalp, #valor_cuotap, #monto_pendientep').val('');
            return;
        }
        var total = (tipo === 'Mensual')
            ? monto + (monto * (interes / 100) * cuotas)
            : monto + (monto * (interes / 100));
        total = Math.round(total);
        $('#monto_totalp').val(total);
        $('#valor_cuotap').val(Math.round(total / cuotas));
        $('#monto_pendientep').val(total);
    }

    /* Reemplazar handlers de calendar.js (si quedaron de caché vieja) con versión inline */
    $(document).off('input change', '#montop, #cuotas, #interes, #tipo_pagop');
    $(document).on('input change',  '#montop, #cuotas, #interes, #tipo_pagop', recalcularPrestamo);

    $('#modal-pc').off('show.bs.modal').on('show.bs.modal', function () {
        $('#form-prestamo')[0].reset();
        $('#monto_totalp, #valor_cuotap, #monto_pendientep').val('');
        $('#form-result-prestamo').html('');
        /* Resetear valores de select2 al abrir */
        $('#modal-pc .select2bs4').val(null).trigger('change');
    });

    /* Inicializar select2 cuando el modal ya es visible (necesario para dropdownParent) */
    $('#modal-pc').off('shown.bs.modal').on('shown.bs.modal', function () {
        /* .not('.select2-hidden-accessible') evita doble-init en aperturas sucesivas */
        $('#modal-pc .select2bs4').not('.select2-hidden-accessible').select2({
            theme:          'bootstrap4',
            dropdownParent: $('#modal-pc'),
        });
    });

    $('#form-prestamo').off('submit').on('submit', function (e) {
        e.preventDefault();
        var monto  = parseFloat($('#montop').val())   || 0;
        var cuotas = parseInt($('#cuotas').val(), 10) || 0;
        var interes= parseFloat($('#interes').val());
        var total  = parseFloat($('#monto_totalp').val()) || 0;
        var cuota  = parseFloat($('#valor_cuotap').val()) || 0;
        if (!monto || !cuotas || isNaN(interes) || !total || !cuota) {
            $('#form-result-prestamo').html(
                '<div class="alert alert-warning py-2"><i class="fas fa-exclamation-triangle mr-1"></i>'
                + 'Completa monto, tipo de pago, cuotas e interés para calcular los totales.</div>'
            );
            return;
        }
        var $btn = $(this).find('[type=submit]').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...');
        $('#form-result-prestamo').html('');
        $.ajax({
            url:      BASE_PRESTAMO,
            method:   'POST',
            dataType: 'json',
            data:     $(this).serialize(),
            success: function (data) {
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Guardar préstamo');
                if (data.errors) {
                    var h = '<div class="alert alert-danger py-2"><ul class="mb-0">';
                    $.each(data.errors, function (i, err) { h += '<li>' + err + '</li>'; });
                    $('#form-result-prestamo').html(h + '</ul></div>');
                    return;
                }
                $('#modal-pc').modal('hide');
                if (typeof cargarCuotasDia === 'function') cargarCuotasDia(selDate);
                if (typeof cargarListaPrestamos === 'function') cargarListaPrestamos();
                Swal.fire({ icon: 'success', title: 'Préstamo creado', showConfirmButton: false, timer: 1800 });
            },
            error: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Guardar préstamo');
                $('#form-result-prestamo').html(
                    '<div class="alert alert-danger py-2"><i class="fas fa-times-circle mr-1"></i>Error al guardar. Intenta de nuevo.</div>'
                );
            }
        });
    });
});
</script>
@endsection

@section('contenido')

{{-- ════════════════════════════════════════════════════════ --}}
{{-- BARRA DE FECHA + NAVEGACIÓN                             --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="fecha-bar d-flex align-items-center justify-content-between">
  <button id="btn-prev-dia" class="btn btn-sm btn-light" title="Día anterior">
    <i class="fas fa-chevron-left"></i>
  </button>
  <div class="text-center" style="flex:1">
    <div class="fecha-label" id="fecha-label">Hoy</div>
    <div class="fecha-sub"   id="fecha-sub"></div>
  </div>
  <button id="btn-next-dia" class="btn btn-sm btn-light" title="Día siguiente">
    <i class="fas fa-chevron-right"></i>
  </button>
</div>

{{-- ════════════════════════════════════════════════════════ --}}
{{-- BARRA DE ACCIONES RÁPIDAS                               --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="d-flex align-items-center justify-content-between flex-wrap mb-2 barra-acciones"
     style="gap:6px">
  <div class="d-flex" style="gap:5px">
    <button id="btn-hoy" class="btn btn-sm btn-outline-light"
            style="background:rgba(255,255,255,.15);border-color:rgba(255,255,255,.3);color:#fff;
                   background:#fff;color:#6366f1;border-color:#6366f1">
      <i class="fas fa-calendar-day mr-1"></i>Hoy
    </button>
    <button id="btn-toggle-cal" class="btn btn-sm btn-outline-secondary">
      <i class="fas fa-calendar-alt mr-1"></i><span id="lbl-toggle-cal">Ver calendario</span>
    </button>
    <button id="btn-toggle-prestamos" class="btn btn-sm btn-outline-secondary">
      <i class="fas fa-list-ul mr-1"></i>Préstamos
    </button>
  </div>
  <div class="d-flex" style="gap:5px">
    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal-u-cli"
            title="Crear cliente">
      <i class="fas fa-user-plus"></i>
    </button>
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-pc"
            title="Crear préstamo">
      <i class="fas fa-file-invoice-dollar"></i>
    </button>
    <button id="btn-modo-masivo" class="btn btn-sm btn-outline-warning"
            title="Cambio masivo de fecha">
      <i class="fas fa-calendar-check"></i>
    </button>
  </div>
</div>

{{-- ════════════════════════════════════════════════════════ --}}
{{-- PANEL PRÉSTAMOS (colapsable)                            --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div id="prestamos-container" style="display:none" class="mb-2">
  <div class="card shadow-sm mb-0">
    <div class="card-body p-2">

      {{-- Cabecera del panel --}}
      <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="font-weight-bold" style="font-size:.85rem">
          <i class="fas fa-list-ul mr-1 text-muted"></i>Préstamos activos
        </span>
        <div class="d-flex align-items-center" style="gap:4px">
          <button class="filter-btn fb-all active" data-prst-filter="all">Todos</button>
          <button class="filter-btn fb-atra"       data-prst-filter="atraso">
            <i class="fas fa-exclamation-triangle mr-1"></i>Atrasos
          </button>
          <button class="filter-btn"              data-prst-filter="hoy"
                  style="border-color:#6366f1;color:#6366f1">
            <i class="fas fa-calendar-day mr-1"></i>Hoy
          </button>
          <button id="btn-prst-refresh" class="btn btn-xs btn-outline-secondary ml-1"
                  title="Recargar" style="padding:2px 7px;font-size:11px">
            <i class="fas fa-sync-alt"></i>
          </button>
        </div>
      </div>

      {{-- Spinner --}}
      <div id="prst-loading" class="text-center py-3" style="display:none">
        <i class="fas fa-spinner fa-spin text-primary"></i>
      </div>

      {{-- Vacío --}}
      <p id="prst-empty" class="text-center text-muted py-2 mb-0"
         style="font-size:12px;display:none">
        <i class="fas fa-check-circle text-success mr-1"></i>Sin préstamos en esta categoría.
      </p>

      {{-- Lista de préstamos --}}
      <div id="prst-list"></div>

    </div>
  </div>
</div>

{{-- ════════════════════════════════════════════════════════ --}}
{{-- MINI CALENDARIO (colapsable)                            --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div id="cal-container" style="display:none" class="mb-2">
  <div class="card shadow-sm mb-0">
    <div class="card-body p-2">
      <div class="d-flex align-items-center justify-content-between mb-1">
        <button id="btn-prev-mes" class="btn btn-sm btn-outline-secondary">
          <i class="fas fa-chevron-left"></i>
        </button>
        <span id="cal-titulo" class="font-weight-bold" style="font-size:.9rem">Cargando...</span>
        <button id="btn-next-mes" class="btn btn-sm btn-outline-secondary">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      <div class="cal-grid mb-1">
        @foreach(['L','M','X','J','V','S','D'] as $d)
        <div class="cal-hdr">{{ $d }}</div>
        @endforeach
      </div>
      <div id="cal-loading" class="text-center py-3">
        <i class="fas fa-spinner fa-spin text-primary"></i>
      </div>
      <div class="cal-grid" id="cal-grid" style="display:none"></div>
    </div>
  </div>
</div>

{{-- ════════════════════════════════════════════════════════ --}}
{{-- RESUMEN DEL DÍA                                         --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="resumen-bar" id="resumen-bar" style="display:none">
  <span class="res-pill pend"><i class="fas fa-clock"></i> <span id="res-pend">0</span> pendientes</span>
  <span class="res-pill atra"><i class="fas fa-exclamation-triangle"></i> <span id="res-atra">0</span> atrasadas</span>
  <span class="res-pill pago"><i class="fas fa-check-circle"></i> <span id="res-pago">0</span> pagadas</span>
  <span class="res-pill mont"><i class="fas fa-dollar-sign"></i> <span id="res-monto">0</span></span>
</div>

{{-- ════════════════════════════════════════════════════════ --}}
{{-- BÚSQUEDA + FILTROS                                      --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="input-group input-group-sm mb-1">
  <div class="input-group-prepend">
    <span class="input-group-text" style="background:#f8f9fa">
      <i class="fas fa-search text-muted"></i>
    </span>
  </div>
  <input type="text" id="panel-search" class="form-control"
         placeholder="Buscar por cliente, crédito #...">
  <div class="input-group-append">
    <button class="btn btn-outline-secondary" id="btn-clear-search"
            type="button" style="display:none" title="Limpiar">
      <i class="fas fa-times"></i>
    </button>
  </div>
</div>

<div class="panel-filters">
  <button class="filter-btn fb-all active" data-filter="all">Todos</button>
  <button class="filter-btn fb-pend"        data-filter="C">Pendiente</button>
  <button class="filter-btn fb-atra"        data-filter="A">Atrasada</button>
  <button class="filter-btn fb-pago"        data-filter="P">Pagada</button>
</div>

{{-- ════════════════════════════════════════════════════════ --}}
{{-- LISTA DE CUOTAS                                         --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div id="cuotas-loading" class="text-center py-4">
  <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
  <p class="text-muted mt-2 mb-0" style="font-size:12px">Cargando cobros del día...</p>
</div>

<div id="panel-list"></div>

<p id="panel-no-results" class="text-center text-muted py-3"
   style="font-size:13px;display:none">
  <i class="fas fa-search fa-lg d-block mb-1 text-muted"></i>
  Sin resultados para ese filtro.
</p>

<p id="panel-empty" class="text-center text-muted py-4"
   style="font-size:13px;display:none">
  <i class="fas fa-check-circle fa-2x d-block mb-2 text-success"></i>
  No hay cuotas para este día.
</p>


{{-- ════════════════════════════════════════════════════════ --}}
{{-- MODAL: Registrar / Editar pago                          --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-pd" tabindex="-1"
     role="dialog" aria-labelledby="modal-pd-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card card-danger mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="modal-title-pd mb-0 flex-grow-1" id="modal-pd-titulo"></h6>
          <button type="button" class="btn btn-sm btn-secondary ml-2"
                  data-dismiss="modal" aria-label="Cerrar">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
        <span id="form_result" role="alert" aria-live="polite"></span>
      </div>
      <form id="form-general" name="form-general"
            class="form-horizontal" method="post" novalidate>
        @csrf
        <div class="card-body">
          @include('admin.v2.pago_card.form-pago')
        </div>
        <div class="card-footer">
          <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
              @include('includes.boton-form-registrar-pago')
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{-- MODAL: Detalle cuotas del préstamo                      --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-d" tabindex="-1"
     role="dialog" aria-labelledby="modal-d-titulo" aria-modal="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="modal-title-d mb-0 flex-grow-1" id="modal-d-titulo"></h6>
          <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
        <div class="card-body table-responsive p-2">
          <table id="detalleCuota"
                 class="table table-sm table-striped table-bordered text-nowrap"
                 style="width:100%">
            <thead class="thead-light">
              <tr>
                <th># Cuota</th><th>Valor</th><th>Fecha</th><th>Pagado</th><th>Estado</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{-- MODAL: Historial de pagos del crédito                   --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-dp" tabindex="-1"
     role="dialog" aria-labelledby="modal-dp-titulo" aria-modal="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="modal-title-dp mb-0 flex-grow-1" id="modal-dp-titulo"></h6>
          <div class="btn-group ml-2" id="btnar" role="group"></div>
          <button type="button" class="btn btn-sm btn-secondary ml-2" data-dismiss="modal" aria-label="Cerrar">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
        <div class="card-body" id="detalles"></div>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{-- MODAL: Adelanto de cuotas                               --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-acuotas" tabindex="-1"
     role="dialog" aria-labelledby="modal-acuotas-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="modal-title-acuotas mb-0 flex-grow-1" id="modal-acuotas-titulo"></h6>
          <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
        <div class="card-body table-responsive p-2">
          <table id="pagoa" class="table table-hover table-sm responsive" style="width:100%">
            <thead class="thead-light">
              <tr>
                <th>Acciones</th><th># Cuota</th><th>Fecha</th>
                <th>Estado</th><th>Nombres</th><th>Apellidos</th><th>Id Crédito</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{-- MODAL: Cuotas atrasadas                                 --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-atrasosp" tabindex="-1"
     role="dialog" aria-labelledby="modal-atrasosp-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="modal-title-atrasosp mb-0 flex-grow-1" id="modal-atrasosp-titulo"></h6>
          <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
        <div class="card-body table-responsive p-2">
          <table id="atrasosp" class="table table-hover table-sm responsive" style="width:100%">
            <thead class="thead-light">
              <tr>
                <th>Acciones</th><th># Cuota</th><th>Fecha</th>
                <th>Estado</th><th>Nombres</th><th>Apellidos</th><th>Id Crédito</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear cliente                                    --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-u-cli" tabindex="-1"
     role="dialog" aria-labelledby="modal-cli-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card card-info mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="mb-0 flex-grow-1" id="modal-cli-titulo">
            <i class="fas fa-user-plus"></i> Crear Cliente
          </h6>
          <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
        <form id="form-cli" class="form-horizontal" method="post" novalidate>
          @csrf
          <div class="card-body">
            @include('admin.v2.pago_card.form-cli')
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-info">
              <i class="fas fa-save"></i> Guardar cliente
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear préstamo                                   --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-pc" tabindex="-1"
     role="dialog" aria-labelledby="modal-pc-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card card-success mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="mb-0 flex-grow-1" id="modal-pc-titulo">
            <i class="fas fa-file-invoice-dollar"></i> Crear Préstamo
          </h6>
          <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
        <form id="form-prestamo" class="form-horizontal" method="post" novalidate>
          @csrf
          <div class="card-body">
            @include('admin.v2.pago_card.form-prestamo')
          </div>
          <div class="card-footer">
            <div id="form-result-prestamo" class="mb-2"></div>
            <div class="text-right">
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-1"></i>Guardar préstamo
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{-- MODAL: Cambio masivo de fecha de cuota                  --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-cambiar-fecha" tabindex="-1"
     role="dialog" aria-labelledby="modal-cf-titulo" aria-modal="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#ffc107">
        <h6 class="modal-title font-weight-bold" id="modal-cf-titulo">
          <i class="fas fa-calendar-alt mr-1"></i> Cambiar fecha de cuotas
        </h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="mb-2" style="font-size:13px">
          Se cambiarán <span id="cf-count" class="font-weight-bold"></span> cuota(s).
        </p>
        <label class="mb-1" style="font-size:13px;font-weight:600">Nueva fecha de cobro</label>
        <input type="date" id="cf-nueva-fecha" class="form-control form-control-sm">
        <div id="cf-feedback" class="text-danger mt-1" style="font-size:12px;display:none"></div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" id="btn-cf-confirmar" class="btn btn-sm btn-warning font-weight-bold">
          <i class="fas fa-check mr-1"></i>Aplicar
        </button>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{-- MODAL: Calendario de cuotas del préstamo (adelantos)    --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-cuotas-prestamo" tabindex="-1"
     role="dialog" aria-labelledby="mcp-titulo-id" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      {{-- Header --}}
      <div class="modal-header py-2"
           style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff">
        <div>
          <h6 class="mb-0 font-weight-bold" id="mcp-titulo-id">
            <span id="mcp-titulo">Cuotas del crédito</span>
          </h6>
          <small id="mcp-subtitulo" style="opacity:.85;font-size:11px"></small>
        </div>
        <button type="button" class="btn btn-sm btn-light ml-2" data-dismiss="modal"
                style="font-size:11px;padding:3px 10px">
          <i class="fas fa-times mr-1"></i>Cerrar
        </button>
      </div>

      {{-- Body --}}
      <div class="modal-body p-2">

        {{-- Cargando --}}
        <div id="mcp-loading" class="text-center py-4">
          <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
        </div>

        {{-- Contenido (oculto hasta cargar) --}}
        <div id="mcp-content" style="display:none">

          {{-- Mini calendario del préstamo --}}
          <div class="card shadow-sm mb-2">
            <div class="card-body p-2">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <button id="mcp-prev-mes" class="btn btn-sm btn-outline-secondary">
                  <i class="fas fa-chevron-left"></i>
                </button>
                <span id="mcp-cal-titulo" class="font-weight-bold" style="font-size:.85rem"></span>
                <button id="mcp-next-mes" class="btn btn-sm btn-outline-secondary">
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
              <div class="cal-grid mb-1">
                @foreach(['L','M','X','J','V','S','D'] as $d)
                <div class="cal-hdr">{{ $d }}</div>
                @endforeach
              </div>
              <div class="cal-grid" id="mcp-cal-grid"></div>
            </div>
          </div>

          {{-- Filtros --}}
          <div class="panel-filters mb-1">
            <button class="filter-btn fb-all active" data-mcp-filter="all">Todas</button>
            <button class="filter-btn fb-pend" data-mcp-filter="C">Pendiente</button>
            <button class="filter-btn fb-atra" data-mcp-filter="A">Atrasada</button>
            <button class="filter-btn fb-pago" data-mcp-filter="P">Pagada</button>
          </div>
          <p id="mcp-dia-lbl" class="text-muted mb-1" style="font-size:11px;display:none">
            <i class="fas fa-filter mr-1"></i><span></span>
            <a href="#" id="mcp-limpiar-dia" class="ml-1">Quitar filtro</a>
          </p>

          {{-- Progreso pago --}}
          <div id="mcp-progreso"></div>

          {{-- Lista de cuotas --}}
          <div id="mcp-lista"></div>

        </div>
      </div>

      {{-- Footer --}}
      <div class="modal-footer py-2 justify-content-between" id="mcp-footer" style="display:none">
        <span id="mcp-sel-info" class="text-muted" style="font-size:12px">
          Selecciona cuotas futuras para pagar
        </span>
        <button id="btn-mcp-pagar" class="btn btn-sm btn-success font-weight-bold" disabled>
          <i class="fas fa-money-bill-wave mr-1"></i>Pagar seleccionadas
        </button>
      </div>

    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════════ --}}
{{-- Barra selección masiva (fija abajo)                     --}}
{{-- ════════════════════════════════════════════════════════ --}}
<div id="sel-bar" style="
    display:none; position:fixed; bottom:0; left:0; right:0; z-index:1040;
    background:#343a40; color:#fff; padding:10px 16px;
    box-shadow:0 -3px 12px rgba(0,0,0,.25);
    align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px">
  <div style="font-size:13px">
    <i class="fas fa-check-square mr-1" style="color:#ffc107"></i>
    <span id="sel-count">0</span> cuota(s) seleccionada(s)
  </div>
  <div class="d-flex" style="gap:8px">
    <button id="btn-sel-limpiar" class="btn btn-sm btn-outline-light">
      <i class="fas fa-times mr-1"></i>Limpiar
    </button>
    <button id="btn-sel-cambiar" class="btn btn-sm btn-warning font-weight-bold"
            style="color:#212529">
      <i class="fas fa-calendar-alt mr-1"></i>Cambiar fecha
    </button>
  </div>
</div>

@endsection
