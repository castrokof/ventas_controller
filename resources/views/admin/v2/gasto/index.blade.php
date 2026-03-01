{{-- resources/views/admin/v2/gasto/index.blade.php --}}
@extends("theme.$theme.layout")

@section('titulo')
    Gastos v2
@endsection

@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet">
<link href="{{asset("assets/$theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css")}}" rel="stylesheet">
<style>
.skeleton-cell {
    display: inline-block; height: 13px; border-radius: 3px;
    background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

.btn-v2-action {
    display: inline-flex; align-items: center;
    border-radius: 50px; padding: 4px 10px; font-size: 11px;
    transition: transform .15s, box-shadow .15s;
    box-shadow: 0 3px 8px rgba(0,0,0,.2);
}
.btn-v2-action:hover { transform: translateY(-1px); box-shadow: 0 5px 12px rgba(0,0,0,.25); }

.v2-loader {
    display: none; position: absolute; inset: 0;
    background: rgba(255,255,255,.85); z-index: 10;
    place-items: center;
}
.v2-loader.active { display: grid; }

#tabla-gastos th,
#tabla-gastos td { white-space: nowrap; font-size: 12px; }
</style>
@endsection

@section('contenido')

{{-- ── Info-box resumen ──────────────────────────────────────────── --}}
<div class="row mb-3">
  <div class="col-12 col-md-4">
    <div class="info-box bg-danger">
      <span class="info-box-icon"><i class="fas fa-money-bill-wave" aria-hidden="true"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total gastos registrados</span>
        <span class="info-box-number" id="total-gastos">—</span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card card-danger shadow-sm">

      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-receipt mr-1" aria-hidden="true"></i>
          Gastos
        </h5>
        <div class="card-tools">
          <button type="button" id="btn-crear-gasto"
                  class="btn btn-sm btn-light"
                  aria-label="Crear un nuevo gasto">
            <i class="fas fa-plus-circle mr-1" aria-hidden="true"></i>
            Nuevo gasto
          </button>
        </div>
      </div>

      <div class="card-body table-responsive p-2">

        {{-- Skeleton --}}
        <div id="skeleton-gastos" role="status" aria-label="Cargando gastos">
          <table class="table table-sm" aria-hidden="true">
            <thead class="thead-light">
              <tr>
                @foreach(['Acciones','Id','Monto','Descripción','Fecha creación'] as $h)
                  <th>{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @for ($i = 0; $i < 5; $i++)
              <tr>
                @foreach([70,30,60,180,120] as $w)
                <td><span class="skeleton-cell" style="width:{{$w}}px">&nbsp;</span></td>
                @endforeach
              </tr>
              @endfor
            </tbody>
          </table>
        </div>

        <div id="wrapper-gastos" style="display:none">
          <table id="tabla-gastos"
                 class="table table-hover table-sm"
                 role="grid"
                 aria-label="Listado de gastos">
            <thead class="thead-light">
              <tr>
                <th>Acciones</th>
                <th>Id</th>
                <th>Monto</th>
                <th>Descripción</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- ══ Modal Crear / Editar gasto ══════════════════════════════════ --}}
<div class="modal fade" id="modal-gasto" tabindex="-1" role="dialog"
     aria-labelledby="modal-gasto-titulo" aria-modal="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h5 class="modal-title text-white" id="modal-gasto-titulo">
          <i class="fas fa-receipt mr-1" aria-hidden="true"></i>
          <span id="modal-gasto-heading">Gasto</span>
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="form-gasto" novalidate>
        @csrf
        <input type="hidden" id="hidden_id_gasto" value="">
        <input type="hidden" id="action_gasto" value="Add">

        <div class="modal-body" style="position:relative">
          <div class="v2-loader" id="loader-gasto" role="status" aria-label="Procesando">
            <div class="spinner-border text-danger"></div>
          </div>
          <span id="form_result_gasto" role="alert" aria-live="polite"></span>

          @include('admin.v2.gasto.form')
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Cancelar
          </button>
          <button type="submit" id="btn-submit-gasto" class="btn btn-danger btn-sm">
            <i class="fas fa-save mr-1"></i>
            <span id="btn-submit-gasto-text">Guardar</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section("scriptsPlugins")
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/datatables-responsive/js/dataTables.responsive.min.js")}}"></script>

<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script>
var idioma_espanol = {
    "sProcessing":"Procesando...","sLengthMenu":"Mostrar _MENU_ registros",
    "sZeroRecords":"Sin resultados","sEmptyTable":"Sin datos",
    "sInfo":"Del _START_ al _END_ de _TOTAL_","sInfoEmpty":"0 al 0 de 0",
    "sInfoFiltered":"(filtrado de _MAX_)","sSearch":"Buscar:",
    "sLoadingRecords":"Cargando...",
    "oPaginate":{"sFirst":"Primero","sLast":"Último","sNext":"Siguiente","sPrevious":"Anterior"}
};

