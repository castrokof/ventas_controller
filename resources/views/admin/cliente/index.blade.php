@extends("theme.$theme.layout")

@section('titulo')
    Cliente
@endsection
@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet" type="text/css"/>

<style>
  .loader { 
   
  visibility: hidden; 
  background-color: rgba(255, 253, 253, 0.952); 
  position: absolute;
  left: 0;
  right: 0;
  bottom: 40%;
  top: 0;
  margin: auto; /* Centramos vertical y horizontalmente */
  z-index: +100 !important;
  width: 10%;  
  height:10%;
 }
    .loader img { position: relative; top:0%; left:0%;
      width: 100px; height: 100px; }
</style>
@endsection


@section('scripts')
<!--<script src="{{asset("assets/pages/scripts/admin/cliente/crear.js")}}" type="text/javascript"></script> -->   
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.form-mensaje')
     <br>   
    <div class="card card-info">
        <div class="card-header with-border">
          <h3 class="card-title">Cliente</h3>
          <div class="card-tools pull-right">
            <button type="button" name="create_cliente" id="create_cliente" class="btn btn-default" data-toggle="modal" data-target="#modal-u"><i class="fa fa-fw fa-plus-circle"></i>Crear cliente</button>
            </button>
          </div>
        </div>
      <div class="card-body table-responsive p-2">
        
      <table id="cliente" class="table table-hover  text-nowrap">
        {{-- <table id="cliente" class="table table-striped table-bordered"> --}}
        <thead>
        <tr>  
              <th>Acciones</th>
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
  </form>
    <!-- /.card-body -->
</div>
</div>
</div>

<!-- /.Modal crear cliente -->



    <div class="modal fade" tabindex="-1" id ="modal-u"  role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
           
        <div class="row">
            <div class="col-lg-12">
              @include('includes.form-error')
              @include('includes.form-mensaje')
               <div class="card card-info">
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
                              @include('admin.cliente.form')
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


  <!-- /.Modal crear prestamo -->



  <div class="modal fade" tabindex="-1" id ="modal-p"  role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
       
    <div class="row">
        <div class="col-lg-12">
          <div class="loader col-lg-12"><img src="{{asset("assets/$theme/dist/img/loader6.gif")}}" class="" /> </div>
          @include('includes.form-error')
          @include('includes.form-mensaje')
           <div class="card card-danger">
            <div class="card-header">
                 <h6 class="modal-title-p"></h6>
              <div class="card-tools pull-right">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                  
                </div>   
                  <span id="form_result"></span>
              </div>

          <form id="form-generalp" name="form-generalp" class="form-horizontal" method="post">
            @csrf
            <div class="card-body">
                          @include('admin.prestamo.form')
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

<!-- /.fin de modal crear prestamo -->


<div class="modal fade" tabindex="-1" id ="modal-d"  role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">


  <!-- Default box -->
  <div class="card">
    <div class="card-header">
      <h6 class="modal-title-d"></h6>
      <div class="card-tools pull-right">
          <button type="button" class="btn btn-block bg-gradient-primary btn-sm" data-dismiss="modal">Close</button>
        </div>
    </div>
    <div class="card-body"  id="detalles" style="display: block;">
      
  
  </div>
  <!-- /.card body -->
  <!--tabla -->
      {{-- <div  class="card-body table-responsive p-2">
        
      <table id="detalleCuota" class="table table-hover  text-nowrap">  
            
      </table>
      </div> --}}
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

//Calculo de monto total diario, semanal, quincenal y mensual al realizar cualqiuier cambio en los input

function monto(){
  
  if( $('#tipo_pago').val() == "Diario"){

    $('#monto_total').val(Math.round(parseFloat($("#monto").val()) +
     parseFloat((($("#monto").val() *  ($("#interes").val()/100)) * ($("#cuotas").val()/$("#cuotas").val())))));
     $('#monto_pendiente').val($("#monto_total").val());
     $('#valor_cuota').val(Math.round( $('#monto_total').val()/$("#cuotas").val()));

    }else if( $('#tipo_pago').val() == "Mensual"){

      $('#monto_total').val(parseFloat($("#monto").val()) +
      parseFloat((($("#monto").val() *  ($("#interes").val()/100)) * $("#cuotas").val())));
      $('#monto_pendiente').val($("#monto_total").val());

      $('#valor_cuota').val(Math.round( $('#monto_total').val()/$("#cuotas").val()));


    }else if( $('#tipo_pago').val() == "Quincenal"){

    $('#monto_total').val(Math.round(parseFloat($("#monto").val()) +
    parseFloat((($("#monto").val() *  ($("#interes").val()/100)) * ($("#cuotas").val()/$("#cuotas").val())))));
    
    $('#monto_pendiente').val($("#monto_total").val());

    $('#valor_cuota').val(Math.round( $('#monto_total').val()/$("#cuotas").val()));


    }else if( $('#tipo_pago').val() == "Semanal"){

    $('#monto_total').val(Math.round(parseFloat($("#monto").val()) +
    parseFloat((($("#monto").val() * ($("#interes").val()/100)) * ($("#cuotas").val()/$("#cuotas").val())))));
    $('#monto_pendiente').val($("#monto_total").val());

    $('#valor_cuota').val(Math.round( $('#monto_total').val()/$("#cuotas").val()));


    }

    
}

 $("#cuotas").change(monto);
 $("#interes").change(monto); 
 $("#monto").change(monto);
 $("#tipo_pago").change(monto);

