{{-- resources/views/admin/v2/pago_card/index.blade.php --}}
@extends("theme.$theme.layout")

@section('titulo')
    Pagos v2
@endsection

@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css")}}" rel="stylesheet">
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet">
<link href="{{asset("assets/css/select2-bootstrap.min.css")}}" rel="stylesheet">
<link href="{{asset("assets/css/select2.min.css")}}" rel="stylesheet">
<style>
/* ── V2 Layout ─────────────────────────────────────── */
.v2-tab-nav .nav-link          { font-size: 12px; font-weight: 700; color: #495057; text-transform: uppercase; letter-spacing: .5px; }
.v2-tab-nav .nav-link.active   { color: #007bff; border-bottom: 2px solid #007bff; }
.v2-tab-nav .nav-link .badge   { font-size: 10px; vertical-align: middle; }

/* ── Skeleton loader ───────────────────────────────── */
.skeleton-row td { padding: 8px 12px; }
.skeleton-cell   { height: 14px; border-radius: 4px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.skeleton-cell.w-80 { width: 80%; }
.skeleton-cell.w-60 { width: 60%; }
.skeleton-cell.w-40 { width: 40%; }

/* ── Botones acción ────────────────────────────────── */
.btn-card-action {
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50px; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    padding: 6px 14px; border: none; outline: none;
    transition: transform .15s, box-shadow .15s;
    box-shadow: 0 4px 10px rgba(0,0,0,.2);
}
.btn-card-action:hover  { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(0,0,0,.25); }
.btn-card-action:active { transform: translateY(0); }
.btn-card-action i      { margin-right: 4px; }

/* ── Small box cliente ─────────────────────────────── */
.client-card {
    border-radius: 8px; overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
}
.client-card .client-info    { padding: 10px 14px; font-size: 12px; line-height: 1.6; }
.client-card .client-info h5 { margin: 0 0 4px; font-size: 14px; font-weight: 700; }
.client-card .client-info p  { margin: 0; }
.client-card .client-footer  { width: 100%; border-radius: 0; font-size: 11px; font-weight: 700; text-transform: uppercase; }

/* ── Estado badges ─────────────────────────────────── */
.badge-estado-C { background-color: #ffc107; color: #212529; }
.badge-estado-P { background-color: #28a745; color: #fff; }
.badge-estado-A { background-color: #dc3545; color: #fff; }
.badge-estado-T { background-color: #17a2b8; color: #fff; }

/* ── Feedback inline ───────────────────────────────── */
.field-feedback { font-size: 11px; margin-top: 2px; display: none; }
.field-feedback.show { display: block; }
.is-valid   ~ .field-feedback { color: #28a745; }
.is-invalid ~ .field-feedback { color: #dc3545; }

/* ── Spinner de carga ──────────────────────────────── */
.v2-spinner {
    display: none; position: absolute; top: 50%; left: 50%;
    transform: translate(-50%,-50%); z-index: 999;
}
.v2-spinner.active { display: block; }
</style>
@endsection

@section('scripts')
<script src="{{asset("assets/pages/scripts/admin/pagocalender/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
  <div class="col-12">

    {{-- ── Card principal con tabs ─────────────────────────── --}}
    <div class="card card-primary card-tabs shadow-sm">

      {{-- Header: botón menú + tabs --}}
      <div class="card-header p-0 pt-1 d-flex align-items-center">

        {{-- Toggle sidebar --}}
        <button class="btn btn-sm btn-link px-3 py-2" data-widget="pushmenu" aria-label="Abrir menú lateral">
          <i class="fas fa-bars fa-lg" aria-hidden="true"></i>
        </button>

        {{-- Tabs --}}
        <ul class="nav nav-tabs v2-tab-nav flex-grow-1" id="v2-tabs" role="tablist">

          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="tab-pagos-link"
               data-toggle="pill" href="#tab-pagos" role="tab"
               aria-controls="tab-pagos" aria-selected="true">
              <i class="fas fa-money-bill-wave" aria-hidden="true"></i>
              Pagos
            </a>
          </li>

          <li class="nav-item" role="presentation">
            <a class="nav-link" id="tab-prestamos-link"
               data-toggle="pill" href="#tab-prestamos" role="tab"
               aria-controls="tab-prestamos" aria-selected="false">
              <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i>
              Préstamos
            </a>
          </li>

          <li class="nav-item" role="presentation">
            <a class="nav-link" id="tab-clientes-link"
               data-toggle="pill" href="#tab-clientes" role="tab"
               aria-controls="tab-clientes" aria-selected="false">
              <i class="fas fa-users" aria-hidden="true"></i>
              Clientes
            </a>
          </li>

          <li class="nav-item" role="presentation">
            <a class="nav-link" id="tab-anulados-link"
               data-toggle="pill" href="#tab-anulados" role="tab"
               aria-controls="tab-anulados" aria-selected="false">
              <i class="fas fa-ban" aria-hidden="true"></i>
              Anulados
            </a>
          </li>

        </ul>
      </div>
      {{-- /Header --}}

      {{-- Tab content --}}
      <div class="tab-content" id="v2-tabContent">

        <div class="tab-pane fade show active" id="tab-pagos"
             role="tabpanel" aria-labelledby="tab-pagos-link">
          @include('admin.v2.pago_card.tab-pago')
        </div>

        <div class="tab-pane fade" id="tab-prestamos"
             role="tabpanel" aria-labelledby="tab-prestamos-link">
          @include('admin.v2.pago_card.tab-prestamos')
        </div>

        <div class="tab-pane fade" id="tab-clientes"
             role="tabpanel" aria-labelledby="tab-clientes-link">
          @include('admin.v2.pago_card.tab-clientes')
        </div>

        <div class="tab-pane fade" id="tab-anulados"
             role="tabpanel" aria-labelledby="tab-anulados-link">
          @include('admin.v2.pago_card.tab-anulados')
        </div>

      </div>
      {{-- /Tab content --}}

    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════ --}}
{{-- MODAL: Registrar / Editar pago                      --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-pd" tabindex="-1"
     role="dialog" aria-labelledby="modal-pd-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">

      <div class="card card-danger mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="modal-title-pd mb-0 flex-grow-1" id="modal-pd-titulo"></h6>
          <button type="button" class="btn btn-sm btn-secondary ml-2"
                  data-dismiss="modal" aria-label="Cerrar modal de pago">
            <i class="fas fa-times" aria-hidden="true"></i> Cerrar
          </button>
        </div>
        <span id="form_result" role="alert" aria-live="polite"></span>
      </div>

      <form id="form-general" name="form-general"
            class="form-horizontal" method="post"
            novalidate aria-label="Formulario de pago">
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


{{-- ════════════════════════════════════════════════════ --}}
{{-- MODAL: Detalle cuotas del préstamo                  --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-d" tabindex="-1"
     role="dialog" aria-labelledby="modal-d-titulo" aria-modal="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="modal-title-d mb-0 flex-grow-1" id="modal-d-titulo"></h6>
          <button type="button" class="btn btn-sm btn-primary"
                  data-dismiss="modal" aria-label="Cerrar detalle de cuotas">
            <i class="fas fa-times" aria-hidden="true"></i> Cerrar
          </button>
        </div>
        <div class="card-body table-responsive p-2">
          <table id="detalleCuota"
                 class="table table-sm table-striped table-bordered text-nowrap"
                 style="width:100%"
                 aria-label="Detalle de cuotas del préstamo">
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════ --}}
{{-- MODAL: Detalle de pagos realizados                  --}}
{{-- ════════════════════════════════════════════════════ --}}
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


{{-- ════════════════════════════════════════════════════ --}}
{{-- MODAL: Adelanto de cuotas                           --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-acuotas" tabindex="-1"
     role="dialog" aria-labelledby="modal-acuotas-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="modal-title-acuotas mb-0 flex-grow-1" id="modal-acuotas-titulo"></h6>
          <button type="button" class="btn btn-sm btn-primary"
                  data-dismiss="modal" aria-label="Cerrar modal de adelanto de cuotas">
            <i class="fas fa-times" aria-hidden="true"></i> Cerrar
          </button>
        </div>
        <div class="card-body table-responsive p-2">
          <table id="pagoa"
                 class="table table-hover table-sm responsive"
                 style="width:100%"
                 aria-label="Tabla de adelanto de cuotas">
            <thead class="thead-light">
              <tr>
                <th scope="col">Acciones</th>
                <th scope="col"># Cuota</th>
                <th scope="col">Fecha</th>
                <th scope="col">Estado</th>
                <th scope="col">Nombres</th>
                <th scope="col">Apellidos</th>
                <th scope="col">Id Préstamo</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════ --}}
{{-- MODAL: Cuotas atrasadas                             --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-atrasosp" tabindex="-1"
     role="dialog" aria-labelledby="modal-atrasosp-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="modal-title-atrasosp mb-0 flex-grow-1" id="modal-atrasosp-titulo"></h6>
          <button type="button" class="btn btn-sm btn-danger"
                  data-dismiss="modal" aria-label="Cerrar modal de atrasos">
            <i class="fas fa-times" aria-hidden="true"></i> Cerrar
          </button>
        </div>
        <div class="card-body table-responsive p-2">
          <table id="atrasosp"
                 class="table table-hover table-sm responsive"
                 style="width:100%"
                 aria-label="Tabla de cuotas atrasadas">
            <thead class="thead-light">
              <tr>
                <th scope="col">Acciones</th>
                <th scope="col"># Cuota</th>
                <th scope="col">Fecha</th>
                <th scope="col">Estado</th>
                <th scope="col">Nombres</th>
                <th scope="col">Apellidos</th>
                <th scope="col">Id Préstamo</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear cliente                                --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-u-cli" tabindex="-1"
     role="dialog" aria-labelledby="modal-cli-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card card-info mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="mb-0 flex-grow-1" id="modal-cli-titulo">
            <i class="fas fa-user-plus" aria-hidden="true"></i> Crear Cliente
          </h6>
          <button type="button" class="btn btn-sm btn-secondary"
                  data-dismiss="modal" aria-label="Cerrar modal de cliente">
            <i class="fas fa-times" aria-hidden="true"></i> Cerrar
          </button>
        </div>
        <form id="form-cli" class="form-horizontal" method="post"
              novalidate aria-label="Formulario crear cliente">
          @csrf
          <div class="card-body">
            @include('admin.v2.pago_card.form-cli')
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-info">
              <i class="fas fa-save" aria-hidden="true"></i> Guardar cliente
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear préstamo                               --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-pc" tabindex="-1"
     role="dialog" aria-labelledby="modal-pc-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card card-success mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="mb-0 flex-grow-1" id="modal-pc-titulo">
            <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i> Crear Préstamo
          </h6>
          <button type="button" class="btn btn-sm btn-secondary"
                  data-dismiss="modal" aria-label="Cerrar modal de préstamo">
            <i class="fas fa-times" aria-hidden="true"></i> Cerrar
          </button>
        </div>
        <form id="form-prestamo" class="form-horizontal" method="post"
              novalidate aria-label="Formulario crear préstamo">
          @csrf
          <div class="card-body">
            @include('admin.v2.pago_card.form-prestamo')
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save" aria-hidden="true"></i> Guardar préstamo
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
