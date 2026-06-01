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
/* ── Layout principal ─────────────────────────────────────── */
.v2-cal-wrap { display: flex; gap: 16px; align-items: flex-start; }
.v2-cal-main  { flex: 1; min-width: 0; }
.v2-cal-panel { width: 340px; flex-shrink: 0; position: sticky; top: 70px; }

/* ── Grid del calendario ──────────────────────────────────── */
.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; }
.cal-hdr {
    text-align: center; font-size: 10px; font-weight: 700;
    text-transform: uppercase; color: #6c757d;
    padding: 4px 2px; letter-spacing: .5px;
}
.cal-cell {
    position: relative; min-height: 64px;
    border: 1px solid #e9ecef; border-radius: 6px;
    padding: 5px 4px 3px; cursor: pointer; background: #fff;
    transition: background .12s, box-shadow .12s, border-color .12s;
    user-select: none;
}
.cal-cell:hover           { background: #f8f9fa; border-color: #adb5bd; }
.cal-cell.today           { border-color: #0d6efd; background: #e7f0ff; }
.cal-cell.today .cal-dn   { color: #0d6efd; font-weight: 800; }
.cal-cell.selected        { border-color: #0d6efd; background: #cfe2ff; box-shadow: 0 2px 8px rgba(13,110,253,.25); }
.cal-cell.empty           { background: transparent; border-color: transparent; cursor: default; min-height: 64px; }
.cal-dn  { font-size: 12px; font-weight: 700; color: #495057; line-height: 1; margin-bottom: 3px; }
.cal-badges { display: flex; flex-wrap: wrap; gap: 2px; }
.cb-p { background: #198754; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 20px; white-space: nowrap; }
.cb-c { background: #fd7e14; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 20px; white-space: nowrap; }
.cb-a { background: #dc3545; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 20px; white-space: nowrap; }

/* ── Panel lateral ────────────────────────────────────────── */
.panel-drag {
    display: none; text-align: center;
    padding: 8px 0 2px;
    background: linear-gradient(135deg,#6366f1 0%,#8b5cf6 100%);
    border-radius: 20px 20px 0 0; cursor: grab;
}
.panel-drag-notch {
    width: 36px; height: 4px;
    background: rgba(255,255,255,.45); border-radius: 2px; margin: 0 auto;
}
.panel-hdr {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff; padding: 10px 14px;
}
.panel-hdr .ph-title { font-weight: 700; font-size: 14px; }
.panel-hdr .ph-sub   { font-size: 11px; opacity: .85; margin-top: 2px; }
.panel-body { max-height: calc(100vh - 280px); overflow-y: auto; padding: 0 8px 8px; }

/* ── Buscador ─────────────────────────────────────────────── */
#panel-search-wrap { padding: 6px 8px 0; }
#panel-search-wrap .input-group-text { border-right: 0; background: #f8f9fa; }
#panel-search-wrap .form-control     { border-left: 0; font-size: 12px; }

/* ── Filtros de estado ────────────────────────────────────── */
.panel-filters { display: flex; gap: 4px; padding: 6px 8px 2px; flex-wrap: wrap; }
.filter-btn {
    font-size: 10px; padding: 2px 9px; border-radius: 20px; cursor: pointer;
    border: 1px solid #dee2e6; background: #fff; color: #6c757d;
    transition: all .12s; line-height: 1.6;
}
.filter-btn.active             { color: #fff; border-color: transparent; }
.filter-btn.fb-all.active      { background: #6366f1; }
.filter-btn.fb-pend.active     { background: #fd7e14; }
.filter-btn.fb-atra.active     { background: #dc3545; }
.filter-btn.fb-pago.active     { background: #198754; }

/* ── Cuota card ───────────────────────────────────────────── */
.cuota-card {
    border-radius: 8px; border-left: 4px solid #dee2e6;
    background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.08);
    padding: 10px 12px; margin-bottom: 8px; margin-top: 6px;
    transition: box-shadow .12s;
}
.cuota-card:hover { box-shadow: 0 3px 8px rgba(0,0,0,.12); }
.cuota-card.ec-C  { border-left-color: #fd7e14; }
.cuota-card.ec-P  { border-left-color: #198754; opacity: .88; }
.cuota-card.ec-A  { border-left-color: #dc3545; }
.cuota-card.ec-T  { border-left-color: #0dcaf0; opacity: .82; }
.cc-name  { font-weight: 700; font-size: 13px; color: #212529; }
.cc-meta  { font-size: 11px; color: #6c757d; line-height: 1.5; }
.cc-value { font-size: 15px; font-weight: 700; color: #212529; }

/* ── Botones cuota ────────────────────────────────────────── */
.btn-xs { font-size: 11px; padding: 3px 10px; border-radius: 20px; }

/* ── Leyenda ──────────────────────────────────────────────── */
.cal-legend { display: flex; align-items: center; gap: 8px; }
.cal-legend span { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; }

/* ── Feedback inline ──────────────────────────────────────── */
.field-feedback { font-size: 11px; margin-top: 2px; display: none; }
.field-feedback.show { display: block; }

/* ── Selección masiva ─────────────────────────────────────── */
#btn-modo-masivo.activo { background:#ffc107!important; color:#212529!important; border-color:#ffc107!important; }
.cuota-check { margin-right: 8px; width: 17px; height: 17px; cursor: pointer; flex-shrink: 0; }
.cuota-card.seleccionada { outline: 2px solid #ffc107; background: #fffde7; }
body.sel-masivo-on { padding-bottom: 62px; }

/* ── Overlay móvil ────────────────────────────────────────── */
.v2-panel-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.45); z-index: 1049;
}
.v2-panel-overlay.active { display: block; }

/* ── Responsive (móvil) ───────────────────────────────────── */
@media (max-width: 768px) {
    /* Panel pasa a bottom-sheet fijo */
    .v2-cal-wrap  { display: block; }
    .v2-cal-panel {
        position: fixed;
        bottom: 0; left: 0; right: 0; width: 100%;
        z-index: 1050;
        transform: translateY(105%);
        transition: transform .32s cubic-bezier(.32,.72,0,1);
        border-radius: 20px 20px 0 0;
        overflow: hidden;
        max-height: 80vh;
        display: flex; flex-direction: column;
    }
    .v2-cal-panel .card {
        border-radius: 20px 20px 0 0;
        flex: 1; display: flex; flex-direction: column; overflow: hidden;
    }
    .v2-cal-panel.panel-open { transform: translateY(0); }
    .panel-drag   { display: block; }
    .panel-body   { max-height: calc(80vh - 110px); }
    /* Calendario más compacto */
    .cal-cell { min-height: 48px; padding: 4px 2px 2px; }
    .cal-dn   { font-size: 11px; }
    .cal-hdr  { font-size: 9px; padding: 3px 1px; }
    .cb-p, .cb-c, .cb-a { font-size: 8px; padding: 1px 3px; }
    /* Barra superior */
    .cal-legend { display: none; }
    .btn-top-text { display: none; }
}
</style>
@endsection

@section("scriptsPlugins")
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/datatables-responsive/js/dataTables.responsive.min.js")}}"></script>
<script>
window.CAL_BASE = '{{ url("admin/v2/pago-card") }}';
</script>
<script src="{{ asset('assets/pages/scripts/admin/v2/pago_card/calendar.js') }}" type="text/javascript"></script>
@endsection

@section('contenido')

{{-- ── Barra superior ─────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between flex-wrap mb-2" style="gap:6px">

  {{-- Navegación de mes --}}
  <div class="d-flex align-items-center">
    <button id="btn-prev-mes" class="btn btn-sm btn-outline-secondary" title="Mes anterior">
      <i class="fas fa-chevron-left"></i>
    </button>
    <h5 id="cal-titulo" class="mb-0 mx-2 font-weight-bold"
        style="min-width:150px;text-align:center;font-size:1rem">Cargando...</h5>
    <button id="btn-next-mes" class="btn btn-sm btn-outline-secondary" title="Mes siguiente">
      <i class="fas fa-chevron-right"></i>
    </button>
    <button id="btn-hoy" class="btn btn-sm btn-outline-primary ml-2">Hoy</button>
  </div>

  {{-- Leyenda + acciones rápidas --}}
  <div class="d-flex align-items-center flex-wrap" style="gap:5px">
    <div class="cal-legend mr-1">
      <span><span class="cb-p">P</span> Pagadas</span>
      <span><span class="cb-c">C</span> Pendientes</span>
      <span><span class="cb-a">A</span> Atrasadas</span>
    </div>
    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal-u-cli">
      <i class="fas fa-user-plus"></i>
      <span class="btn-top-text"> Cliente</span>
    </button>
    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-pc">
      <i class="fas fa-file-invoice-dollar"></i>
      <span class="btn-top-text"> Préstamo</span>
    </button>
    <button id="btn-modo-masivo" class="btn btn-sm btn-outline-warning"
            title="Activar selección masiva para cambio de fecha">
      <i class="fas fa-calendar-check mr-1"></i>
      <span class="btn-top-text">Cambio masivo</span>
      <span class="d-none d-inline btn-top-text-short" style="display:none!important">Masivo</span>
    </button>
  </div>
</div>

{{-- Overlay para bottom-sheet móvil --}}
<div class="v2-panel-overlay" id="panel-overlay"></div>

{{-- ── Calendario + panel lateral ─────────────────────────────── --}}
<div class="v2-cal-wrap">

  {{-- Calendario --}}
  <div class="v2-cal-main">
    <div class="card shadow-sm mb-0">
      <div class="card-body p-2">

        {{-- Cabecera días de semana --}}
        <div class="cal-grid mb-1">
          @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d)
          <div class="cal-hdr">{{ $d }}</div>
          @endforeach
        </div>

        {{-- Spinner mientras carga --}}
        <div id="cal-loading" class="text-center py-5">
          <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
          <p class="text-muted mt-2 mb-0" style="font-size:12px">Cargando calendario...</p>
        </div>

        {{-- Grid generado por JS --}}
        <div class="cal-grid" id="cal-grid" style="display:none"></div>

      </div>
    </div>
  </div>

  {{-- Panel lateral de cuotas --}}
  <div class="v2-cal-panel">
    <div class="card shadow-sm mb-0">

      {{-- Drag handle (solo móvil) --}}
      <div class="panel-drag">
        <div class="panel-drag-notch"></div>
      </div>

      {{-- Header --}}
      <div class="panel-hdr">
        <div class="d-flex align-items-start justify-content-between">
          <div style="flex:1;min-width:0">
            <div class="ph-title" id="panel-title">Cobros del día</div>
            <div class="ph-sub"   id="panel-subtitle">Selecciona un día del calendario</div>
          </div>
          <button id="btn-panel-close" class="btn btn-sm btn-light ml-2" style="display:none">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      {{-- Buscador (oculto hasta que carguen cuotas) --}}
      <div id="panel-search-wrap" style="display:none">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
          </div>
          <input type="text" id="panel-search" class="form-control"
                 placeholder="Buscar cliente, crédito...">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" id="btn-clear-search"
                    type="button" style="display:none" title="Limpiar">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </div>

      {{-- Filtros de estado (ocultos hasta que carguen cuotas) --}}
      <div id="panel-filters-wrap" style="display:none" class="panel-filters">
        <button class="filter-btn fb-all active"  data-filter="all">Todos</button>
        <button class="filter-btn fb-pend"         data-filter="C">Pendiente</button>
        <button class="filter-btn fb-atra"         data-filter="A">Atrasada</button>
        <button class="filter-btn fb-pago"         data-filter="P">Pagada</button>
      </div>

      {{-- Cuerpo --}}
      <div class="panel-body" id="panel-body">
        <div id="panel-placeholder" class="text-center py-4 text-muted">
          <i class="fas fa-calendar-day fa-2x mb-2 d-block" style="color:#8b5cf6"></i>
          <span style="font-size:13px">Toca un día del calendario<br>para ver las cuotas.</span>
        </div>
        <div id="panel-list" style="display:none"></div>
        <p id="panel-no-results" class="text-center text-muted py-2"
           style="font-size:12px;display:none">Sin resultados.</p>
      </div>

    </div>
  </div>

</div>{{-- /v2-cal-wrap --}}


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
          <div class="btn-group ml-auto" id="btnar" role="group"></div>
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
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save"></i> Guardar préstamo
            </button>
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
          Se cambiarán <span id="cf-count" class="font-weight-bold text-warning"></span> cuota(s).
        </p>
        <label class="mb-1" style="font-size:13px;font-weight:600">Nueva fecha de cobro</label>
        <input type="date" id="cf-nueva-fecha" class="form-control form-control-sm">
        <div id="cf-feedback" class="text-danger mt-1" style="font-size:12px;display:none"></div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" id="btn-cf-confirmar" class="btn btn-sm btn-warning font-weight-bold">
          <i class="fas fa-check mr-1"></i>Aplicar cambio
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ════════════════════════════════════════════════════════ --}}
{{-- Barra de selección masiva (fija en la parte inferior)   --}}
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
