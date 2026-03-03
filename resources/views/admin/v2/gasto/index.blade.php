{{-- resources/views/admin/v2/gasto/index.blade.php --}}
@extends("theme.$theme.layout")

@section('titulo')
    Gastos v2
@endsection

@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet">
<link href="{{asset("assets/$theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css")}}" rel="stylesheet">
@include('admin.v2._partials.mobile-styles')
@endsection

@section('contenido')

{{-- ── Info-box total ───────────────────────────────────────── --}}
<div class="row mb-2">
  <div class="col-12 col-sm-5 col-md-4">
    <div class="info-box bg-danger">
      <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total gastos</span>
        <span class="info-box-number" id="total-gastos">—</span>
      </div>
    </div>
  </div>
</div>

{{-- ── Tabla desktop ────────────────────────────────────────── --}}
<div class="row v2-dt-wrapper">
  <div class="col-12">
    <div class="card card-danger shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-receipt mr-1"></i> Gastos
        </h5>
        <div class="card-tools">
          <button type="button" id="btn-crear-desktop"
                  class="btn btn-sm btn-light">
            <i class="fas fa-plus-circle mr-1"></i>Nuevo
          </button>
        </div>
      </div>
      <div class="card-body table-responsive p-2">
        <div id="skeleton-gastos">
          <table class="table table-sm" aria-hidden="true">
            <thead class="thead-light">
              <tr>
                @foreach(['Acc.','Id','Monto','Descripción','Fecha'] as $h)
                  <th>{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @for($i=0;$i<5;$i++)
              <tr>
                @foreach([70,30,60,180,120] as $w)
                <td><span class="skeleton-cell" style="width:{{$w}}px">&nbsp;</span></td>
                @endforeach
              </tr>
              @endfor
            </tbody>
          </table>
        </div>
        <div id="dt-gasto-wrap" style="display:none">
          <table id="tabla-gastos" class="table table-hover table-sm" role="grid">
            <thead class="thead-light">
              <tr>
                <th>Acciones</th><th>Id</th><th>Monto</th>
                <th>Descripción</th><th>Fecha</th>
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
    <h6 class="mb-0 font-weight-bold text-danger">
      <i class="fas fa-receipt mr-1"></i>Gastos
    </h6>
    <span class="badge badge-danger ml-2" id="badge-total-gasto">—</span>
  </div>
  <div id="mobile-cards-gasto"></div>
  <p id="mobile-gasto-empty" class="text-muted text-center mt-3" style="display:none">
    Sin gastos registrados.
  </p>
</div>

{{-- ── FAB ──────────────────────────────────────────────────── --}}
<button type="button" id="fab-gasto"
        class="v2-fab btn-danger text-white"
        aria-label="Registrar gasto">
  <i class="fas fa-plus"></i>
</button>

{{-- ══ Modal ════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modal-gasto" tabindex="-1" role="dialog" aria-modal="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h5 class="modal-title text-white">
          <i class="fas fa-receipt mr-1"></i>
          <span id="modal-gasto-heading">Gasto</span>
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="form-gasto" novalidate>
        @csrf
        <input type="hidden" id="hidden_id_gasto" value="">
        <input type="hidden" id="action_gasto" value="Add">

        <div class="modal-body" style="position:relative">
          <div class="v2-loader" id="loader-gasto">
            <div class="spinner-border text-danger"></div>
          </div>
          <span id="form_result_gasto" role="alert" aria-live="polite"></span>
          @include('admin.v2.gasto.form')
        </div>

        <div class="modal-footer d-flex">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>Cancelar
          </button>
          <button type="submit" id="btn-submit-gasto" class="btn btn-danger">
            <i class="fas fa-save mr-1"></i>
            <span id="btn-gasto-text">Guardar</span>
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
var ES={
    "sProcessing":"Procesando...","sLengthMenu":"Mostrar _MENU_",
    "sZeroRecords":"Sin resultados","sEmptyTable":"Sin datos",
    "sInfo":"_START_-_END_ de _TOTAL_","sInfoEmpty":"0","sInfoFiltered":"(de _MAX_)",
    "sSearch":"Buscar:","sLoadingRecords":"Cargando...",
    "oPaginate":{"sFirst":"«","sLast":"»","sNext":"›","sPrevious":"‹"}
};
var AJAX_URL = '{{ route("admin.v2.gasto.index") }}';

function fmtMoney(v){ return '$ '+parseFloat(v||0).toLocaleString('es-CO'); }

$(function(){

    /* ── DataTable ─────────────────────────────── */
    var tabla = $('#tabla-gastos').DataTable({
        language:ES, processing:true, serverSide:true, responsive:true,
        order:[[1,'desc']], lengthMenu:[[25,50,100,-1],[25,50,100,'Todo']],
        dom:'<"row"<"col-6"l><"col-6"f>>rt<"row"<"col-7"i><"col-5"p>>',
        ajax:{
            url:AJAX_URL,
            dataSrc:function(json){
                if(json.data&&json.data.length){
                    var t=json.data.reduce(function(s,r){return s+parseFloat(r.monto||0);},0);
                    $('#total-gastos').text(fmtMoney(t));
                }
                return json.data;
            }
        },
        columns:[
            {data:'action',orderable:false,searchable:false},
            {data:'id'},{data:'monto',render:function(v){return fmtMoney(v);}},
            {data:'descripcion',className:'text-wrap'},{data:'created_at'}
        ],
        initComplete:function(){$('#skeleton-gastos').hide();$('#dt-gasto-wrap').show();}
    });

    /* ── Cards móvil ─────────────────────────────── */
    function loadCards(){
        $.ajax({
            url:AJAX_URL,
            data:{draw:1,start:0,length:500,
                  'columns[0][data]':'id','order[0][column]':0,'order[0][dir]':'desc',
                  'search[value]':'','search[regex]':false},
            dataType:'json',
            success:function(res){
                var items=res.data||[];
                var total=items.reduce(function(s,r){return s+parseFloat(r.monto||0);},0);
                $('#total-gastos').text(fmtMoney(total));
                $('#badge-total-gasto').text(items.length);
                if(!items.length){$('#mobile-gasto-empty').show();return;}
                var h='';
                items.forEach(function(d){
                    h+='<div class="v2-mcard">'
                      +'<div class="v2-mcard-header bg-danger text-white">'
                      +'<span><i class="fas fa-hashtag mr-1"></i>'+d.id+'</span>'
                      +'<span class="font-weight-bold">'+fmtMoney(d.monto)+'</span>'
                      +'</div>'
                      +'<div class="v2-mcard-body" style="grid-template-columns:1fr;">'
                      +'<div><div class="v2-lbl">Descripción</div><div class="v2-val">'+d.descripcion+'</div></div>'
                      +'<div><div class="v2-lbl">Fecha</div><div class="v2-val">'+d.created_at+'</div></div>'
                      +'</div>'
                      +'<div class="v2-mcard-footer">'
                      +'<button class="btn btn-primary edit" id="'+d.id+'"><i class="far fa-edit mr-1"></i>Editar</button>'
                      +'</div></div>';
                });
                $('#mobile-cards-gasto').html(h);
            }
        });
    }
    loadCards();

    /* ── Crear ──────────────────────────────────── */
    $('#btn-crear-desktop,#fab-gasto').on('click',function(){
        resetForm();
        $('#modal-gasto-heading').text('Nuevo gasto');
        $('#btn-gasto-text').text('Guardar');
        $('#modal-gasto').modal('show');
    });

    /* ── Editar ─────────────────────────────────── */
    $(document).on('click','.edit',function(){
        var id=$(this).attr('id');
        $.ajax({url:'{{ url("admin/v2/gasto") }}/'+id+'/editar',dataType:'json',
            success:function(data){
                var d=data.result;
                $('#monto_gasto').val(d.monto);
                $('#descripcion_gasto').val(d.descripcion);
                // Disparar evento para actualizar contador de caracteres
                $('#descripcion_gasto').trigger('input');
                $('#hidden_id_gasto').val(d.idg||id);
                $('#action_gasto').val('Edit');
                $('#modal-gasto-heading').text('Editar gasto #'+id);
                $('#btn-gasto-text').text('Actualizar');
                $('#modal-gasto').modal('show');
            }
        });
    });

    /* ── Submit ─────────────────────────────────── */
    $('#form-gasto').on('submit',function(e){
        e.preventDefault();
        var isEdit=($('#action_gasto').val()==='Edit');
        var id=$('#hidden_id_gasto').val();
        var url=isEdit?'{{ url("admin/v2/gasto") }}/'+id:'{{ route("admin.v2.gasto.guardar") }}';

        Swal.fire({title:'¿Confirmar?',icon:'question',showCancelButton:true,
            confirmButtonText:'Aceptar',cancelButtonText:'Cancelar'
        }).then(function(res){
            if(!res.value)return;
            loaderOn();
            $.ajax({url:url,method:isEdit?'PUT':'POST',
                data:$('#form-gasto').serialize(),dataType:'json',
                success:function(data){
                    loaderOff();
                    if(data.errors){
                        var h='<div class="alert alert-danger"><ul>';
                        data.errors.forEach(function(e){h+='<li>'+e+'</li>';});
                        $('#form_result_gasto').html(h+'</ul></div>');return;
                    }
                    $('#modal-gasto').modal('hide');
                    tabla.ajax.reload(); loadCards();
                    Swal.fire({icon:'success',title:isEdit?'Actualizado':'Registrado',timer:1500,showConfirmButton:false});
                },
                error:function(){loaderOff();Swal.fire('Error','No se pudo guardar.','error');}
            });
        });
    });

    /* ── Helpers ─────────────────────────────────── */
    function resetForm(){
        $('#form-gasto')[0].reset();
        $('#form_result_gasto').html('');
        $('#hidden_id_gasto').val('');$('#action_gasto').val('Add');
        $('#char-count').text('0');
    }
    function loaderOn(){$('#loader-gasto').addClass('active');$('#btn-submit-gasto').prop('disabled',true);}
    function loaderOff(){$('#loader-gasto').removeClass('active');$('#btn-submit-gasto').prop('disabled',false);}
});
</script>
@endsection
