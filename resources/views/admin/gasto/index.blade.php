@extends("theme.$theme.layout")

@section('titulo')
    Gastos
@endsection
@section("styles")

<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet" type="text/css"/>
@endsection


@section('scripts')
{{-- <script src="{{asset("assets/pages/scripts/admin/empleado/crear.js")}}" type="text/javascript"></script>     --}}
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.form-mensaje')
     <br>   
    <div class="card card-success">
        <div class="card-header with-border">
          <h3 class="card-title">Gastos</h3>
          <div class="card-tools pull-right">
            <button type="button" name="create_gasto" id="create_gasto" class="btn btn-default" data-toggle="modal" data-target="#modal-g"><i class="fa fa-fw fa-plus-circle"></i>Crear Gasto</button>
            </button>
          </div>
        </div>
      <div class="card-body table-responsive p-2">
        
      <table id="gasto" class="table table-hover  text-nowrap">
        {{-- <table id="empresa" class="table table-striped table-bordered"> --}}
        <thead>
        <tr>  
              <th>Editar</th>
              <th>Id</th>
              <th>Monto</th>
              <th>Descripcion</th>
              <th>Fecha<th>
              
        </tr>
        </thead>
        <tbody>
           
        </tbody>
      </table>
    </div>
  </form>
    <!-- /.card-body -->
</div>
</div>
</div>

<!-- /.Modal crear gasto -->
    <div class="modal fade" tabindex="-1" id ="modal-g" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">   
        <div class="row">
            <div class="col-lg-12">
              @include('includes.form-error')
              @include('includes.form-mensaje')
               <div class="card card-warning">
                <div class="card-header">
                     <h6 class="modal-title"></h6>
                  <div class="card-tools pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                      
                    </div>   
                      <span id="form_result"></span>
                  </div>

              <form id="form-general" name="form-general" class="form-horizontal" method="post">
                @csrf
                <div class="card-body">
                              @include('admin.gasto.form')
                </div>
                <!-- /.card-body -->
                             <div class="card-footer">
                                
                                <div class="col-lg-12"></div>
                                
                                  @include('includes.boton-form-crear-empresa-empleado-usuario')    
                              
                               </div>
                <!-- /.card-footer -->
              </form>
                         
            
               
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



  







@endsection



@section("scriptsPlugins")
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}" type="text/javascript"></script>



<script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script>
 
 $(document).ready(function(){

        //initiate dataTables plugin
      var datatable = 
        $('#gasto')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        processing: true,
        lengthMenu: [ [25, 50, 100, 500, -1 ], [25, 50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        ajax:{
          url:"{{ route('gasto')}}",
              },
        columns: [
          {data:'editar'},
          {data:'id'},
          {data:'monto'},
          {data:'descripcion'},
          {data:'created_at'},
        ],

         //Botones----------------------------------------------------------------------
     
         "dom":'<"row"<"col-xs-1 form-inline"><"col-md-4 form-inline"l><"col-md-5 form-inline"f><"col-md-3 form-inline"B>>rt<"row"<"col-md-8 form-inline"i> <"col-md-4 form-inline"p>>',
                   
         buttons: [
                      {
    
                   extend:'copyHtml5',
                   titleAttr: 'Copiar Registros',
                   title:"seguimiento",
                   className: "btn  btn-outline-primary btn-sm"
    
    
                      },
                      {
    
                   extend:'excelHtml5',
                   titleAttr: 'Exportar Excel',
                   title:"seguimiento",
                   className: "btn  btn-outline-success btn-sm"
    
    
                      },
                       {
    
                   extend:'csvHtml5',
                   titleAttr: 'Exportar csv',
                   className: "btn  btn-outline-warning btn-sm"
                   //text: '<i class="fas fa-file-excel"></i>'
                   
                      },
                      {
    
                   extend:'pdfHtml5',
                   titleAttr: 'Exportar pdf',
                   className: "btn  btn-outline-secondary btn-sm"
    
    
                      }
                   ],


        
    
        });

$('#create_gasto').click(function(){
  $('#form-general')[0].reset();
  $('.modal-title').text('Agregar Nuevo Gasto');
  $('#action_button').val('Add');
  $('#action').val('Add');
  $('#form_result').html('');
  $('#modal-g').modal('show');
 });

 $('#form-general').on('submit', function(event){
    event.preventDefault(); 
    var url = '';

  if($('#action').val() == 'Add')
  {
    url = "{{route('guardar_gasto')}}";
    method = 'post';
    text = "Estás por crear un gasto";
  }  

  if($('#action').val() == 'Edit')
  {
    var updateid = $('#hidden_id').val();
    url = "gasto/"+updateid;
    method = 'put';
    text = "Estás por actualizar un gasto";
  }  
    Swal.fire({
     title: "¿Estás seguro?",
     text: text,
     icon: "success", 
     showCancelButton: true,
     showCloseButton: true,
     confirmButtonText: 'Aceptar',
     }).then((result)=>{
    if(result.value){ 
    $.ajax({
           url:url,
           method:method,
           data:$(this).serialize(),
           dataType:"json",
           success:function(data){
              var html = '';
                    if(data.errors){

                    html = '<div class="alert alert-danger alert-dismissible" data-auto-dismiss="3000">'
                      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                        '<h5><i class="icon fas fa-check"></i> Mensaje Ventas</h5>';
                                     
                    for (var count = 0; count < data.errors.length; count++)
                    {
                      html += '<p>' + data.errors[count]+'<p>';
                    }         
                    html += '</div>';
                    }
                    if(data.success == 'ok') {
                      $('#form-general')[0].reset();
                      $('#modal-g').modal('hide');
                      $('#gasto').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'success',
                          title: 'gasto creado correctamente',
                          showConfirmButton: true,
                          timer: 1500
                        }
                      )
                                           
                    }else if(data.success == 'ok1'){
                      $('#form-general')[0].reset();
                      $('#modal-g').modal('hide');
                      $('#gasto').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'success',
                          title: 'gasto actualizado correctamente',
                          showConfirmButton: true,
                          timer: 1500
                        }
                      )
                     

                    } 
                    $('#form_result').html(html)  
              }


           });
          }
        });
          

  });



 // Edición de gasto

 $(document).on('click', '.edit', function(){
    var id = $(this).attr('id');
    
  $.ajax({
    url:"gasto/"+id+"/editar",
    dataType:"json",
    success:function(data){
      $('#monto').val(data.result.monto);
      $('#descripcion').val(data.result.descripcion);
      $('#hidden_id').val(id);
      $('.modal-title').text('Editar Cliente');
      $('#action_button').val('Edit');
      $('#action').val('Edit');
      $('#modal-g').modal('show');
     
    }
    

  }).fail( function( jqXHR, textStatus, errorThrown ) {

if (jqXHR.status === 403) {

  Manteliviano.notificaciones('No tienes permisos para realizar esta accion', 'Sistema Ventas', 'warning');

}});

 });



});



    
       

   var idioma_espanol =
                 {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla =(",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
                }   
       
  </script>
   

@endsection

