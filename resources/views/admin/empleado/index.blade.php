@extends("theme.$theme.layout")

@section('titulo')
    Empleado
@endsection
@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet" type="text/css"/>       
@endsection


@section('scripts')
<!--<script src="{{asset("assets/pages/scripts/admin/empleado/crear.js")}}" type="text/javascript"></script> -->

@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.form-mensaje')
     <br>   
    <div class="card card-warning">
        <div class="card-header with-border">
          <h3 class="card-title">Empleado</h3>
          <div class="card-tools pull-right">
            <button type="button" name="create_empleado" id="create_empleado" class="btn btn-default" data-toggle="modal" data-target="#modal-u"><i class="fa fa-fw fa-plus-circle"></i>Crear Empleado</button>
            </button>
          </div>
        </div>
      <div class="card-body table-responsive p-2">
        
      <table id="empleado" class="table table-hover  text-nowrap">
        {{-- <table id="empresa" class="table table-striped table-bordered"> --}}
        <thead>
        <tr>  
              <th>Editar</th>
              <th>Id</th>
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>Tipo de documento</th>
              <th>Documento</th>
              <th>Pais</th>
              <th>Ciudad</th>
              <th>Barrio</th>
              <th>Dirección</th>
              <th>Celular</th>
              <th>Telefono</th>
              <th>Empresa</th>
              <th>Activo</th>
                           
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

<!-- /.Modal crear empleado -->
    <div class="modal fade" tabindex="-1" id ="modal-u" role="dialog" aria-labelledby="myLargeModalLabel">
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
                              @include('admin.empleado.form')
                </div>
                <!-- /.card-body -->
                             <div class="card-footer">
                                
                                  <div class="col-lg-3"></div>
                                  <div class="col-lg-6">
                                  @include('includes.boton-form-crear-empresa-empleado-usuario')    
                              </div>
                               </div>
                <!-- /.card-footer -->
              </form>
                         
            
               
          </div>
        </div>
      </div>
    </div>
  </div>
</div>