// funcion Cuota------------------------------------------------------------------------

function cuota(){

 if( $('#monto_total').val() > 0){

  }   
}

$("#interes").change(cuota);

//---------------------------------------------------

        //initiate dataTables plugin
      var datatable = 
        $('#cliente')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        processing: true,
        lengthMenu: [ [25, 50, 100, 500, -1 ], [25, 50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        aaSorting: [[ 1, "asc" ]],
        ajax:{
          url:"{{ route('cliente')}}",
              },
        columns: [
          {data:'action',
           name:'action',
           orderable: false
          },
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

$('#create_cliente').click(function(){
  $('#form-general')[0].reset();
  $('.modal-title').text('Agregar Nuevo cliente');
  $('#action_button').val('Add');
  $('#action').val('Add');
  $('#form_result').html('');
  $('#modal-u').modal('show');
 });

 $('#form-general').on('submit', function(event){
    event.preventDefault(); 
    var url = '';
    var method = '';
    var text = '';

  if($('#action').val() == 'Add')
  {
    text = "Estás por crear un cliente"
    url = "{{route('guardar_cliente')}}";
    method = 'post';
  }  

  if($('#action').val() == 'Edit')
  {
    text = "Estás por actualizar un cliente"
    var updateid = $('#hidden_id').val();
    url = "cliente/"+updateid;
    method = 'put';
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
                      $('#modal-u').modal('hide');
                      $('#cliente').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'success',
                          title: 'cliente creado correctamente',
                          showConfirmButton: false,
                          timer: 1500
                          
                        }
                      )
                      // Manteliviano.notificaciones('cliente creado correctamente', 'Sistema Ventas', 'success');
                      
                    }else if(data.success == 'ok1'){
                      $('#form-general')[0].reset();
                      $('#modal-u').modal('hide');
                      $('#cliente').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'warning',
                          title: 'cliente actualizado correctamente',
                          showConfirmButton: false,
                          timer: 1500
                          
                        }
                      )
                      // Manteliviano.notificaciones('cliente actualizado correctamente', 'Sistema Ventas', 'success');

                    } 
                    $('#form_result').html(html)  
              }


           });
          }
        });
          

  });


 

