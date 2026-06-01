@extends("theme.$theme.layout")

@section('titulo')
    Informes
@endsection

@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet" type="text/css"/>
<style>
.stat-card {
    border-radius: 10px;
    border: none;
    transition: transform .15s, box-shadow .15s;
    overflow: hidden;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,.18) !important;
}
.stat-icon {
    font-size: 2.8rem;
    opacity: .18;
    position: absolute;
    right: 14px;
    bottom: 8px;
}
.stat-value { font-size: 1.6rem; font-weight: 700; line-height: 1.1; }
.stat-label { font-size: .75rem; opacity: .9; margin-top: 4px; }
.stat-loading { opacity: .5; font-size: 1rem; }
.inf-filter-card .card-header { background: transparent; border-bottom: 1px solid rgba(0,0,0,.08); }
.tab-pane { padding: 0; }
#tab-informes .nav-link { font-size: .85rem; padding: .5rem .9rem; }
#tab-informes .nav-link i { opacity: .7; }
</style>
@endsection

@section('contenido')

{{-- ── Encabezado ──────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center mb-3">
  <div>
    <h4 class="mb-0 font-weight-bold">
      <i class="fas fa-chart-bar mr-2 text-primary" aria-hidden="true"></i>Informes
    </h4>
    <small class="text-muted" id="fecha-hoy-inf"></small>
  </div>
</div>

{{-- ── Filtros ──────────────────────────────────────────────────────────── --}}
<div class="card card-outline card-primary mb-3 inf-filter-card">
  <div class="card-header py-2">
    <h6 class="mb-0 font-weight-bold">
      <i class="fas fa-filter mr-1 text-muted" aria-hidden="true"></i>Filtros
    </h6>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse">
        <i class="fas fa-minus" aria-hidden="true"></i>
      </button>
    </div>
  </div>
  <div class="card-body py-2 px-3">
    <div class="row align-items-end">

      <div class="col-6 col-md-3 mb-2">
        <label class="mb-1" style="font-size:.8rem;font-weight:600">Desde</label>
        <input type="date" id="fechaini" class="form-control form-control-sm">
      </div>

      <div class="col-6 col-md-3 mb-2">
        <label class="mb-1" style="font-size:.8rem;font-weight:600">Hasta</label>
        <input type="date" id="fechafin" class="form-control form-control-sm">
      </div>

      <div class="col-12 col-md-3 mb-2">
        <label class="mb-1" style="font-size:.8rem;font-weight:600">Usuario</label>
        <select id="usuario" class="form-control form-control-sm select2bs4" style="width:100%">
          <option value="">— Todos —</option>
          @foreach ($usuarios as $uid => $nombre)
          <option value="{{ $uid }}">{{ $nombre }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md-3 mb-2 d-flex align-items-end" style="gap:6px">
        <button id="reset" class="btn btn-sm btn-default flex-fill">
          <i class="fas fa-undo mr-1" aria-hidden="true"></i>Limpiar
        </button>
        <button id="buscar" class="btn btn-sm btn-primary flex-fill">
          <i class="fas fa-search mr-1" aria-hidden="true"></i>Buscar
        </button>
      </div>

    </div>
  </div>
</div>

{{-- ── Tarjetas de resumen ───────────────────────────────────────────────── --}}
<div class="row mb-3" id="stat-row">

  <div class="col-6 col-md-3 mb-2">
    <div class="card stat-card bg-gradient-info text-white shadow-sm position-relative p-3">
      <div class="stat-value" id="stat-cobrado"><span class="stat-loading">…</span></div>
      <div class="stat-label">Total cobrado</div>
      <i class="fas fa-motorcycle stat-icon" aria-hidden="true"></i>
    </div>
  </div>

  <div class="col-6 col-md-3 mb-2">
    <div class="card stat-card bg-gradient-success text-white shadow-sm position-relative p-3">
      <div class="stat-value" id="stat-atrasado"><span class="stat-loading">…</span></div>
      <div class="stat-label">Total atrasos</div>
      <i class="fas fa-handshake stat-icon" aria-hidden="true"></i>
    </div>
  </div>

  <div class="col-6 col-md-3 mb-2">
    <div class="card stat-card bg-gradient-warning text-white shadow-sm position-relative p-3">
      <div class="stat-value" id="stat-prestamos"><span class="stat-loading">…</span></div>
      <div class="stat-label">Total préstamos</div>
      <i class="fas fa-money-bill-alt stat-icon" aria-hidden="true"></i>
    </div>
  </div>

  <div class="col-6 col-md-3 mb-2">
    <div class="card stat-card bg-gradient-danger text-white shadow-sm position-relative p-3">
      <div class="stat-value" id="stat-gastos"><span class="stat-loading">…</span></div>
      <div class="stat-label">Total gastos</div>
      <i class="fas fa-route stat-icon" aria-hidden="true"></i>
    </div>
  </div>

</div>

{{-- ── Pestañas de detalle ───────────────────────────────────────────────── --}}
<div class="row">
  <div class="col-12">
    <div class="card card-primary card-tabs">
      <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tab-informes" role="tablist">

          <li class="nav-item">
            <a class="nav-link active"
               id="tab-pagos-lnk"
               data-toggle="pill"
               href="#tab-pagos"
               role="tab"
               aria-controls="tab-pagos"
               aria-selected="true">
              <i class="fas fa-receipt mr-1" aria-hidden="true"></i>Pagos
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link"
               id="tab-prestamos-lnk"
               data-toggle="pill"
               href="#tab-prestamos"
               role="tab"
               aria-controls="tab-prestamos"
               aria-selected="false">
              <i class="fas fa-file-invoice-dollar mr-1" aria-hidden="true"></i>Préstamos
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link"
               id="tab-gastos-lnk"
               data-toggle="pill"
               href="#tab-gastos"
               role="tab"
               aria-controls="tab-gastos"
               aria-selected="false">
              <i class="fas fa-receipt mr-1" aria-hidden="true"></i>Gastos
            </a>
          </li>

        </ul>
      </div>

      <div class="tab-content" id="tab-content-informes">

        <div class="tab-pane fade active show" id="tab-pagos" role="tabpanel" aria-labelledby="tab-pagos-lnk">
          @include('admin.admin.form-pagos')
        </div>

        <div class="tab-pane fade" id="tab-prestamos" role="tabpanel" aria-labelledby="tab-prestamos-lnk">
          @include('admin.admin.form-prestamos')
        </div>

        <div class="tab-pane fade" id="tab-gastos" role="tabpanel" aria-labelledby="tab-gastos-lnk">
          @include('admin.admin.form-gastos')
        </div>

      </div>
    </div>
  </div>
</div>

{{-- ── Modal detalle ────────────────────────────────────────────────────── --}}
<div class="modal fade" tabindex="-1" id="modal-d" role="dialog" aria-labelledby="modal-d-title">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="card mb-0">
        <div class="card-header">
          <h6 class="modal-title-d mb-0" id="modal-d-title"></h6>
          <div class="card-tools">
            <button type="button" class="btn btn-sm bg-gradient-primary" data-dismiss="modal">
              <i class="fas fa-times mr-1" aria-hidden="true"></i>Cerrar
            </button>
          </div>
        </div>
        <div class="card-body table-responsive p-2">
          <table id="detallePagos" class="table table-hover text-nowrap table-striped table-bordered" style="width:100%"></table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section("scriptsPlugins")
<script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}" type="text/javascript"></script>

<script>
var idioma_espanol = {
  "sProcessing":    "Procesando...",
  "sLengthMenu":    "Mostrar _MENU_ registros",
  "sZeroRecords":   "No se encontraron resultados",
  "sEmptyTable":    "Ningún dato disponible en esta tabla",
  "sInfo":          "Registros del _START_ al _END_ de _TOTAL_",
  "sInfoEmpty":     "Registros del 0 al 0 de 0",
  "sInfoFiltered":  "(filtrado de _MAX_ registros)",
  "sSearch":        "Buscar:",
  "sLoadingRecords":"Cargando...",
  "oPaginate":      { "sFirst":"Primero","sLast":"Último","sNext":"Siguiente","sPrevious":"Anterior" },
  "oAria":          { "sSortAscending":": ordenar ascendente","sSortDescending":": ordenar descendente" },
  "buttons":        { "copy":"Copiar","colvis":"Visibilidad" }
};

var dtButtons = [
  { extend:'copyHtml5',  titleAttr:'Copy',  className:'btn btn-info btn-sm'    },
  { extend:'excelHtml5', titleAttr:'Excel', className:'btn btn-success btn-sm' },
  { extend:'csvHtml5',   titleAttr:'CSV',   className:'btn btn-warning btn-sm' },
  { extend:'pdfHtml5',   titleAttr:'PDF',   className:'btn btn-primary btn-sm' }
];

function fmtMoney(val) {
  var n = parseInt(val) || 0;
  return '$' + n.toLocaleString('es');
}

/* ── Tarjetas de resumen ──────────────────────────────────────── */
function cargarResumen(fechaini, fechafin, usuario) {
  $('#stat-cobrado, #stat-atrasado, #stat-prestamos, #stat-gastos')
    .html('<span class="stat-loading">…</span>');

  $.ajax({
    url: "{{route('informes')}}",
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    data: { fechaini: fechaini, fechafin: fechafin, usuario: usuario },
    dataType: 'json',
    success: function(data) {
      $('#stat-cobrado').text(fmtMoney(data.result[0]  ? data.result[0].cobrado   : 0));
      $('#stat-atrasado').text(fmtMoney(data.result1[0] ? data.result1[0].atrasado  : 0));
      $('#stat-prestamos').text(fmtMoney(data.result2[0] ? data.result2[0].prestamos : 0));
      $('#stat-gastos').text(fmtMoney(data.result3[0]   ? data.result3[0].gastos    : 0));
    },
    error: function() {
      $('#stat-cobrado, #stat-atrasado, #stat-prestamos, #stat-gastos').text('—');
    }
  });
}

/* ── DataTable: Pagos ─────────────────────────────────────────── */
function dtPagos(fechaini, fechafin, usuario) {
  return $('#tpago').DataTable({
    language: idioma_espanol,
    lengthMenu: [-1],
    processing: true,
    serverSide: true,
    aaSorting: [[5, 'asc']],
    ajax: {
      url: "{{route('informesp')}}",
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      data: { fechaini: fechaini, fechafin: fechafin, usuario: usuario }
    },
    columns: [
      { data:'pid',   name:'pid'   },
      { data:'cli',   name:'cli'   },
      { data:'va',    name:'va'    },
      { data:'vc',    name:'vc'    },
      { data:'c',     name:'c'     },
      { data:'fhp',   name:'fhp'   },
      { data:'obsp',  name:'obsp'  },
      { data:'emp',   name:'emp'   }
    ],
    footerCallback: function(row, data, start, end, display) {
      var api = this.api();
      var total = api.column(2, {page:'current'}).data()
        .reduce(function(a,b){ return parseInt(a)+parseInt(b); }, 0);
      $(api.column(2).footer()).html(total.toLocaleString('es'));
    },
    dom: 'Brtip',
    buttons: dtButtons
  });
}

/* ── DataTable: Préstamos ─────────────────────────────────────── */
function dtPrestamos(fechaini, fechafin, usuario) {
  return $('#tprestamo').DataTable({
    language: idioma_espanol,
    lengthMenu: [-1],
    processing: true,
    serverSide: true,
    aaSorting: [[8, 'asc']],
    ajax: {
      url: "{{route('informespo')}}",
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      data: { fechaini: fechaini, fechafin: fechafin, usuario: usuario }
    },
    columns: [
      { data:'poid',  name:'poid'  },
      { data:'cli',   name:'cli'   },
      { data:'vm',    name:'vm'    },
      { data:'tp',    name:'tp'    },
      { data:'in',    name:'in'    },
      { data:'tc',    name:'tc'    },
      { data:'vmt',   name:'vmt'   },
      { data:'vc',    name:'vc'    },
      { data:'fhpo',  name:'fhpo'  },
      { data:'obspo', name:'obspo' },
      { data:'emp',   name:'emp'   }
    ],
    footerCallback: function(row, data, start, end, display) {
      var api = this.api();
      var total = api.column(2, {page:'current'}).data()
        .reduce(function(a,b){ return parseInt(a)+parseInt(b); }, 0);
      $(api.column(2).footer()).html(total.toLocaleString('es'));
    },
    dom: 'Brtip',
    buttons: dtButtons
  });
}

/* ── DataTable: Gastos ────────────────────────────────────────── */
function dtGastos(fechaini, fechafin, usuario) {
  return $('#tgasto').DataTable({
    language: idioma_espanol,
    lengthMenu: [-1],
    processing: true,
    serverSide: true,
    aaSorting: [[3, 'asc']],
    ajax: {
      url: "{{route('informesg')}}",
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      data: { fechaini: fechaini, fechafin: fechafin, usuario: usuario }
    },
    columns: [
      { data:'id',          name:'id'          },
      { data:'monto',       name:'monto'       },
      { data:'descripcion', name:'descripcion' },
      { data:'created_at',  name:'created_at'  },
      { data:'emp',         name:'emp'         }
    ],
    footerCallback: function(row, data, start, end, display) {
      var api = this.api();
      var total = api.column(1, {page:'current'}).data()
        .reduce(function(a,b){ return parseInt(a)+parseInt(b); }, 0);
      $(api.column(1).footer()).html(total.toLocaleString('es'));
    },
    dom: 'Brtip',
    buttons: dtButtons
  });
}

function destroyTables() {
  if ($.fn.DataTable.isDataTable('#tpago'))     $('#tpago').DataTable().destroy();
  if ($.fn.DataTable.isDataTable('#tprestamo')) $('#tprestamo').DataTable().destroy();
  if ($.fn.DataTable.isDataTable('#tgasto'))    $('#tgasto').DataTable().destroy();
}

function cargarTodo(fechaini, fechafin, usuario) {
  cargarResumen(fechaini, fechafin, usuario);
  destroyTables();
  dtPagos(fechaini, fechafin, usuario);
  dtPrestamos(fechaini, fechafin, usuario);
  dtGastos(fechaini, fechafin, usuario);
}

$(function () {

  /* Fecha de hoy en el encabezado */
  var hoy = new Date();
  var opciones = { weekday:'long', year:'numeric', month:'long', day:'numeric' };
  $('#fecha-hoy-inf').text(hoy.toLocaleDateString('es', opciones));

  /* Valores por defecto en filtros: primer día del mes → hoy */
  var yyyy = hoy.getFullYear();
  var mm   = String(hoy.getMonth() + 1).padStart(2, '0');
  var dd   = String(hoy.getDate()).padStart(2, '0');
  var hoyStr    = yyyy + '-' + mm + '-' + dd;
  var primeroStr = yyyy + '-' + mm + '-01';
  $('#fechaini').val(primeroStr);
  $('#fechafin').val(hoyStr);

  /* Carga inicial */
  cargarTodo('', '', '');

  /* Botón Buscar */
  $('#buscar').on('click', function () {
    var fi  = $('#fechaini').val();
    var ff  = $('#fechafin').val();
    var uid = $('#usuario').val();
    if (!fi || !ff) {
      swal({ title: 'Selecciona un rango de fechas', icon: 'warning', buttons: { cancel: 'Cerrar' } });
      return;
    }
    cargarTodo(fi, ff, uid);
  });

  /* Botón Limpiar */
  $('#reset').on('click', function () {
    $('#fechaini').val(primeroStr);
    $('#fechafin').val(hoyStr);
    $('#usuario').val('').trigger('change');
    cargarTodo('', '', '');
  });

});
</script>
@endsection
