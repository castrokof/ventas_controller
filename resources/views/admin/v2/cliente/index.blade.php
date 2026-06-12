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
<link href="{{asset("assets/css/ios-form.css")}}?v={{ filemtime(public_path('assets/css/ios-form.css')) }}" rel="stylesheet">
@include('admin.v2._partials.mobile-styles')
@endsection

@section('contenido')

{{-- ── Tabla desktop (oculta en móvil) ─────────────────────── --}}
<div class="row v2-dt-wrapper">
  <div class="col-12">
    <div class="card card-primary shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-users mr-1"></i> Clientes
        </h5>
        <div class="card-tools">
          <button type="button" id="btn-crear-desktop"
                  class="btn btn-sm btn-light"
                  aria-label="Crear cliente">
            <i class="fas fa-plus-circle mr-1"></i>Crear
          </button>
        </div>
      </div>
      <div class="card-body table-responsive p-2">
        {{-- Skeleton --}}
        <div id="skeleton-clientes">
          <table class="table table-sm" aria-hidden="true">
            <thead class="thead-light">
              <tr>
                @foreach(['Acc.','Consec.','Nombres','Apellidos','Doc.','Teléf.','Celular','Dirección','Estado'] as $h)
                  <th>{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @for ($i=0; $i<6; $i++)
              <tr>
                @foreach([80,35,90,90,70,70,70,100,40] as $w)
                <td><span class="skeleton-cell" style="width:{{$w}}px">&nbsp;</span></td>
                @endforeach
              </tr>
              @endfor
            </tbody>
          </table>
        </div>
        <div id="dt-clientes-wrap" style="display:none">
          <table id="tabla-clientes" class="table table-hover table-sm" role="grid">
            <thead class="thead-light">
              <tr>
                <th>Acciones</th><th>Consec.</th><th>Nombres</th><th>Apellidos</th>
                <th>Tipo Doc.</th><th>Documento</th><th>Teléfono</th><th>Celular</th>
                <th>Dirección</th><th>Estado</th><th>País</th><th>Ciudad</th>
                <th>Barrio</th><th>Sector</th><th>Activo</th>
                <th>Observación</th><th>Usuario</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── Cards móvil (ocultas en desktop) ────────────────────── --}}
<div class="v2-mobile-list" style="display:none; padding: .5rem .5rem 5rem;">
  <div class="d-flex align-items-center mb-2 px-1">
    <h6 class="mb-0 font-weight-bold text-primary">
      <i class="fas fa-users mr-1"></i>Clientes
    </h6>
    <span class="badge badge-primary ml-2" id="badge-total-cli">—</span>
  </div>

  {{-- Búsqueda + ordenamiento móvil --}}
  <div class="mb-2 px-1">
    <div class="input-group input-group-sm mb-1">
      <div class="input-group-prepend">
        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
      </div>
      <input type="text" id="cli-search-mobile" class="form-control"
             placeholder="Buscar por nombre, documento, ciudad…"
             autocomplete="off">
      <div class="input-group-append">
        <button class="btn btn-outline-secondary" id="cli-search-clear" style="display:none">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    <div class="d-flex" style="gap:.35rem;flex-wrap:wrap">
      <select id="cli-sort-mobile" class="form-control form-control-sm" style="max-width:160px">
        <option value="consecutivo">Consecutivo ↑</option>
        <option value="nombres">Nombre A-Z</option>
        <option value="apellidos">Apellido A-Z</option>
        <option value="documento">Documento</option>
      </select>
      <select id="cli-filter-activo" class="form-control form-control-sm" style="max-width:110px">
        <option value="">Todos</option>
        <option value="1">Activos</option>
        <option value="0">Inactivos</option>
      </select>
      <button id="btn-editar-orden" class="btn btn-sm btn-outline-secondary" title="Editar orden de ruta">
        <i class="fas fa-sort-numeric-down mr-1"></i>Orden
      </button>
      <span class="badge badge-light align-self-center ml-auto" id="badge-filtrados-cli" style="display:none"></span>
    </div>
  </div>

  {{-- Barra guardar orden (oculta hasta activar modo edición) --}}
  <div id="orden-bar" class="d-none mb-2 px-1">
    <div class="alert alert-warning py-2 mb-1" style="font-size:.8rem;border-radius:8px">
      <i class="fas fa-info-circle mr-1"></i>
      Edita el número de orden de cada cliente y pulsa <strong>Guardar</strong>.
    </div>
    <div class="d-flex" style="gap:.5rem">
      <button id="btn-guardar-orden" class="btn btn-success btn-sm flex-grow-1">
        <i class="fas fa-save mr-1"></i>Guardar orden
      </button>
      <button id="btn-cancelar-orden" class="btn btn-secondary btn-sm">
        <i class="fas fa-times mr-1"></i>Cancelar
      </button>
    </div>
  </div>

  <div id="mobile-cards-cli"></div>
  <p id="mobile-cli-empty" class="text-muted text-center mt-3" style="display:none">
    Sin clientes registrados.
  </p>