// Edición de cliente

 $(document).on('click', '.edit', function(){
    var id = $(this).attr('id');
    
  $.ajax({
    url:"cliente/"+id+"/editar",
    dataType:"json",
    success:function(data){
      $('#nombres').val(data.result.nombres);
      $('#apellidos').val(data.result.apellidos);
      $('#tipo_documento').val(data.result.tipo_documento);
      $('#documento').val(data.result.documento);
      $('#pais').val(data.result.pais);
      $('#estado').val(data.result.estado);
      $('#ciudad').val(data.result.ciudad);
      $('#barrio').val(data.result.barrio);
      $('#sector').val(data.result.sector);
      $('#direccion').val(data.result.direccion);
      $('#celular').val(data.result.celular);
      $('#telefono').val(data.result.telefono);
      $('#consecutivo').val(data.result.consecutivo);
      $('#observacion').val(data.result.observacion_cli);
      $('#usuario_id').val(data.result.usuario_id);
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

//Crear prestamos

$(document).on('click', '.prestamo', function(){
    var id = $(this).attr('id');
    $('#cliente_id').val(id);
    $('.modal-title-p').text('Agregar prestamo');
    $('#action_button').val('Add  ');
    $('#action').val('Add');
    $('#modal-p').modal('show');
    

  $('#form-generalp').on('submit', function(event){
    event.preventDefault(); 
   
    if($('#action').val() == 'Add')
  {
    urlp = "{{route('guardar_prestamo')}}";
    methodp = 'post';
  }

  Swal.fire({
     title: "¿Estás seguro?",
     text: "Estás por crear un prestamo",
     icon: "success", 
     showCancelButton: true,
     showCloseButton: true,
     confirmButtonText: 'Aceptar',
     }).then((result)=>{
    if(result.value){
    $.ajax({
          beforeSend: function(){ 
          $('.loader').css("visibility", "visible"); },  
           url:urlp,
           method:methodp,
           data:$(this).serialize(),
           dataType:"json",
           success:function(data){
            if(data.success == 'ok') {
                      $('#form-generalp')[0].reset();
                      $('#modal-p').modal('hide');
                      $('#cliente').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'success',
                          title: 'prestamo agregado correctamente',
                          showConfirmButton: false,
                          timer: 1500
                          
                        }
                      )
                      // Manteliviano.notificaciones('prestamo agregado correctamente', 'Sistema Ventas', 'success');
                      
                                    }
          },
          complete: function(){ 
          $('.loader').css("visibility", "hidden");
          }

            });
                    }
                      });

  });

});


//Detalle prestamo

$(document).on('click', '.detalle', function(){
  
    var id = $(this).attr('id');
    $("#detalleCuota").empty();
    $("#detalles").empty();
    $.ajax({
    url:"prestamo/"+id+"",
    dataType:"json",
    success:function(data){
      $.each(data.result, function(i, item){
      $("#detalles").append(        
        '<div class="row">'+
        '<div class="col-12"><div class="info-box bg-info"><span class="info-box-icon"><i class="far fa-bookmark"></i></span><div class="info-box-content">'+
        '<span class="info-box-text">Nombre Cliente</span>'+
        '<span class="info-box-text">'+item.nombres+' '+item.apellidos+'</span>'+
        '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div></div><!-- /.col -->'+
        '</div>'+
        
        '<div class="row">'+
        '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-info"><span class="info-box-icon"><i class="fas fa-money-check-alt"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Monto</span>'+
              '<span class="info-box-number">'+item.monto+'</span>'+
        '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+

        '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-success"><span class="info-box-icon"><i class="fas fa-hashtag"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Cantidad de Cuotas</span>'+
              '<span class="info-box-number">'+item.cuotas+'</span>'+
        '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
        
        '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-warning"><span class="info-box-icon"><i class="far fa-calendar-alt"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Fecha de Prestamo</span>'+
              '<span class="info-box-date">'+item.created_at+'</span>'+
        '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
         
        '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-danger"><span class="info-box-icon"><i class="fab fa-paypal"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Tipo de Pago</span>'+
              '<span class="info-box-text">'+item.tipo_pago+'</span>'+
        '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
        '</div>'+
    // Segundo bloque de wigetds

    '<div class="row">'+
    '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-info"><span class="info-box-icon"><i class="far fa-bookmark"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Interes</span>'+
              '<span class="info-box-number-interes">'+item.interes+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+

    '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-success"><span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">valor de cuota</span>'+
              '<span class="info-box-number-valorcuota">'+item.valor_cuota+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
        
    '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-danger"><span class="info-box-icon"><i class="fas fa-money-check-alt"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Monto Total</span>'+
              '<span class="info-box-number-montototal">'+item.monto_total+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
    '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-warning"><span class="info-box-icon"><i class="fas fa-money-check-alt"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Saldo</span>'+
              '<span class="info-box-number-saldo">'+item.monto_pendiente+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
    
    '</div>'
    


    // Para colocar en tabla
    //             '<th>Monto</th>'+
    //             '<th>cuotas</th>'+
    //             '<th>Fecha de prestamo</th>'+
    //             '<th>Tipo de Pago</th>'+
    //             '<th>Interes</th>'+
    //             '<th>Valor Cuota</th>'+
    //             '<th>Monto Total</th>'+
    //             '<th>Fecha primera cuota</th></tr></thead>'+
        
    //  '<tr>'+
    //   '<td align="center" style="dislay: none;">'+item.nombres+' '+item.apellidos+'</td>'+
    //   '<td align="center" style="dislay: none;">'+item.monto + '</td>'+
    //   '<td align="center" style="dislay: none;">'+item.cuotas+ '</td>'+
    //   '<td align="center" style="dislay: none;">'+item.created_at+ '</td>'+
    //   '<td align="center" style="dislay: none;">'+item.tipo_pago+ '</td>'+
    //   '<td align="center" style="dislay: none;">'+item.interes+ '</td>'+
    //   '<td align="center" style="dislay: none;">'+item.valor_cuota+ '</td>'+
    //   '<td align="center" style="dislay: none;">'+item.monto_total+ '</td>'+
    //   '<td align="center" style="dislay: none;">'+item.fecha_inicial+ '</td>'+
    //    '</tr>'
       );
       
      }),
      function(dataCuotas){
      $.each(dataCuotas.result1, function(i, items){
      $("#detalleCuota").append(        
      
      //Para colocar en tabla
                '<tr><th>Numero de cuota</th>'+
                '<th>Valor cuota</th>'+
                '<th>Fecha de Cuota</th>'+
                '<th>estado</th>'+
                '</tr></thead>'+
        
     '<tr>'+
      '<td align="center" style="dislay: none;">'+items.numero_cuota+'</td>'+
      '<td align="center" style="dislay: none;">'+items.valor_cuota + '</td>'+
      '<td align="center" style="dislay: none;">'+items.fecha_cuota+ '</td>'+
      '<td align="center" style="dislay: none;">'+items.estado+ '</td>'+
      '</tr>'
      
      );
      });
       
      
    
    } 
    $('.modal-title-d').text('Detalle de prestamo');
    $('#modal-d').modal('show');
    }
    
  });
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

