{{-- resources/views/admin/v2/cliente/index.blade.php --}}
@extends("theme.$theme.layout")

@section('titulo')
    Clientes v2
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
#tabla-clientes th,
#tabla-clientes td { white-space: nowrap; font-size: 12px; }
</style>
@endsection

@section('contenido')
<div class="row">
  <div class="col-12">
    <div class="card card-primary shadow-sm">

      {{-- ── Header ───────────────────────────────────────────── --}}
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-users mr-1" aria-hidden="true"></i>
          Clientes
        </h5>
        <div class="card-tools">
          <button type="button" id="btn-crear-cliente"
                  class="btn btn-sm btn-light"
                  aria-label="Abrir formulario para crear un nuevo cliente">
            <i class="fas fa-plus-circle mr-1" aria-hidden="true"></i>
            Crear cliente
          </button>
        </div>
      </div>

      {{-- ── Cuerpo ───────────────────────────────────────────── --}}
      <div class="card-body table-responsive p-2">

        {{-- Skeleton mientras carga DataTables --}}
        <div id="skeleton-clientes" role="status" aria-label="Cargando clientes">
          <table class="table table-sm" aria-hidden="true">
            <thead class="thead-light">
              <tr>
                @foreach(['Acciones','Consec.','Nombres','Apellidos','Tipo Doc.','Documento','Teléfono','Celular','Dirección','Activo'] as $h)
                  <th>{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @for ($i = 0; $i < 6; $i++)
              <tr>
                @for ($j = 0; $j < 10; $j++)
                <td><span class="skeleton-cell" style="width:{{ [80,35,90,90,60,70,70,70,100,40][$j] }}px">&nbsp;</span></td>
                @endfor
              </tr>
              @endfor
            </tbody>
          </table>
        </div>

        {{-- Tabla real (oculta hasta que DataTables inicialice) --}}
        <div id="wrapper-clientes" style="display:none">
          <table id="tabla-clientes"
                 class="table table-hover table-sm"
                 role="grid"
                 aria-label="Listado de clientes">
            <thead class="thead-light">
              <tr>
                <th>Acciones</th>
                <th>Consec.</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Tipo Doc.</th>
                <th>Documento</th>
                <th>Teléfono</th>
                <th>Celular</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th>País</th>
                <th>Ciudad</th>
                <th>Barrio</th>
                <th>Sector</th>
                <th>Activo</th>
                <th>Observación</th>
                <th>Usuario ID</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

      </div>{{-- /.card-body --}}
    </div>{{-- /.card --}}
  </div>
</div>

{{-- ══════════════════════════════════════════════════════
     Modal: Crear / Editar cliente
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-cliente" tabindex="-1" role="dialog"
     aria-labelledby="modal-cliente-titulo" aria-modal="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="modal-cliente-titulo">
          <i class="fas fa-user mr-1" aria-hidden="true"></i>
          <span id="modal-cliente-heading">Cliente</span>
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="form-cliente" novalidate>
        @csrf
        <input type="hidden" name="_method" id="form-method" value="POST">
        <input type="hidden" id="hidden_id" value="">
        <input type="hidden" id="action" value="Add">

        <div class="modal-body" style="position:relative">
          {{-- Loader modal --}}
          <div class="v2-loader" id="loader-cliente" role="status" aria-label="Procesando">
            <div class="spinner-border text-primary" role="status">
              <span class="sr-only">Cargando...</span>
            </div>
          </div>

          <span id="form_result_cliente" role="alert" aria-live="polite"></span>

          @include('admin.v2.cliente.form')
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
            <i class="fas fa-times mr-1" aria-hidden="true"></i>Cancelar
          </button>
          <button type="submit" id="btn-submit-cliente" class="btn btn-primary btn-sm">
            <i class="fas fa-save mr-1" aria-hidden="true"></i>
            <span id="btn-submit-text">Guardar</span>
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════
     Modal: Detalle de préstamos del cliente
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-detalle" tabindex="-1" role="dialog"
     aria-labelledby="modal-detalle-titulo" aria-modal="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h5 class="modal-title text-white" id="modal-detalle-titulo">
          <i class="fas fa-atlas mr-1" aria-hidden="true"></i>
          Detalle de préstamos
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contenido-detalle">
        <p class="text-muted text-center">Cargando...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
          <i class="fas fa-times mr-1" aria-hidden="true"></i>Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section("scriptsPlugins")
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/datatables-responsive/js/dataTables.responsive.min.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/select2/js/select2.full.min.js")}}"></script>

<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script>
/* ── i18n DataTables ───────────────────────────────────────────── */
var idioma_espanol = {
    "sProcessing": "Procesando...", "sLengthMenu": "Mostrar _MENU_ registros",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "Ningún dato disponible en esta tabla",
    "sInfo": "Mostrando registros del _START_ al _END_ de _TOTAL_",
    "sInfoEmpty": "Mostrando 0 al 0 de 0 registros",
    "sInfoFiltered": "(filtrado de _MAX_ registros)",
    "sSearch": "Buscar:", "sLoadingRecords": "Cargando...",
    "oPaginate": { "sFirst":"Primero","sLast":"Último","sNext":"Siguiente","sPrevious":"Anterior" },
    "oAria": { "sSortAscending": ": orden ascendente", "sSortDescending": ": orden descendente" },
    "buttons": { "copy": "Copiar", "colvis": "Visibilidad" }
};

$(function () {

    /* ── Select2 ────────────────────────────────────────────────── */
    $('.select2bs4').select2({ theme: 'bootstrap4' });

    /* ── DataTable ──────────────────────────────────────────────── */
    var tabla = $('#tabla-clientes').DataTable({
        language: idioma_espanol,
        processing: true,
        serverSide: true,
        responsive: true,
        order: [[1, 'asc']],
        lengthMenu: [[25, 50, 100, 500, -1], [25, 50, 100, 500, 'Todo']],
        dom: '<"row"<"col-md-4"l><"col-md-5"f><"col-md-3"B>>rt<"row"<"col-md-8"i><"col-md-4"p>>',
        buttons: [
            { extend: 'copyHtml5',  className: 'btn btn-outline-primary btn-sm',  titleAttr: 'Copiar' },
            { extend: 'excelHtml5', className: 'btn btn-outline-success btn-sm',  titleAttr: 'Excel', title: 'clientes' },
            { extend: 'csvHtml5',   className: 'btn btn-outline-warning btn-sm',  titleAttr: 'CSV' },
            { extend: 'pdfHtml5',   className: 'btn btn-outline-secondary btn-sm',titleAttr: 'PDF', title: 'clientes' }
        ],
        ajax: { url: '{{ route("admin.v2.cliente.index") }}' },
        columns: [
            { data: 'action',         name: 'action',         orderable: false, searchable: false },
            { data: 'consecutivo',    name: 'consecutivo' },
            { data: 'nombres',        name: 'nombres' },
            { data: 'apellidos',      name: 'apellidos' },
            { data: 'tipo_documento', name: 'tipo_documento' },
            { data: 'documento',      name: 'documento' },
            { data: 'telefono',       name: 'telefono' },
            { data: 'celular',        name: 'celular' },
            { data: 'direccion',      name: 'direccion' },
            { data: 'estado',         name: 'estado' },
            { data: 'pais',           name: 'pais' },
            { data: 'ciudad',         name: 'ciudad' },
            { data: 'barrio',         name: 'barrio' },
            { data: 'sector',         name: 'sector' },
            { data: 'activo',         name: 'activo' },
            { data: 'observacion_cli',name: 'observacion_cli' },
            { data: 'usuario_id',     name: 'usuario_id' }
        ],
        initComplete: function () {
            $('#skeleton-clientes').hide();
            $('#wrapper-clientes').show();
        }
    });

    /* ── Abrir modal: CREAR ─────────────────────────────────────── */
    $('#btn-crear-cliente').on('click', function () {
        resetForm();
        $('#modal-cliente-heading').text('Nuevo cliente');
        $('#btn-submit-text').text('Guardar');
        $('#modal-cliente').modal('show');
    });

    /* ── Abrir modal: EDITAR ────────────────────────────────────── */
    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $.ajax({
            url: '{{ url("admin/v2/cliente") }}/' + id + '/editar',
            dataType: 'json',
            success: function (data) {
                var d = data.result;
                $('#nombrescli').val(d.nombres);
                $('#apellidoscli').val(d.apellidos);
                $('#tipo_documentocli').val(d.tipo_documento).trigger('change');
                $('#documentocli').val(d.documento);
                $('#paiscli').val(d.pais);
                $('#estadocli').val(d.estado);
                $('#ciudadcli').val(d.ciudad);
                $('#barriocli').val(d.barrio);
                $('#sectorcli').val(d.sector);
                $('#direccioncli').val(d.direccion);
                $('#celularcli').val(d.celular);
                $('#telefonocli').val(d.telefono);
                $('#consecutivocli').val(d.consecutivo);
                $('#observacioncli').val(d.observacion_cli);
                $('#activocli').val(d.activo).trigger('change');
                $('#hidden_id').val(id);
                $('#action').val('Edit');
                $('#modal-cliente-heading').text('Editar cliente #' + d.consecutivo);
                $('#btn-submit-text').text('Actualizar');
                $('#modal-cliente').modal('show');
            },
            error: function (xhr) {
                if (xhr.status === 403) {
                    Swal.fire('Sin permiso', 'No tienes permiso para esta acción.', 'warning');
                }
            }
        });
    });

    /* ── Envío del formulario ────────────────────────────────────── */
    $('#form-cliente').on('submit', function (e) {
        e.preventDefault();

        var isEdit  = ($('#action').val() === 'Edit');
        var id      = $('#hidden_id').val();
        var url     = isEdit
            ? '{{ url("admin/v2/cliente") }}/' + id
            : '{{ route("admin.v2.cliente.guardar") }}';
        var method  = isEdit ? 'PUT' : 'POST';
        var msg     = isEdit ? 'Estás por actualizar el cliente' : 'Estás por crear un cliente';

        Swal.fire({
            title: '¿Estás seguro?', text: msg,
            icon: 'question',
            showCancelButton: true, showCloseButton: true,
            confirmButtonText: 'Aceptar', cancelButtonText: 'Cancelar'
        }).then(function (res) {
            if (!res.value) return;

            loaderOn();

            $.ajax({
                url: url,
                method: method,
                data: $('#form-cliente').serialize(),
                dataType: 'json',
                success: function (data) {
                    loaderOff();
                    if (data.errors) {
                        var html = '<div class="alert alert-danger alert-dismissible">'
                            + '<button type="button" class="close" data-dismiss="alert">&times;</button>'
                            + '<strong>Errores:</strong><ul>';
                        data.errors.forEach(function (err) { html += '<li>' + err + '</li>'; });
                        html += '</ul></div>';
                        $('#form_result_cliente').html(html);
                        return;
                    }
                    $('#modal-cliente').modal('hide');
                    tabla.ajax.reload();
                    var titulo = data.success === 'ok' ? 'Cliente creado' : 'Cliente actualizado';
                    Swal.fire({ icon: 'success', title: titulo, showConfirmButton: false, timer: 1500 });
                },
                error: function () {
                    loaderOff();
                    Swal.fire('Error', 'No se pudo guardar. Intente de nuevo.', 'error');
                }
            });
        });
    });

    /* ── Detalle de préstamos ────────────────────────────────────── */
    $(document).on('click', '.detalle', function () {
        var id = $(this).attr('id');
        $('#contenido-detalle').html('<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>');
        $('#modal-detalle').modal('show');

        $.ajax({
            url: '{{ url("admin/v2/cliente") }}/' + id + '/detalle',
            dataType: 'json',
            success: function (data) {
                if (!data.result || data.result.length === 0) {
                    $('#contenido-detalle').html('<p class="text-muted text-center">Sin préstamos registrados.</p>');
                    return;
                }
                var html = '';
                data.result.forEach(function (item) {
                    html += '<div class="row mb-2">'
                        + '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-info">'
                        + '<span class="info-box-icon"><i class="fas fa-money-check-alt"></i></span>'
                        + '<div class="info-box-content">'
                        + '<span class="info-box-text">Monto</span>'
                        + '<span class="info-box-number">$ ' + (item.monto || 0) + '</span></div></div></div>'

                        + '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-success">'
                        + '<span class="info-box-icon"><i class="fas fa-hashtag"></i></span>'
                        + '<div class="info-box-content">'
                        + '<span class="info-box-text">Cuotas</span>'
                        + '<span class="info-box-number">' + (item.cuotas || 0) + '</span></div></div></div>'

                        + '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-warning">'
                        + '<span class="info-box-icon"><i class="fab fa-paypal"></i></span>'
                        + '<div class="info-box-content">'
                        + '<span class="info-box-text">Tipo pago</span>'
                        + '<span class="info-box-text">' + (item.tipo_pago || '—') + '</span></div></div></div>'

                        + '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-danger">'
                        + '<span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>'
                        + '<div class="info-box-content">'
                        + '<span class="info-box-text">Saldo</span>'
                        + '<span class="info-box-number">$ ' + (item.monto_pendiente || 0) + '</span></div></div></div>'
                        + '</div>';
                });
                $('#contenido-detalle').html(html);
            },
            error: function () {
                $('#contenido-detalle').html('<p class="text-danger text-center">Error al cargar los datos.</p>');
            }
        });
    });

    /* ── Helpers ─────────────────────────────────────────────────── */
    function resetForm() {
        $('#form-cliente')[0].reset();
        $('#form_result_cliente').html('');
        $('#hidden_id').val('');
        $('#action').val('Add');
        $('.select2bs4').trigger('change');
    }

    function loaderOn()  { $('#loader-cliente').addClass('active'); $('#btn-submit-cliente').prop('disabled', true); }
    function loaderOff() { $('#loader-cliente').removeClass('active'); $('#btn-submit-cliente').prop('disabled', false); }

});
</script>
@endsection
