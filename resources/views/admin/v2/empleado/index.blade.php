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
@include('admin.v2._partials.mobile-styles')
@endsection

@section('contenido')

{{-- ── Tabla desktop ────────────────────────────────────────── --}}
<div class="row v2-dt-wrapper">
  <div class="col-12">
    <div class="card card-warning shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-id-badge mr-1"></i> Empleados
        </h5>
        <div class="card-tools">
          <button type="button" id="btn-crear-desktop"
                  class="btn btn-sm btn-light">
            <i class="fas fa-plus-circle mr-1"></i>Crear
          </button>
        </div>
      </div>
      <div class="card-body table-responsive p-2">
        <div id="skeleton-empleados">
          <table class="table table-sm" aria-hidden="true">
            <thead class="thead-light">
              <tr>
                @foreach(['Acc.','Id','Nombres','Apellidos','Doc.','País','Ciudad','Empresa','Activo'] as $h)
                  <th>{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @for($i=0;$i<5;$i++)
              <tr>
                @foreach([90,30,90,90,70,70,70,90,40] as $w)
                <td><span class="skeleton-cell" style="width:{{$w}}px">&nbsp;</span></td>
                @endforeach
              </tr>
              @endfor
            </tbody>
          </table>
        </div>
        <div id="dt-emp-wrap" style="display:none">
          <table id="tabla-empleados" class="table table-hover table-sm" role="grid">
            <thead class="thead-light">
              <tr>
                <th>Acciones</th><th>Id</th><th>Nombres</th><th>Apellidos</th>
                <th>Tipo Doc.</th><th>Documento</th><th>País</th><th>Ciudad</th>
                <th>Barrio</th><th>Dirección</th><th>Celular</th><th>Teléfono</th>
                <th>Empresa</th><th>Activo</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── Cards móvil ──────────────────────────────────────────── --}}
<div class="v2-mobile-list" style="display:none; padding:.5rem .5rem 5rem;">
  <div class="d-flex align-items-center mb-2 px-1">
    <h6 class="mb-0 font-weight-bold" style="color:#856404">
      <i class="fas fa-id-badge mr-1"></i>Empleados
    </h6>
    <span class="badge badge-warning ml-2" id="badge-total-emp">—</span>
  </div>
  <div id="mobile-cards-emp"></div>
  <p id="mobile-emp-empty" class="text-muted text-center mt-3" style="display:none">
    Sin empleados registrados.
  </p>
</div>

{{-- ── FAB ──────────────────────────────────────────────────── --}}
<button type="button" id="fab-empleado"
        class="v2-fab text-white"
        style="background:#ffc107;"
        aria-label="Crear empleado">
  <i class="fas fa-plus"></i>
</button>

