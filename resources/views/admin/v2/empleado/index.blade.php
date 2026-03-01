{{-- resources/views/admin/v2/empleado/index.blade.php --}}
@extends("theme.$theme.layout")

@section('titulo')
    Empleados v2
@endsection

@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet">
<link href="{{asset("assets/$theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css")}}" rel="stylesheet">
<link href="{{asset("assets/css/select2-bootstrap.min.css")}}" rel="stylesheet">
<link href="{{asset("assets/css/select2.min.css")}}" rel="stylesheet">
<style>
.skeleton-cell {
    display: inline-block; height: 13px; border-radius: 3px;
    background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s infinite;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

.btn-v2-action {
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50px; padding: 4px 10px; font-size: 11px;
    transition: transform .15s, box-shadow .15s;
    box-shadow: 0 3px 8px rgba(0,0,0,.2);
}
.btn-v2-action:hover  { transform: translateY(-1px); box-shadow: 0 5px 12px rgba(0,0,0,.25); }

.v2-loader {
    display: none; position: absolute; inset: 0;
    background: rgba(255,255,255,.85); z-index: 10;
    place-items: center;
}
.v2-loader.active { display: grid; }

#tabla-empleados th,
#tabla-empleados td { white-space: nowrap; font-size: 12px; }
</style>
@endsection

@section('contenido')
<div class="row">
  <div class="col-12">
    <div class="card card-warning shadow-sm">

      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-id-badge mr-1" aria-hidden="true"></i>
          Empleados
        </h5>
        <div class="card-tools">
          <button type="button" id="btn-crear-empleado"
                  class="btn btn-sm btn-light"
                  aria-label="Abrir formulario para crear un nuevo empleado">
            <i class="fas fa-plus-circle mr-1" aria-hidden="true"></i>
            Crear empleado
          </button>
        </div>
      </div>

      <div class="card-body table-responsive p-2">

        {{-- Skeleton --}}
        <div id="skeleton-empleados" role="status" aria-label="Cargando empleados">
          <table class="table table-sm" aria-hidden="true">
            <thead class="thead-light">
              <tr>
                @foreach(['Acciones','Id','Nombres','Apellidos','Tipo Doc.','Documento','País','Ciudad','Barrio','Dirección','Celular','Teléfono','Empresa','Activo'] as $h)
                  <th>{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @for ($i = 0; $i < 5; $i++)
              <tr>
                @for ($j = 0; $j < 14; $j++)
                <td><span class="skeleton-cell" style="width:{{ [90,30,90,90,55,70,70,70,70,100,70,70,90,40][$j] }}px">&nbsp;</span></td>
                @endfor
              </tr>
              @endfor
            </tbody>
          </table>
        </div>

        <div id="wrapper-empleados" style="display:none">
          <table id="tabla-empleados"
                 class="table table-hover table-sm"
                 role="grid"
                 aria-label="Listado de empleados">
            <thead class="thead-light">
              <tr>
                <th>Acciones</th>
                <th>Id</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Tipo Doc.</th>
                <th>Documento</th>
                <th>País</th>
                <th>Ciudad</th>
                <th>Barrio</th>
                <th>Dirección</th>
                <th>Celular</th>
                <th>Teléfono</th>
                <th>Empresa</th>
                <th>Activo</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- ══ Modal Crear / Editar empleado ══════════════════════════════ --}}
