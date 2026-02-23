@extends("theme.$theme.layout")

@section('titulo')
    Empresa
@endsection
@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css")}}" rel="stylesheet" type="text/css"/>
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet" type="text/css"/>       
@endsection


@section('scripts')
<script src="{{asset("assets/pages/scripts/admin/empresa/crear.js")}}" type="text/javascript"></script>    
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        {{-- @include('includes.form-error')
        @include('includes.form-mensaje') --}}
     <br>   
    <div class="card card-warning">
        <div class="card-header with-border">
          <h3 class="card-title">Empresa</h3>
          <div class="card-tools pull-right">
            <button type="button" name="create_empresa" id="create_empresa" class="btn btn-default" data-toggle="modal" data-target="#modal-u"><i class="fa fa-fw fa-plus-circle"></i>Crear Empresa</button>
            </button>
          </div>
        </div>
      <div class="card-body table-responsive p-2">
        
      <table id="empresa" class="table table-hover text-nowrap display responsive ">
    
        <thead>
        <tr>  
              <th>Editar</th>
              <th>Id</th>
              <th>Nombre</th>
              <th>Tipo de documento</th>
              <th>Documento</th>
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

<!-- /.Modal crear usuario -->
    <div class="modal fade" tabindex="-1" id ="modal-u" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">   
        <div class="row">
            <div class="col-lg-12">
              {{-- @include('includes.form-error')
              @include('includes.form-mensaje')     --}}
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
                              @include('admin.empresa.form')
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



{{-- <!-- /.Modal actualizar usuario -->
<div class="modal fade" tabindex="-1" id ="modal-a" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">   
  <div class="row">
      <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.form-mensaje')    
         <div class="card card-warning">
          <div class="card-header">
            <h3 class="card-title">Actualizar Empresa</h3>
            <div class="card-tools pull-right">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
          </div>
        <form action="" id="form-general" class="form-horizontal" method="POST">
          @csrf
          <div class="card-body">
                            @include('admin.empresa.form-editar')
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                          
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                            @include('includes.boton-form-editar-empresa')    
                          </div>
                         </div>
                        <!-- /.card-footer -->
          </form>
                   
      
         
    </div>
  </div>
</div>
</div>
</div>
</div> --}}


@endsection



@section("scriptsPlugins")
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/datatables-responsive/js/dataTables.responsive.min.js")}}" type="text/javascript"></script>



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
        $('#empresa')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        responsive: true,
        processing: true,
        lengthMenu: [ [25, 50, 100, 500, -1 ], [25, 50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        ajax:{
          url:"{{ route('empresa')}}",
              },
        columns: [
          {data:'editar'},
          {data:'id'},
          {data:'nombre'},
          {data:'tipo_documento'},
          {data:'documento'},
          {data:'activo'},
          
        ],

         //Botones----------------------------------------------------------------------
     
         "dom":'<"row"<"col-md-9 form-inline"l><"col-xs-3 form-inline"B>>rt<"row"<"col-md-8 form-inline"i> <"col-md-4 form-inline"p>>',
                   
                   buttons: [
                      {
    
                   extend:'copyHtml5',
                   titleAttr: 'Copy',
                   title:"seguimiento",
                   className: "btn btn-info"
    
    
                      },
                      {
    
                   extend:'excelHtml5',
                   titleAttr: 'Excel',
                   title:"seguimiento",
                   className: "btn btn-success"
    
    
                      },
                       {
    
                   extend:'csvHtml5',
                   titleAttr: 'csv',
                   className: "btn btn-warning"
    
    
                      },
                      {
    
                   extend:'pdfHtml5',
                   titleAttr: 'pdf',
                   className: "btn btn-primary"
    
    
                      }
                   ],


        
    
        });