{{-- ══ Modal ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-empleado" tabindex="-1" role="dialog" aria-modal="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title text-white">
          <i class="fas fa-id-badge mr-1"></i>
          <span id="modal-emp-heading">Empleado</span>
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="form-empleado" novalidate>
        @csrf
        <input type="hidden" id="hidden_id_emp" value="">
        <input type="hidden" id="action_emp" value="Add">

        <div class="modal-body" style="position:relative">
          <div class="v2-loader" id="loader-empleado">
            <div class="spinner-border text-warning"></div>
          </div>
          <span id="form_result_emp" role="alert" aria-live="polite"></span>
          @include('admin.v2.empleado.form')
        </div>

        <div class="modal-footer d-flex">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Cancelar
          </button>
          <button type="submit" id="btn-submit-emp" class="btn btn-warning text-white">
            <i class="fas fa-save mr-1"></i>
            <span id="btn-emp-text">Guardar</span>
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
var ES = {
    "sProcessing":"Procesando...","sLengthMenu":"Mostrar _MENU_",
    "sZeroRecords":"Sin resultados","sEmptyTable":"Sin datos",
    "sInfo":"_START_-_END_ de _TOTAL_","sInfoEmpty":"0","sInfoFiltered":"(de _MAX_)",
    "sSearch":"Buscar:","sLoadingRecords":"Cargando...",
    "oPaginate":{"sFirst":"«","sLast":"»","sNext":"›","sPrevious":"‹"}
};
var AJAX_URL = '{{ route("admin.v2.empleado.index") }}';

$(function () {
    $('.select2bs4').select2({ theme:'bootstrap4' });

    /* ── DataTable ───────────────────────────────── */
    var tabla = $('#tabla-empleados').DataTable({
        language:ES, processing:true, serverSide:true, responsive:true,
        order:[[1,'asc']], lengthMenu:[[25,50,100,-1],[25,50,100,'Todo']],
        dom:'<"row"<"col-6"l><"col-6"f>>rt<"row"<"col-7"i><"col-5"p>>',
        ajax:{ url:AJAX_URL },
        columns:[
            {data:'action', orderable:false, searchable:false},
            {data:'ide'},{data:'nombres'},{data:'apellidos'},
            {data:'tipo_documento'},{data:'documento'},{data:'pais'},
            {data:'ciudad'},{data:'barrio'},{data:'direccion'},
            {data:'celular'},{data:'telefono'},{data:'empresa_nombre'},{data:'activo'}
        ],
        initComplete:function(){$('#skeleton-empleados').hide();$('#dt-emp-wrap').show();}
    });

    /* ── Cards móvil ─────────────────────────────── */
    function loadCards() {
        $.ajax({
            url:AJAX_URL,
            data:{draw:1,start:0,length:500,
                  'columns[0][data]':'ide','order[0][column]':0,'order[0][dir]':'asc',
                  'search[value]':'','search[regex]':false},
            dataType:'json',
            success:function(res){
                var items = res.data||[];
                $('#badge-total-emp').text(items.length);
                if (!items.length){$('#mobile-emp-empty').show();return;}
                var h='';
                items.forEach(function(d){
                    h+='<div class="v2-mcard">'
                      +'<div class="v2-mcard-header" style="background:#ffc107;color:#212529;">'
                      +'<span><i class="fas fa-id-badge mr-1"></i>'+d.ide+' — '+d.nombres+' '+d.apellidos+'</span>'
                      +'<span class="badge '+(d.activo==1?'badge-success':'badge-danger')+'">'+(d.activo==1?'Activo':'Inactivo')+'</span>'
                      +'</div>'
                      +'<div class="v2-mcard-body">'
                      +'<div><div class="v2-lbl">Documento</div><div class="v2-val">'+d.tipo_documento+' '+d.documento+'</div></div>'
                      +'<div><div class="v2-lbl">Empresa</div><div class="v2-val">'+(d.empresa_nombre||'—')+'</div></div>'
                      +'<div><div class="v2-lbl">Ciudad</div><div class="v2-val">'+(d.ciudad||'—')+'</div></div>'
                      +'<div><div class="v2-lbl">Celular</div><div class="v2-val"><a href="tel:'+(d.celular||'')+'">'+( d.celular||'—')+'</a></div></div>'
                      +'</div>'
                      +'<div class="v2-mcard-footer">'
                      +'<button class="btn btn-primary edit" id="'+d.ide+'"><i class="far fa-edit mr-1"></i>Editar</button>'
                      +'<button class="btn btn-warning clientes" id="'+d.ide+'"><i class="fas fa-users mr-1"></i>Clientes</button>'
                      +'</div></div>';
                });
                $('#mobile-cards-emp').html(h);
            }
        });
    }
    loadCards();

    /* ── Crear ──────────────────────────────────── */
    $('#btn-crear-desktop, #fab-empleado').on('click',function(){
        resetForm();
        $('#modal-emp-heading').text('Nuevo empleado');
        $('#btn-emp-text').text('Guardar');
        $('#modal-empleado').modal('show');
    });

    /* ── Editar ─────────────────────────────────── */
    $(document).on('click','.edit',function(){
        var id=$(this).attr('id');
        $.ajax({url:'{{ url("admin/v2/empleado") }}/'+id+'/editar',dataType:'json',
            success:function(data){
                var d=data.result;
                $('#nombres_emp').val(d.nombres);$('#apellidos_emp').val(d.apellidos);
                $('#tipo_documento_emp').val(d.tipo_documento).trigger('change');
                $('#documento_emp').val(d.documento);$('#pais_emp').val(d.pais);
                $('#ciudad_emp').val(d.ciudad);$('#barrio_emp').val(d.barrio);
                $('#direccion_emp').val(d.direccion);$('#celular_emp').val(d.celular);
                $('#telefono_emp').val(d.telefono);
                $('#empresa_id_emp').val(d.empresa_id).trigger('change');
                $('#activo_emp').val(d.activo).trigger('change');
                $('#hidden_id_emp').val(id);$('#action_emp').val('Edit');
                $('#modal-emp-heading').text('Editar #'+id);
                $('#btn-emp-text').text('Actualizar');
                $('#modal-empleado').modal('show');
            }
        });
    });

    /* ── Submit ─────────────────────────────────── */
    $('#form-empleado').on('submit',function(e){
        e.preventDefault();
        var isEdit=($('#action_emp').val()==='Edit');
        var id=$('#hidden_id_emp').val();
        var url=isEdit?'{{ url("admin/v2/empleado") }}/'+id:'{{ route("admin.v2.empleado.guardar") }}';

        Swal.fire({title:'¿Confirmar?',icon:'question',showCancelButton:true,
            confirmButtonText:'Aceptar',cancelButtonText:'Cancelar'
        }).then(function(res){
            if(!res.value)return;
            loaderOn();
            $.ajax({url:url,method:isEdit?'PUT':'POST',
                data:$('#form-empleado').serialize(),dataType:'json',
                success:function(data){
                    loaderOff();
                    if(data.errors){
                        var h='<div class="alert alert-danger"><ul>';
                        data.errors.forEach(function(e){h+='<li>'+e+'</li>';});
                        $('#form_result_emp').html(h+'</ul></div>');return;
                    }
                    $('#modal-empleado').modal('hide');
                    tabla.ajax.reload(); loadCards();
                    Swal.fire({icon:'success',title:isEdit?'Actualizado':'Creado',timer:1500,showConfirmButton:false});
                },
                error:function(){loaderOff();Swal.fire('Error','No se pudo guardar.','error');}
            });
        });
    });

    /* ── Helpers ─────────────────────────────────── */
    function resetForm(){
        $('#form-empleado')[0].reset();
        $('#form_result_emp').html('');
        $('#hidden_id_emp').val('');$('#action_emp').val('Add');
        $('.select2bs4').trigger('change');
    }
    function loaderOn(){$('#loader-empleado').addClass('active');$('#btn-submit-emp').prop('disabled',true);}
    function loaderOff(){$('#loader-empleado').removeClass('active');$('#btn-submit-emp').prop('disabled',false);}
});
</script>
@endsection