$(function () {

    var tabla = $('#tabla-gastos').DataTable({
        language: idioma_espanol,
        processing: true, serverSide: true, responsive: true,
        order: [[1, 'desc']],
        lengthMenu: [[25, 50, 100, -1],[25, 50, 100, 'Todo']],
        dom: '<"row"<"col-md-4"l><"col-md-5"f><"col-md-3"B>>rt<"row"<"col-md-8"i><"col-md-4"p>>',
        buttons: [
            { extend:'copyHtml5',  className:'btn btn-outline-primary btn-sm',   titleAttr:'Copiar' },
            { extend:'excelHtml5', className:'btn btn-outline-success btn-sm',   titleAttr:'Excel', title:'gastos' },
            { extend:'csvHtml5',   className:'btn btn-outline-warning btn-sm',   titleAttr:'CSV' },
            { extend:'pdfHtml5',   className:'btn btn-outline-secondary btn-sm', titleAttr:'PDF', title:'gastos' }
        ],
        ajax: {
            url: '{{ route("admin.v2.gasto.index") }}',
            dataSrc: function (json) {
                // Calcular total sumando montos
                if (json.data && json.data.length) {
                    var total = json.data.reduce(function (s, r) { return s + parseFloat(r.monto || 0); }, 0);
                    $('#total-gastos').text('$ ' + total.toLocaleString('es-CO'));
                }
                return json.data;
            }
        },
        columns: [
            { data:'action',      name:'action',      orderable:false, searchable:false },
            { data:'id',          name:'id' },
            { data:'monto',       name:'monto',       render: function(v){ return '$ '+parseFloat(v||0).toLocaleString('es-CO'); } },
            { data:'descripcion', name:'descripcion', className:'text-wrap' },
            { data:'created_at',  name:'created_at' }
        ],
        initComplete: function () {
            $('#skeleton-gastos').hide();
            $('#wrapper-gastos').show();
        }
    });

    /* ── Crear ─────────────────────────────────────────────── */
    $('#btn-crear-gasto').on('click', function () {
        resetForm();
        $('#modal-gasto-heading').text('Nuevo gasto');
        $('#btn-submit-gasto-text').text('Guardar');
        $('#modal-gasto').modal('show');
    });

    /* ── Editar ────────────────────────────────────────────── */
    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $.ajax({
            url: '{{ url("admin/v2/gasto") }}/' + id + '/editar',
            dataType: 'json',
            success: function (data) {
                var d = data.result;
                $('#monto_gasto').val(d.monto);
                $('#descripcion_gasto').val(d.descripcion);
                $('#hidden_id_gasto').val(d.id || id);
                $('#action_gasto').val('Edit');
                $('#modal-gasto-heading').text('Editar gasto #' + id);
                $('#btn-submit-gasto-text').text('Actualizar');
                $('#modal-gasto').modal('show');
            }
        });
    });

    /* ── Submit ────────────────────────────────────────────── */
    $('#form-gasto').on('submit', function (e) {
        e.preventDefault();
        var isEdit = ($('#action_gasto').val() === 'Edit');
        var id     = $('#hidden_id_gasto').val();
        var url    = isEdit ? '{{ url("admin/v2/gasto") }}/' + id : '{{ route("admin.v2.gasto.guardar") }}';
        var method = isEdit ? 'PUT' : 'POST';

        Swal.fire({
            title:'¿Confirmar?',
            text: isEdit ? 'Actualizarás el gasto' : 'Registrarás un nuevo gasto',
            icon:'question', showCancelButton:true,
            confirmButtonText:'Aceptar', cancelButtonText:'Cancelar'
        }).then(function (res) {
            if (!res.value) return;
            loaderOn();
            $.ajax({
                url:url, method:method,
                data:$('#form-gasto').serialize(),
                dataType:'json',
                success: function (data) {
                    loaderOff();
                    if (data.errors) {
                        var html = '<div class="alert alert-danger alert-dismissible"><button class="close" data-dismiss="alert">&times;</button><ul>';
                        data.errors.forEach(function(e){ html += '<li>'+e+'</li>'; });
                        $('#form_result_gasto').html(html + '</ul></div>');
                        return;
                    }
                    $('#modal-gasto').modal('hide');
                    tabla.ajax.reload();
                    Swal.fire({ icon:'success', title: isEdit ? 'Gasto actualizado' : 'Gasto registrado', timer:1500, showConfirmButton:false });
                },
                error: function () { loaderOff(); Swal.fire('Error','No se pudo guardar.','error'); }
            });
        });
    });

    /* ── Helpers ───────────────────────────────────────────── */
    function resetForm() {
        $('#form-gasto')[0].reset();
        $('#form_result_gasto').html('');
        $('#hidden_id_gasto').val('');
        $('#action_gasto').val('Add');
    }
    function loaderOn()  { $('#loader-gasto').addClass('active');    $('#btn-submit-gasto').prop('disabled',true); }
    function loaderOff() { $('#loader-gasto').removeClass('active'); $('#btn-submit-gasto').prop('disabled',false); }
});
</script>
@endsection