$('#create_empresa').click(function(){
  $('.modal-title').text('Agregar Nueva Empresa');
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
    url = "{{route('guardar_empresa')}}";
    method = 'post';
    text = "Estás por crear empresa";
  }  

  if($('#action').val() == 'Edit')
  {
    var updateid = $('#hidden_id').val();
    url = "empresa/"+updateid;
    method = 'put';
    text = "Estás por actualizar empresa";
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
                        '<h5><i class="icon fas fa-check"></i> Mensaje Acuasur Rural</h5>';
                                     //<div class = "alert alert-danger alert-dismissible" data-auto-dismiss="3000">';
                    for (var count = 0; count < data.errors.length; count++)
                    {
                      html += '<p>' + data.errors[count]+'<p>';
                    }         
                    html += '</div>';
                    }
                    if(data.success == 'ok') {
                      $('#form-general')[0].reset();
                      $('#modal-u').modal('hide');
                      $('#empresa').DataTable().ajax.reload();
                      Manteliviano.notificaciones('Empresa creada correctamente', 'Sistema Ventas', 'success');
                      
                    }else if(data.success == 'ok1'){
                      $('#form-general')[0].reset();
                      $('#modal-u').modal('hide');
                      $('#empresa').DataTable().ajax.reload();
                      Manteliviano.notificaciones('Empresa actualizada correctamente', 'Sistema Ventas', 'success');

                    } 
                    $('#form_result').html(html)  
              }


           });
          }
        });
          

  });


  $(document).on('click', '.edit', function(){
    var id = $(this).attr('id');
    $('#form_result').html('');
    $.ajax({
        url:"empresa/"+id+"/editar",
        dataType: "json",
        success:function(data){
          $('#nombre').val(data.result.nombre);
          $('#tipo_documento').val(data.result.tipo_documento);
          $('#documento').val(data.result.documento);
          $('#activo').val(data.result.activo);
          $('#hidden_id').val(id);
          $('.modal-title').text('Editar Registro');
          $('#action_button').val('Edit');
          $('#action').val('Edit');
          $('#modal-u').modal('show');

        }
    })
  });

$('#form-general').on('click', '#guardar',  function(){
  
  
  var nombre = $('#nombre').val();
  var tipo_documento = $('#tipo_documento').val();
  var documento = $('#documento').val();
  var activo = $('#activo').val();
  
  if(nombre == '' || tipo_documento == '' || documento == '' || activo == ''){

        Swal.fire({
         title: 'Debes rellenar todos los campos',
         icon: 'warning',
         buttons:{
             cancel: "Cerrar"
             
                 }
           }) 

  }else{
     Swal.fire({
     title: "¿Estás seguro?",
     text: "Estás por crear empresa",
     icon: "success",
     showCancelButton: true,
     showCloseButton: true,
     confirmButtonText: 'Aceptar',
     }).then((result)=>{
    if(result.value){  
       $.ajax({
        
             url:"{{route('guardar_empresa')}}",
             method:'post',
             data:{nombre:nombre, tipo_documento:tipo_documento, documento:documento, activo:activo,
              "_token": $("meta[name='csrf-token']").attr("content")},

             success:function(respuesta)
             {  
              if(respuesta.mensaje = 'ok') {
               $('#modal-u').modal('hide');
               $('#empresa').DataTable().ajax.reload();
               
               Manteliviano.notificaciones('Empresa creada correctamente', 'Sistema Ventas', 'success');
               }else if(respuesta.mensaje = 'ng'){
              
                 $('#empresa').DataTable().ajax.reload();
                 Manteliviano.notificaciones('Nombre o Documento ya estan registrados, documento debe ser numerico', 'Sistema Ventas', 'warning');
             }
             }
            
              }).fail( function( jqXHR, textStatus, errorThrown ) {

          if (jqXHR.status === 0) {

            alert('Not connect: Verify Network.');

          } else if (jqXHR.status == 404) {

            alert('Requested page not found [404]');

          } else if (jqXHR.status == 422) {
                 Manteliviano.notificaciones('Nombre o Documento ya estan registrados, documento debe ser numerico y menor de 9999999999','Sistema Ventas',  'warning'); 
              
            
          } else if (textStatus === 'parsererror') {

            alert('Requested JSON parse failed.');

          } else if (textStatus === 'timeout') {

            alert('Time out error.');

          } else if (textStatus === 'abort') {

            alert('Ajax request aborted.');

          }       
            
              });

  }

});

  }
})


// $(document).on('click', function(Mostrar){
  
//   var id = $(this).attr('id');
//   $.ajax({
//     url:"http://127.0.0.1:8000/empresa/"+id+"/editar",
//     dataType:"json",
//     success:function(data){
//       $('#nombre').val(data.nombre);
//       $('#tipo_documento').val(data.tipo_documento);
//       $('#documento').val(data.documento);
//       $('#activo').val(data.activo);
//       $('#modal-a').modal('show');
//     }

//   })

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