</div>

{{-- ── FAB ──────────────────────────────────────────────────── --}}
<button type="button" id="fab-cliente"
        class="v2-fab btn-primary text-white"
        aria-label="Crear nuevo cliente">
  <i class="fas fa-plus"></i>
</button>

{{-- ══ Modal ════════════════════════════════════════════════════ --}}
<div class="modal fade ios-form" id="modal-cliente" tabindex="-1" role="dialog"
     aria-labelledby="modal-cliente-titulo" aria-modal="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h5 class="modal-title text-white" id="modal-cliente-titulo">
          <i class="fas fa-user mr-1"></i>
          <span id="modal-cliente-heading">Cliente</span>
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="form-cliente" novalidate>
        @csrf
        <input type="hidden" id="hidden_id" value="">
        <input type="hidden" id="action" value="Add">

        <div class="modal-body" style="position:relative">
          <div class="v2-loader" id="loader-cliente">
            <div class="spinner-border text-primary"></div>
          </div>
          <span id="form_result_cliente" role="alert" aria-live="polite"></span>
          @include('admin.v2.cliente.form')
        </div>

        <div class="modal-footer d-flex">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Cancelar
          </button>
          <button type="submit" id="btn-submit-cliente" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i>
            <span id="btn-submit-text">Guardar</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Modal calificación cliente --}}
<div class="modal fade" id="modal-calificacion" tabindex="-1" role="dialog" aria-modal="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title text-white">
          <i class="fas fa-star mr-1"></i>Calificación del cliente
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" id="contenido-calificacion">
        <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-1"></i>Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

{{-- Modal detalle préstamos --}}
<div class="modal fade" id="modal-detalle" tabindex="-1" role="dialog" aria-modal="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h5 class="modal-title text-white">
          <i class="fas fa-atlas mr-1"></i>Préstamos del cliente
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="contenido-detalle">
        <p class="text-muted text-center">Cargando...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-1"></i>Cerrar
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
var ES = {
    "sProcessing":"Procesando...","sLengthMenu":"Mostrar _MENU_",
    "sZeroRecords":"Sin resultados","sEmptyTable":"Sin datos",
    "sInfo":"_START_-_END_ de _TOTAL_","sInfoEmpty":"0","sInfoFiltered":"(de _MAX_)",
    "sSearch":"Buscar:","sLoadingRecords":"Cargando...",
    "oPaginate":{"sFirst":"«","sLast":"»","sNext":"›","sPrevious":"‹"}
};
var AJAX_URL = '{{ route("admin.v2.cliente.index") }}';