<div class="modal fade" id="modal-empleado" tabindex="-1" role="dialog"
     aria-labelledby="modal-empleado-titulo" aria-modal="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title text-white" id="modal-empleado-titulo">
          <i class="fas fa-id-badge mr-1" aria-hidden="true"></i>
          <span id="modal-empleado-heading">Empleado</span>
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="form-empleado" novalidate>
        @csrf
        <input type="hidden" id="hidden_id_emp" value="">
        <input type="hidden" id="action_emp" value="Add">

        <div class="modal-body" style="position:relative">
          <div class="v2-loader" id="loader-empleado" role="status" aria-label="Procesando">
            <div class="spinner-border text-warning"></div>
          </div>
          <span id="form_result_empleado" role="alert" aria-live="polite"></span>

          @include('admin.v2.empleado.form')
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Cancelar
          </button>
          <button type="submit" id="btn-submit-empleado" class="btn btn-warning btn-sm text-white">
            <i class="fas fa-save mr-1"></i>
            <span id="btn-submit-emp-text">Guardar</span>
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
<script src="{{asset("assets/$theme/plugins/select2/js/select2.full.min.js")}}"></script>

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
    $('.select2bs4').select2({ theme: 'bootstrap4' });

    var tabla = $('#tabla-empleados').DataTable({
        language: idioma_espanol,
        processing: true, serverSide: true, responsive: true,
        order: [[1, 'asc']],
        lengthMenu: [[25, 50, 100, -1],[25, 50, 100, 'Todo']],
        dom: '<"row"<"col-md-4"l><"col-md-5"f><"col-md-3"B>>rt<"row"<"col-md-8"i><"col-md-4"p>>',
        buttons: [
            { extend:'copyHtml5',  className:'btn btn-outline-primary btn-sm',   titleAttr:'Copiar' },
            { extend:'excelHtml5', className:'btn btn-outline-success btn-sm',   titleAttr:'Excel', title:'empleados' },
            { extend:'csvHtml5',   className:'btn btn-outline-warning btn-sm',   titleAttr:'CSV' },
            { extend:'pdfHtml5',   className:'btn btn-outline-secondary btn-sm', titleAttr:'PDF', title:'empleados' }
        ],
        ajax: { url: '{{ route("admin.v2.empleado.index") }}' },
        columns: [
            { data:'action',          name:'action',          orderable:false, searchable:false },
            { data:'ide',             name:'ide' },
            { data:'nombres',         name:'nombres' },
            { data:'apellidos',       name:'apellidos' },
            { data:'tipo_documento',  name:'tipo_documento' },
            { data:'documento',       name:'documento' },
            { data:'pais',            name:'pais' },
            { data:'ciudad',          name:'ciudad' },
            { data:'barrio',          name:'barrio' },
            { data:'direccion',       name:'direccion' },
            { data:'celular',         name:'celular' },
            { data:'telefono',        name:'telefono' },
            { data:'empresa_nombre',  name:'empresa_nombre' },
            { data:'activo',          name:'activo' }
        ],
        initComplete: function () {
            $('#skeleton-empleados').hide();
            $('#wrapper-empleados').show();
        }
    });

    /* ── Crear ─────────────────────────────────────────────── */
    $('#btn-crear-empleado').on('click', function () {
        resetForm();
        $('#modal-empleado-heading').text('Nuevo empleado');
        $('#btn-submit-emp-text').text('Guardar');
        $('#modal-empleado').modal('show');
    });

    /* ── Editar ────────────────────────────────────────────── */
    $(document).on('click', '.edit', function () {
        var id = $(this).attr('id');
        $.ajax({
            url: '{{ url("admin/v2/empleado") }}/' + id + '/editar',
            dataType: 'json',
            success: function (data) {
                var d = data.result;
                $('#nombres_emp').val(d.nombres);
                $('#apellidos_emp').val(d.apellidos);
                $('#tipo_documento_emp').val(d.tipo_documento).trigger('change');
                $('#documento_emp').val(d.documento);
                $('#pais_emp').val(d.pais);
                $('#ciudad_emp').val(d.ciudad);
                $('#barrio_emp').val(d.barrio);
                $('#direccion_emp').val(d.direccion);
                $('#celular_emp').val(d.celular);
                $('#telefono_emp').val(d.telefono);
                $('#empresa_id_emp').val(d.empresa_id).trigger('change');
                $('#activo_emp').val(d.activo).trigger('change');
                $('#hidden_id_emp').val(id);
                $('#action_emp').val('Edit');
                $('#modal-empleado-heading').text('Editar empleado #' + id);
                $('#btn-submit-emp-text').text('Actualizar');
                $('#modal-empleado').modal('show');
            }
        });
    });

    /* ── Submit ────────────────────────────────────────────── */
    $('#form-empleado').on('submit', function (e) {
        e.preventDefault();
        var isEdit = ($('#action_emp').val() === 'Edit');
        var id     = $('#hidden_id_emp').val();
        var url    = isEdit ? '{{ url("admin/v2/empleado") }}/' + id : '{{ route("admin.v2.empleado.guardar") }}';
        var method = isEdit ? 'PUT' : 'POST';

        Swal.fire({
            title:'¿Confirmar?',
            text: isEdit ? 'Actualizarás el empleado' : 'Crearás un nuevo empleado',
            icon:'question', showCancelButton:true,
            confirmButtonText:'Aceptar', cancelButtonText:'Cancelar'
        }).then(function (res) {
            if (!res.value) return;
            loaderOn();
            $.ajax({
                url:url, method:method,
                data:$('#form-empleado').serialize(),
                dataType:'json',
                success: function (data) {
                    loaderOff();
                    if (data.errors) {
                        var html = '<div class="alert alert-danger alert-dismissible"><button class="close" data-dismiss="alert">&times;</button><ul>';
                        data.errors.forEach(function(e){ html += '<li>'+e+'</li>'; });
                        $('#form_result_empleado').html(html + '</ul></div>');
                        return;
                    }
                    $('#modal-empleado').modal('hide');
                    tabla.ajax.reload();
                    Swal.fire({ icon:'success', title: isEdit ? 'Empleado actualizado' : 'Empleado creado', timer:1500, showConfirmButton:false });
                },
                error: function () { loaderOff(); Swal.fire('Error','No se pudo guardar.','error'); }
            });
        });
    });

    /* ── Helpers ───────────────────────────────────────────── */
    function resetForm() {
        $('#form-empleado')[0].reset();
        $('#form_result_empleado').html('');
        $('#hidden_id_emp').val('');
        $('#action_emp').val('Add');
        $('.select2bs4').trigger('change');
    }
    function loaderOn()  { $('#loader-empleado').addClass('active');    $('#btn-submit-empleado').prop('disabled',true); }
    function loaderOff() { $('#loader-empleado').removeClass('active'); $('#btn-submit-empleado').prop('disabled',false); }
});
</script>
@endsection
