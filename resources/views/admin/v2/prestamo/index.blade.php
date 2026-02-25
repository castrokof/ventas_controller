{{-- resources/views/admin/v2/prestamo/index.blade.php --}}
@extends("theme.$theme.layout")

@section('titulo')
    Préstamos v2
@endsection

@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet">
<link href="{{asset("assets/$theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css")}}" rel="stylesheet">
<link href="{{asset("assets/css/select2-bootstrap.min.css")}}" rel="stylesheet">
<link href="{{asset("assets/css/select2.min.css")}}" rel="stylesheet">
<style>
/* ── Skeleton loader ─────────────────────────────── */
.skeleton-cell {
    display: inline-block; height: 13px; border-radius: 3px;
    background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* ── Estado de filas ─────────────────────────────── */
.row-atrasado  td { background-color: #f8d7da !important; }
.row-pagado    td { background-color: #d4edda !important; }

/* ── Botones acción tabla ────────────────────────── */
.btn-v2-action {
    display: inline-flex; align-items: center; justify-content: center;
    border: none; border-radius: 50px; padding: 5px 10px; font-size: 11px;
    cursor: pointer; transition: transform .15s, box-shadow .15s;
    box-shadow: 0 3px 8px rgba(0,0,0,.2);
}
.btn-v2-action:hover  { transform: translateY(-1px); box-shadow: 0 5px 12px rgba(0,0,0,.25); }
.btn-v2-action:active { transform: translateY(0); }

/* ── Loader modal ────────────────────────────────── */
.v2-loader {
    display: none; position: absolute; inset: 0;
    background: rgba(255,255,255,.85); z-index: 10;
    place-items: center;
}
.v2-loader.active { display: grid; }

/* ── Columnas compactas ──────────────────────────── */
#tabla-prestamos th,
#tabla-prestamos td { white-space: nowrap; font-size: 12px; }
</style>
@endsection

@section('contenido')
<div class="row">
  <div class="col-12">
    <div class="card card-success shadow-sm">

      {{-- ── Header ───────────────────────────────────────────── --}}
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-file-invoice-dollar mr-1" aria-hidden="true"></i>
          Préstamos activos
        </h5>
        <div class="card-tools">
          <button type="button" id="btn-crear-prestamo"
                  class="btn btn-sm btn-light"
                  aria-label="Abrir formulario para crear un nuevo préstamo">
            <i class="fas fa-plus-circle mr-1" aria-hidden="true"></i>
            Crear préstamo
          </button>
        </div>
      </div>

      {{-- ── Cuerpo ───────────────────────────────────────────── --}}
      <div class="card-body table-responsive p-2">

        {{-- Skeleton mientras carga DataTables --}}
        <div id="skeleton-prestamos" role="status" aria-label="Cargando préstamos">
          <table class="table table-sm" aria-hidden="true">
            <thead class="thead-light">
              <tr>
                @foreach(['Acciones','Consec.','Id','Cliente','Monto','Total','Saldo','Tipo','Cuotas','Interés','Estado'] as $h)
                  <th>{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @for ($i = 0; $i < 6; $i++)
              <tr>
                @for ($j = 0; $j < 11; $j++)
                <td><span class="skeleton-cell" style="width:{{ [60,30,30,80,45,45,45,50,35,35,50][$j] }}px">&nbsp;</span></td>
                @endfor
              </tr>
              @endfor
            </tbody>
          </table>
        </div>

        {{-- Tabla real (oculta hasta que DataTables inicialice) --}}
        <div id="wrapper-prestamos" style="display:none">
          <table id="tabla-prestamos"
                 class="table table-hover table-sm"
                 style="width:100%"
                 aria-label="Listado de préstamos activos"
                 role="grid">
            <thead class="thead-light">
              <tr>
                <th scope="col">Acciones</th>
                <th scope="col">Consecutivo</th>
                <th scope="col">Id</th>
                <th scope="col">Nombres</th>
                <th scope="col">Apellidos</th>
                <th scope="col">Monto</th>
                <th scope="col">Monto Total</th>
                <th scope="col">Saldo</th>
                <th scope="col">Atrasado</th>
                <th scope="col">C. Atrasadas</th>
                <th scope="col">Tipo Pago</th>
                <th scope="col">Cuotas</th>
                <th scope="col">C. Pendientes</th>
                <th scope="col">Interés</th>
                <th scope="col">Valor Cuota</th>
                <th scope="col">Fecha Inicial</th>
                <th scope="col">Estado</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

      </div>
      {{-- /card-body --}}

    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════ --}}
{{-- MODAL: Crear préstamo                               --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-crear-prestamo" tabindex="-1"
     role="dialog" aria-labelledby="modal-crear-titulo" aria-modal="true"
     style="overflow-y:scroll">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content position-relative">

      {{-- Loader durante el guardado --}}
      <div class="v2-loader" id="loader-crear" aria-hidden="true">
        <img src="{{asset("assets/$theme/dist/img/loader6.gif")}}"
             alt="Procesando..." style="width:120px">
      </div>

      <div class="card card-success mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="mb-0 flex-grow-1" id="modal-crear-titulo">
            <i class="fas fa-file-invoice-dollar mr-1" aria-hidden="true"></i>
            Crear Préstamo
          </h6>
          <button type="button" class="btn btn-sm btn-secondary"
                  data-dismiss="modal" aria-label="Cerrar formulario de préstamo">
            <i class="fas fa-times" aria-hidden="true"></i> Cerrar
          </button>
        </div>

        <div id="form-result-crear" role="alert" aria-live="polite"></div>

        <form id="form-crear-prestamo" method="post" novalidate
              aria-label="Formulario para crear un nuevo préstamo">
          @csrf
          <div class="card-body">
            @include('admin.v2.prestamo.form-prestamo')
          </div>
          <div class="card-footer text-right">
            <button type="button" class="btn btn-secondary mr-2"
                    data-dismiss="modal">Cancelar</button>
            <button type="submit" id="btn-guardar-prestamo"
                    class="btn btn-success">
              <i class="fas fa-save mr-1" aria-hidden="true"></i>
              Guardar préstamo
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════ --}}
{{-- MODAL: Detalle de cuotas                            --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-cuotas" tabindex="-1"
     role="dialog" aria-labelledby="modal-cuotas-titulo" aria-modal="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="mb-0 flex-grow-1" id="modal-cuotas-titulo">
            <i class="fas fa-list-ol mr-1" aria-hidden="true"></i>
            Detalle de cuotas
          </h6>
          <button type="button" class="btn btn-sm btn-primary"
                  data-dismiss="modal" aria-label="Cerrar detalle de cuotas">
            <i class="fas fa-times" aria-hidden="true"></i> Cerrar
          </button>
        </div>
        <div class="card-body table-responsive p-2">
          <table id="tabla-cuotas"
                 class="table table-sm table-striped table-bordered"
                 style="width:100%"
                 aria-label="Cuotas del préstamo">
            <thead class="thead-light">
              <tr>
                <th scope="col"># Cuota</th>
                <th scope="col">Valor</th>
                <th scope="col">Fecha</th>
                <th scope="col">Abonado</th>
                <th scope="col">Estado</th>
              </tr>
            </thead>
            <tbody id="body-cuotas"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


{{-- ════════════════════════════════════════════════════ --}}
{{-- MODAL: Detalle de pagos                             --}}
{{-- ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-pagos" tabindex="-1"
     role="dialog" aria-labelledby="modal-pagos-titulo" aria-modal="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card mb-0">
        <div class="card-header d-flex align-items-center">
          <h6 class="mb-0 flex-grow-1" id="modal-pagos-titulo">
            <i class="fas fa-money-bill-wave mr-1" aria-hidden="true"></i>
            Detalle de pagos realizados
          </h6>
          <button type="button" class="btn btn-sm btn-primary"
                  data-dismiss="modal" aria-label="Cerrar detalle de pagos">
            <i class="fas fa-times" aria-hidden="true"></i> Cerrar
          </button>
        </div>
        <div class="card-body table-responsive p-2">
          <table id="tabla-pagos"
                 class="table table-sm table-striped table-bordered"
                 style="width:100%"
                 aria-label="Pagos realizados para el préstamo">
            <thead class="thead-light">
              <tr>
                <th scope="col">Id Préstamo</th>
                <th scope="col"># Cuota</th>
                <th scope="col">Valor abono</th>
                <th scope="col">Observación</th>
                <th scope="col">Fecha cuota</th>
                <th scope="col">Fecha pago</th>
              </tr>
            </thead>
            <tbody id="body-pagos"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@section('scriptsPlugins')
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/datatables-responsive/js/dataTables.responsive.min.js")}}"></script>
<script src="{{asset("assets/js/jquery-select2/select2.min.js")}}"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
{{-- JS V2 extraído en archivo separado (no inline) --}}
<script>
    // Inyectamos la URL de la ruta como variable global para que el JS externo la use
    window.V2_PRESTAMO_URL       = "{{ route('admin.v2.prestamo.index') }}";
    window.V2_GUARDAR_URL        = "{{ route('admin.v2.prestamo.guardar') }}";
    window.V2_CSRF               = "{{ csrf_token() }}";
</script>
<script src="{{asset("assets/pages/scripts/admin/prestamo/v2.js")}}" defer></script>
@endsection