$(function () {
    $('.select2bs4').select2({ theme: 'bootstrap4' });

    /* ── DataTable ───────────────────────────────── */
    $('#skeleton-clientes').hide();
    $('#dt-clientes-wrap').show();
    var tabla = $('#tabla-clientes').DataTable({
        language: ES, processing:true, responsive:true,
        order:[[1,'asc']], lengthMenu:[[25,50,100,-1],[25,50,100,'Todo']],
        dom:'<"row"<"col-6"l><"col-6"f>>rt<"row"<"col-7"i><"col-5"p>>',
        ajax: { url: AJAX_URL, headers: { 'X-Requested-With': 'XMLHttpRequest' } },
        columns: [
            { data:'action',         orderable:false, searchable:false },
            { data:'consecutivo' }, { data:'nombres' },  { data:'apellidos' },
            { data:'tipo_documento'},{ data:'documento'}, { data:'telefono' },
            { data:'celular' },     { data:'direccion' }, { data:'estado' },
            { data:'pais' },        { data:'ciudad' },    { data:'barrio' },
            { data:'sector' },      { data:'activo' },    { data:'observacion_cli'},
            { data:'usuario_id' }
        ],
    });

    /* ── Cards móvil ─────────────────────────────── */
    var allClientItems = [];
    var modoOrden = false;

    function renderCards(items) {
        if (!items.length) {
            $('#mobile-cards-cli').html('');
            $('#mobile-cli-empty').show();
            $('#badge-filtrados-cli').hide();
            return;
        }
        $('#mobile-cli-empty').hide();
        var total = allClientItems.length;
        if (items.length < total) {
            $('#badge-filtrados-cli').text(items.length + ' de ' + total).show();
        } else {
            $('#badge-filtrados-cli').hide();
        }
        var html = '';
        items.forEach(function(d) {
            var consec = d.consecutivo || '';
            var consecHtml = modoOrden
                ? '<input type="number" class="consec-input form-control form-control-sm d-inline-block text-center font-weight-bold"'
                  + ' data-id="' + d.id + '" value="' + consec + '"'
                  + ' style="width:70px;border:2px solid #ffc107;border-radius:6px;padding:2px 4px"'
                  + ' min="1" max="99999">'
                : '<span><i class="fas fa-hashtag mr-1"></i>' + consec + '</span>';

            html += '<div class="v2-mcard">'
                + '<div class="v2-mcard-header bg-primary text-white" style="align-items:center">'
                + '<div style="display:flex;align-items:center;gap:.5rem">'
                + consecHtml
                + '<span>' + (d.nombres||'') + ' ' + (d.apellidos||'') + '</span>'
                + '</div>'
                + '<span class="badge ' + (d.activo==1 ? 'badge-success' : 'badge-danger') + '">' + (d.activo==1?'Activo':'Inactivo') + '</span>'
                + '</div>'
                + '<div class="v2-mcard-body">'
                + '<div><div class="v2-lbl">Documento</div><div class="v2-val">' + (d.tipo_documento||'') + ' ' + (d.documento||'') + '</div></div>'
                + '<div><div class="v2-lbl">Celular</div><div class="v2-val"><a href="tel:' + (d.celular||'') + '">' + (d.celular||'—') + '</a></div></div>'
                + '<div><div class="v2-lbl">Ciudad</div><div class="v2-val">' + (d.ciudad||'—') + '</div></div>'
                + '<div><div class="v2-lbl">Dirección</div><div class="v2-val">' + (d.direccion||'—') + '</div></div>'
                + '</div>'
                + (modoOrden ? '' :
                  '<div class="v2-mcard-footer">'
                + '<button class="btn btn-primary edit" id="' + d.id + '"><i class="far fa-edit mr-1"></i>Editar</button>'
                + '<button class="btn btn-warning prestamo" id="' + d.id + '"><i class="fas fa-plus-circle mr-1"></i>Préstamo</button>'
                + '<button class="btn btn-success detalle" id="' + d.id + '"><i class="fas fa-atlas"></i></button>'
                + '<button class="btn btn-dark calificacion" id="' + d.id + '"><i class="fas fa-star"></i></button>'
                + '<button class="btn btn-info resetpwd" id="' + d.id + '" title="Restablecer contraseña portal"><i class="fas fa-key"></i></button>'
                + '</div>')
                + '</div>';
        });
        $('#mobile-cards-cli').html(html);
    }

    function filtrarYOrdenarCards() {
        var q      = ($('#cli-search-mobile').val() || '').toLowerCase().trim();
        var orden  = $('#cli-sort-mobile').val() || 'consecutivo';
        var activo = $('#cli-filter-activo').val();

        var items = allClientItems.slice();

        if (activo !== '') {
            items = items.filter(function(d) { return String(d.activo) === activo; });
        }
        if (q) {
            items = items.filter(function(d) {
                return ((d.nombres||'') + ' ' + (d.apellidos||'') + ' ' +
                        (d.documento||'') + ' ' + (d.ciudad||'') + ' ' +
                        (d.celular||'')).toLowerCase().indexOf(q) !== -1;
            });
        }
        items.sort(function(a, b) {
            var va = String(a[orden] || '').toLowerCase();
            var vb = String(b[orden] || '').toLowerCase();
            if (orden === 'consecutivo' || orden === 'documento') {
                return (parseFloat(va)||0) - (parseFloat(vb)||0);
            }
            return va < vb ? -1 : va > vb ? 1 : 0;
        });
        renderCards(items);
    }

    function loadCards() {
        $.ajax({
            url: AJAX_URL,
            data: { draw:1, start:0, length:2000,
                    'columns[0][data]':'consecutivo','order[0][column]':0,'order[0][dir]':'asc',
                    'search[value]':'','search[regex]':false },
            dataType:'json',
            success: function(res) {
                allClientItems = res.data || [];
                $('#badge-total-cli').text(allClientItems.length);
                filtrarYOrdenarCards();
            }
        });
    }
    loadCards();

    /* ── Editar orden de consecutivo ────────────── */
    $('#btn-editar-orden').on('click', function() {
        modoOrden = true;
        $('#orden-bar').removeClass('d-none');
        $('#btn-editar-orden').addClass('active').addClass('btn-warning').removeClass('btn-outline-secondary');
        $('#cli-search-mobile').val('').prop('disabled', true);
        $('#cli-search-clear').hide();
        $('#cli-sort-mobile').val('consecutivo').prop('disabled', true);
        $('#cli-filter-activo').val('').prop('disabled', true);
        filtrarYOrdenarCards();
    });

    $('#btn-cancelar-orden').on('click', function() {
        modoOrden = false;
        $('#orden-bar').addClass('d-none');
        $('#btn-editar-orden').removeClass('active btn-warning').addClass('btn-outline-secondary');
        $('#cli-search-mobile, #cli-sort-mobile, #cli-filter-activo').prop('disabled', false);
        filtrarYOrdenarCards();
    });

    $('#btn-guardar-orden').on('click', function() {
        var cambios = [];
        $('.consec-input').each(function() {
            var id  = parseInt($(this).data('id'), 10);
            var val = parseInt($(this).val(), 10);
            if (id && val > 0) cambios.push({ id: id, consecutivo: val });
        });
        if (!cambios.length) return;

        var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Guardando...');
        var token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').first().val();

        $.ajax({
            url: '{{ url("admin/v2/cliente/reordenar") }}',
            method: 'POST',
            dataType: 'json',
            data: { _token: token, cambios: cambios },
            success: function(data) {
                if (data.success) {
                    // actualizar allClientItems con los nuevos consecutivos
                    cambios.forEach(function(c) {
                        var item = allClientItems.find(function(x) { return x.id === c.id; });
                        if (item) item.consecutivo = c.consecutivo;
                    });
                    $('#btn-cancelar-orden').trigger('click');
                    tabla.ajax.reload();
                    Swal.fire({ icon:'success', title:'Orden guardado', showConfirmButton:false, timer:1400 });
                } else {
                    Swal.fire('Error', data.error || 'No se pudo guardar.', 'error');
                }
            },
            error: function() { Swal.fire('Error', 'Error de red. Intenta de nuevo.', 'error'); },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Guardar orden');
            }
        });
    });

    /* Búsqueda en tiempo real */
    $(document).on('input', '#cli-search-mobile', function() {
        var q = $(this).val();
        $('#cli-search-clear').toggle(q.length > 0);
        filtrarYOrdenarCards();
    });
    $('#cli-search-clear').on('click', function() {
        $('#cli-search-mobile').val('');
        $(this).hide();
        filtrarYOrdenarCards();
    });
    $('#cli-sort-mobile, #cli-filter-activo').on('change', filtrarYOrdenarCards);

    /* ── Abrir modal: crear ──────────────────────── */
    $('#btn-crear-desktop, #fab-cliente').on('click', function () {
        resetForm();
        $('#modal-cliente-heading').text('Nuevo cliente');
        $('#btn-submit-text').text('Guardar');
        $('#modal-cliente').modal('show');
    });

    /* ── Editar ──────────────────────────────────── */
    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $.ajax({
            url: '{{ url("admin/v2/cliente") }}/' + id + '/editar',
            dataType:'json',
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
                $('#modal-cliente-heading').text('Editar #' + d.consecutivo);
                $('#btn-submit-text').text('Actualizar');
                $('#modal-cliente').modal('show');
            }
        });
    });

    /* ── Submit ──────────────────────────────────── */
    $('#form-cliente').on('submit', function (e) {
        e.preventDefault();
        var isEdit = ($('#action').val() === 'Edit');
        var id = $('#hidden_id').val();
        var url = isEdit ? '{{ url("admin/v2/cliente") }}/' + id : '{{ route("admin.v2.cliente.guardar") }}';
        var method = isEdit ? 'PUT' : 'POST';

        Swal.fire({ title:'¿Confirmar?', icon:'question', showCancelButton:true,
            confirmButtonText:'Aceptar', cancelButtonText:'Cancelar'
        }).then(function (res) {
            if (!res.value) return;
            loaderOn();
            $.ajax({ url:url, method:method, data:$('#form-cliente').serialize(), dataType:'json',
                success: function (data) {
                    loaderOff();
                    if (data.errors) {
                        var h='<div class="alert alert-danger"><ul>';
                        data.errors.forEach(function(e){h+='<li>'+e+'</li>';});
                        $('#form_result_cliente').html(h+'</ul></div>'); return;
                    }
                    $('#modal-cliente').modal('hide');
                    tabla.ajax.reload();
                    loadCards();
                    Swal.fire({icon:'success', title: isEdit?'Actualizado':'Creado', timer:1500, showConfirmButton:false});
                },
                error:function(){loaderOff(); Swal.fire('Error','No se pudo guardar.','error');}
            });
        });
    });

    /* ── Detalle préstamos ───────────────────────── */
    $(document).on('click', '.detalle', function () {
        var id = $(this).attr('id');
        $('#contenido-detalle').html('<p class="text-center"><i class="fas fa-spinner fa-spin"></i></p>');
        $('#modal-detalle').modal('show');
        $.ajax({ url:'{{ url("admin/v2/cliente") }}/'+id+'/detalle', dataType:'json',
            success:function(data){
                if (!data.result||!data.result.length){
                    $('#contenido-detalle').html('<p class="text-muted text-center">Sin préstamos.</p>'); return;
                }
                var h='';
                data.result.forEach(function(p){
                    h+='<div class="v2-mcard mb-2">'
                      +'<div class="v2-mcard-body">'
                      +'<div><div class="v2-lbl">Monto</div><div class="v2-val">$ '+(p.monto||0)+'</div></div>'
                      +'<div><div class="v2-lbl">Cuotas</div><div class="v2-val">'+(p.cuotas||0)+'</div></div>'
                      +'<div><div class="v2-lbl">Tipo pago</div><div class="v2-val">'+(p.tipo_pago||'—')+'</div></div>'
                      +'<div><div class="v2-lbl">Saldo</div><div class="v2-val">$ '+(p.monto_pendiente||0)+'</div></div>'
                      +'</div></div>';
                });
                $('#contenido-detalle').html(h);
            },
            error:function(){$('#contenido-detalle').html('<p class="text-danger text-center">Error.</p>');}
        });
    });

    /* ── Restablecer contraseña portal ─────────── */
    $(document).on('click', '.resetpwd', function () {
        var id = $(this).attr('id');
        Swal.fire({
            title: '¿Restablecer contraseña?',
            text: 'La contraseña quedará en los últimos 6 dígitos del documento del cliente.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, restablecer',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#17a2b8'
        }).then(function(res) {
            if (!res.value) return;
            var token = $('meta[name="csrf-token"]').attr('content')
                     || $('input[name="_token"]').first().val();
            $.ajax({
                url: '{{ url("admin/v2/cliente") }}/' + id + '/reset-password',
                method: 'POST',
                dataType: 'json',
                data: { _token: token },
                success: function(data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Contraseña restablecida',
                        html: '<div class="text-left">'
                            + '<p class="mb-1">Cliente: <strong>' + (data.nombre||'') + '</strong></p>'
                            + '<p class="mb-0">Nueva contraseña: <span class="badge badge-dark px-3 py-1"'
                            + ' style="font-size:1rem;letter-spacing:.1em">' + (data.default||'') + '</span></p>'
                            + '</div>',
                        confirmButtonText: 'Entendido'
                    });
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo restablecer la contraseña.', 'error');
                }
            });
        });
    });

    /* ── Calificación ───────────────────────────── */
    $(document).on('click', '.calificacion', function () {
        var id = $(this).attr('id');
        $('#contenido-calificacion').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        $('#modal-calificacion').modal('show');
        $.ajax({
            url: '{{ url("admin/v2/cliente") }}/' + id + '/calificacion',
            dataType: 'json',
            success: function(d) {
                var nivelClass = {A:'success', B:'primary', C:'warning', D:'danger'}[d.nivel] || 'secondary';
                var scoreColor = d.score >= 90 ? '#28a745' : d.score >= 75 ? '#007bff' : d.score >= 55 ? '#ffc107' : '#dc3545';
                var html = '<div class="mb-3">'
                    + '<h5 class="font-weight-bold mb-0">' + d.cliente + '</h5>'
                    + '<small class="text-muted">Documento: ' + d.documento + '</small>'
                    + '</div>'
                    + '<div class="d-flex align-items-center mb-3">'
                    + '<div class="mr-3 text-center" style="min-width:80px">'
                    + '<div style="font-size:2.8rem;font-weight:bold;color:' + scoreColor + ';line-height:1">' + d.score + '</div>'
                    + '<div class="text-muted" style="font-size:.7rem">/ 100</div>'
                    + '</div>'
                    + '<div class="flex-grow-1">'
                    + '<div class="progress mb-2" style="height:16px;border-radius:8px">'
                    + '<div class="progress-bar bg-' + nivelClass + '" style="width:' + d.score + '%;transition:width .6s ease"></div>'
                    + '</div>'
                    + '<span class="badge badge-' + nivelClass + ' badge-pill px-3 py-1" style="font-size:.85rem">'
                    + d.calificacion + '</span>'
                    + '</div>'
                    + '</div>'
                    + '<div class="row text-center mb-3">'
                    + '<div class="col-6 col-sm-3 mb-2"><div class="border rounded p-2 h-100"><div class="font-weight-bold text-primary h5 mb-0">' + d.total_prestamos + '</div><small class="text-muted">Préstamos</small></div></div>'
                    + '<div class="col-6 col-sm-3 mb-2"><div class="border rounded p-2 h-100"><div class="font-weight-bold text-success h5 mb-0">' + d.pagadas + '</div><small class="text-muted">Pagadas</small></div></div>'
                    + '<div class="col-6 col-sm-3 mb-2"><div class="border rounded p-2 h-100"><div class="font-weight-bold text-danger h5 mb-0">' + d.atrasadas + '</div><small class="text-muted">Atrasadas</small></div></div>'
                    + '<div class="col-6 col-sm-3 mb-2"><div class="border rounded p-2 h-100"><div class="font-weight-bold text-warning h5 mb-0">' + d.pendientes + '</div><small class="text-muted">Pendientes</small></div></div>'
                    + '</div>'
                    + '<div class="row text-center mb-3 small text-muted">'
                    + '<div class="col-6">Monto total: <strong>$ ' + Number(d.monto_total).toLocaleString() + '</strong></div>'
                    + '<div class="col-6">Pagado: <strong>$ ' + Number(d.monto_pagado).toLocaleString() + '</strong></div>'
                    + '</div>';
                if (d.prestamos && d.prestamos.length) {
                    html += '<h6 class="font-weight-bold border-bottom pb-1 mb-2"><i class="fas fa-list mr-1"></i>Historial de préstamos</h6>'
                        + '<div class="table-responsive"><table class="table table-sm table-hover mb-0">'
                        + '<thead class="thead-light"><tr>'
                        + '<th>#</th><th>Monto</th><th>Cuotas</th><th>Tipo</th><th>Fecha</th>'
                        + '<th class="text-success">Pag.</th><th class="text-danger">Atr.</th><th>Estado</th>'
                        + '</tr></thead><tbody>';
                    d.prestamos.forEach(function(p) {
                        var est = p.estado_prestamo === 'P'
                            ? '<span class="badge badge-success">Saldado</span>'
                            : p.estado_prestamo === 'A'
                            ? '<span class="badge badge-danger">Anulado</span>'
                            : '<span class="badge badge-primary">Activo</span>';
                        html += '<tr>'
                            + '<td>' + p.idp + '</td>'
                            + '<td>$' + Number(p.monto).toLocaleString() + '</td>'
                            + '<td>' + p.cuotas + '</td>'
                            + '<td>' + (p.tipo_pago || '—') + '</td>'
                            + '<td>' + (p.fecha_inicial || '—') + '</td>'
                            + '<td class="text-success font-weight-bold">' + p.dp_pagadas + '</td>'
                            + '<td class="text-danger font-weight-bold">' + p.dp_atrasadas + '</td>'
                            + '<td>' + est + '</td>'
                            + '</tr>';
                    });
                    html += '</tbody></table></div>';
                }
                $('#contenido-calificacion').html(html);
            },
            error: function() {
                $('#contenido-calificacion').html('<p class="text-danger text-center"><i class="fas fa-exclamation-triangle mr-1"></i>Error al cargar la calificación.</p>');
            }
        });
    });

    /* ── Helpers ─────────────────────────────────── */
    function resetForm(){
        $('#form-cliente')[0].reset();
        $('#form_result_cliente').html('');
        $('#hidden_id').val(''); $('#action').val('Add');
        $('.select2bs4').trigger('change');
    }
    function loaderOn(){ $('#loader-cliente').addClass('active'); $('#btn-submit-cliente').prop('disabled',true); }
    function loaderOff(){ $('#loader-cliente').removeClass('active'); $('#btn-submit-cliente').prop('disabled',false); }
});
</script>
@endsection