<!-- /.Modal detalle prestamo -->

  
<div class="modal fade" tabindex="-1" id ="modal-cliente"  role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">


  <!-- Default box -->
  <div class="card card-success" style="transition: all 0.15s ease 0s; height: inherit; width: inherit;">
    <div class="card-header">
      <h3 class="card-title-cli"></h3>

      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="maximize">
          <i class="fas fa-expand"></i>
        </button>
        <button type="button" class="btn btn-tool" data-card-widget="collapse">
          <i class="fas fa-minus"></i>
        </button>
        
          <button type="button" class="btn btn-tool" data-dismiss="modal">
            <i class="fas fa-times"></i>
          </button>
        
       
      </div>
      <!-- /.card-tools -->
    </div>
    <!-- /.card-header -->
   
  <div class="card-body table-responsive p-2">
        
    <table id="cliente" class="table table-hover  text-nowrap">
      {{-- <table id="cliente" class="table table-striped table-bordered"> --}}
      <thead>
      <tr>  
           
            <th>Consecutivo</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Tipo de documento</th>
            <th>Documento</th>
            <th>Telefono</th>
            <th>Celular</th>
            <th>Dirección</th>
            <th>Estado</th>
            <th>Pais</th>
            <th>Ciudad</th>
            <th>Barrio</th>
            <th>Sector</th>
            <th>Activo</th>
            <th>Observación</th>
            <th>Usuario_id</th>
            
                         
      </tr>
      </thead>
      <tbody>
         
      </tbody>
    </table>
  </div>
      <!-- /.class-table-responsive -->
  </div>
  <!-- /.card -->

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
     
      
    $("#form-general").validate({
                errorElement: 'span',
                errorClass: 'help-block help-block-error',
                focusInvalid: false,
                ignore: "",
                highlight: function(element, errorClass, validClass){
                    $(element).closest('.form-control').addClass('is-invalid');
                },
                unhighlight: function(element){
                    $(element).closest('.form-control').removeClass('is-invalid');
                },
                success: function(label){
                    label.closest('.form-control').removeClass('is-invalid');
                },
                errorPlacement: function(error, element){
                    if($(element).is('select') && element.hasClass('bs-select')){
                        error.insertAfter(element);
                    }else if($(element).is('select') && element.hasClass('select2-hidden-accessible')){
                        element.next().after(error);
                    }else if(element.attr("data-error-container")){
                        error.appendTo(element.attr("data-error-container"));
                    }else{
                        error.insertAfter(element);
                    }
                },
                invalidHandler: function(event, validator){

                },
                submitHandler: function(form){

                    return true;

                }
            });
      

  fill_datatable();

  function fill_datatable( id = '')
  {
      //---------------------------------------------------

        //initiate dataTables plugin
        var datatable1 = 
        $('#cliente')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        processing: true,
        lengthMenu: [ [25, 50, 100, 500, -1 ], [25, 50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        aaSorting: [[ 0, "asc" ]],
        ajax:{
          url:"{{ route('clientecli')}}",
          type:"get",
          data: {id:id}, 
              },
        columns: [
          
          {data:'consecutivo',
          name:'consecutivo'
          },
          {data:'nombres',
          name:'nombres'}
          ,
          {data:'apellidos',
          name:'apellidos'
          },
          {data:'tipo_documento',
          name:'tipo_documento'
          },
          {data:'documento',
          name:'documento'
          },
          {data:'telefono',
          name:'telefono'
          },
          {data:'celular',
          name:'celular'
          },
          {data:'direccion',
          name:'direccion'
          },
          {data:'estado',
          name:'estado'
          },
          {data:'pais',
          name:'pais'
          },
          {data:'ciudad',
          name:'ciudad'
          },
          {data:'barrio',
          name:'barrio'
          },
          {data:'sector',
          name:'sector'
          },
         
          {data:'activo',
          name:'activo'
          },
          {data:'observacion_cli',
          name:'observacion'
          },
          {data:'usuario_id',
          name:'usuario_id'
          },
                   
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

      }
$(document).on('click', '.clientes', function(){
  
  var id = $(this).attr('id');
 
  if(id != '' ){
    
       $('#cliente').DataTable().destroy();
       fill_datatable(id);
       $('.card-title-cli').text('Clientes');
       $('#modal-cliente').modal('show');  
    }
  });

     
        //initiate dataTables plugin
      var datatable = 
        $('#empleado')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        processing: true,
        lengthMenu: [ [25, 50, 100, 500, -1 ], [25, 50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        ajax:{
          url:"{{ route('empleado')}}",
              },
        columns: [
          {data:'editar'},
          {data:'ide'},
          {data:'nombres'},
          {data:'apellidos'},
          {data:'tipo_documento'},
          {data:'documento'},
          {data:'pais'},
          {data:'ciudad'},
          {data:'barrio'},
          {data:'direccion'},
          {data:'celular'},
          {data:'telefono'},
          {data:'nombre'}, 
          {data:'activo'},
          
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

$('#create_empleado').click(function(){
  $('#form-general')[0].reset();
  $('.modal-title').text('Agregar Nuevo Empleado');
  $('#action_button').val('Add');
  $('#action').val('Add');
  $('#form_result').html('');
  $('#modal-u').modal('show');
 });

 $('#form-general').on('submit', function(event){
    event.preventDefault(); 
    var url = '';

  if($('#action').val() == 'Add')
  {
    url = "{{route('guardar_empleado')}}";
    method = 'post';
    text = "Estás por crear un empleado";
  }  

  if($('#action').val() == 'Edit')
  {
    var updateid = $('#hidden_id').val();
    url = "empleado/"+updateid;
    method = 'put';
    text = "Estás por actualizar un empleado";
  }
  
  if($('#nombres').val() == '' || $('#apellidos').val() == '' || $('#tipo_documento').val() == '' || $('#documento').val() == '' || $('#pais').val() == '' || $('#ciudad').val() == '' || $('#barrio').val() == '' || $('#direccion').val() == '' || $('#ciudad').val() == '' || $('#celular').val() == '' || $('#telefono').val() == '' || $('#empresa_id').val() == '' || $('#activo').val() == ''){
  
        Swal.fire({
              title: 'Los campos no pueden estar vacios',
              icon: 'warning',
              showCloseButton: true,
              confirmButtonText: 'Aceptar',
                }) 
        
     
       }else{
  
  
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
                      $('#modal-u').modal('hide');
                      $('#empleado').DataTable().ajax.reload();
                      Manteliviano.notificaciones('Empleado creado correctamente', 'Sistema Ventas', 'success');
                      
                    }else if(data.success == 'ok1'){
                      $('#form-general')[0].reset();
                      $('#modal-u').modal('hide');
                      $('#empleado').DataTable().ajax.reload();
                      Manteliviano.notificaciones('Empleado actualizado correctamente', 'Sistema Ventas', 'success');

                    } 
                    $('#form_result').html(html)  
              }


           });
          }
        });
          

  }});


//   $(document).on('click', '.clientes', function(){
//     var id = $(this).attr('id');
    
//   $.ajax({
//     url:"http://127.0.0.1:8000/cliente/"+id+"",
//     dataType:"json"
//   })
// });
 // Edición de empleado

 $(document).on('click', '.edit', function(){
    var id = $(this).attr('id');
    
  $.ajax({
    url:"empleado/"+id+"/editar",
    dataType:"json",
    success:function(data){
      $('#nombres').val(data.result.nombres);
      $('#apellidos').val(data.result.apellidos);
      $('#tipo_documento').val(data.result.tipo_documento);
      $('#documento').val(data.result.documento);
      $('#pais').val(data.result.pais);
      $('#empresa_id').val(data.result.empresa_id);
      $('#ciudad').val(data.result.ciudad);
      $('#barrio').val(data.result.barrio);
      $('#sector').val(data.result.sector);
      $('#direccion').val(data.result.direccion);
      $('#celular').val(data.result.celular);
      $('#telefono').val(data.result.telefono);
      $('#activo').val(data.result.activo);
      $('#hidden_id').val(id);
      $('.modal-title').text('Editar Cliente');
      $('#action_button').val('Edit');
      $('#action').val('Edit');
      $('#modal-u').modal('show');
     
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

