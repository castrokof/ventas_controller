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
<div class="modal fade" id="modal-cliente" tabindex="-1" role="dialog"
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
    var tabla = $('#tabla-clientes').DataTable({
        language: ES, processing:true, serverSide:true, responsive:true,
        order:[[1,'asc']], lengthMenu:[[25,50,100,-1],[25,50,100,'Todo']],
        dom:'<"row"<"col-6"l><"col-6"f>>rt<"row"<"col-7"i><"col-5"p>>',
        ajax: { url: AJAX_URL },
        columns: [
            { data:'action',         orderable:false, searchable:false },
            { data:'consecutivo' }, { data:'nombres' },  { data:'apellidos' },
            { data:'tipo_documento'},{ data:'documento'}, { data:'telefono' },
            { data:'celular' },     { data:'direccion' }, { data:'estado' },
            { data:'pais' },        { data:'ciudad' },    { data:'barrio' },
            { data:'sector' },      { data:'activo' },    { data:'observacion_cli'},
            { data:'usuario_id' }
        ],
        initComplete: function () {
            $('#skeleton-clientes').hide();
            $('#dt-clientes-wrap').show();
        }
    });

    /* ── Cards móvil ─────────────────────────────── */
    function loadCards() {
        $.ajax({
            url: AJAX_URL,
            data: { draw:1, start:0, length:500,
                    'columns[0][data]':'consecutivo','order[0][column]':0,'order[0][dir]':'asc',
                    'search[value]':'','search[regex]':false },
            dataType:'json',
            success: function(res) {
                var items = res.data || [];
                $('#badge-total-cli').text(items.length);
                if (!items.length) { $('#mobile-cli-empty').show(); return; }
                var html = '';
                items.forEach(function(d) {
                    html += '<div class="v2-mcard">'
                        + '<div class="v2-mcard-header bg-primary text-white">'
                        + '<span><i class="fas fa-hashtag mr-1"></i>' + (d.consecutivo||'') + ' — ' + (d.nombres||'') + ' ' + (d.apellidos||'') + '</span>'
                        + '<span class="badge ' + (d.activo==1 ? 'badge-success' : 'badge-danger') + '">' + (d.activo==1?'Activo':'Inactivo') + '</span>'
                        + '</div>'
                        + '<div class="v2-mcard-body">'
                        + '<div><div class="v2-lbl">Documento</div><div class="v2-val">' + (d.tipo_documento||'') + ' ' + (d.documento||'') + '</div></div>'
                        + '<div><div class="v2-lbl">Celular</div><div class="v2-val"><a href="tel:' + (d.celular||'') + '">' + (d.celular||'—') + '</a></div></div>'
                        + '<div><div class="v2-lbl">Ciudad</div><div class="v2-val">' + (d.ciudad||'—') + '</div></div>'
                        + '<div><div class="v2-lbl">Dirección</div><div class="v2-val">' + (d.direccion||'—') + '</div></div>'
                        + '</div>'
                        + '<div class="v2-mcard-footer">'
                        + '<button class="btn btn-primary edit" id="' + d.id + '"><i class="far fa-edit mr-1"></i>Editar</button>'
                        + '<button class="btn btn-warning prestamo" id="' + d.id + '"><i class="fas fa-plus-circle mr-1"></i>Préstamo</button>'
                        + '<button class="btn btn-success detalle" id="' + d.id + '"><i class="fas fa-atlas"></i></button>'
                        + '</div></div>';
                });
                $('#mobile-cards-cli').html(html);
            }
        });
    }
    loadCards();

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
