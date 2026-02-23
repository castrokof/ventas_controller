@extends("theme.$theme.layout")

@section('titulo')
    Pago
@endsection
@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-responsive/css/responsive.bootstrap4.min.css")}}" rel="stylesheet" type="text/css"/>
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet" type="text/css"/>
<link href="{{asset("assets/css/select2-bootstrap.min.css")}}" rel="stylesheet" type="text/css"/>
<link href="{{asset("assets/css/select2.min.css")}}" rel="stylesheet" type="text/css"/>

<style>
/* .dt-button {
  padding: 2px;
  border: true;
} */


</style>
@endsection


@section('scripts')

@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.form-mensaje')
     <br>   
    <div class="card card-success">
        <div class="card-header with-border">
          <h3 class="card-title">Pagos</h3>
          <div class="col-lg-6 card-tools pull-right">
          <div class="form-group row">
            <div class="col-lg-6 col-md-6 col-xs-12">
              <label for="estado" class="col-xs-4 control-label requerido">Seleccione los pagos</label>
                          <select name="estado_pago" id="estado_pago" class="form-control select2bs4" style="width: 80%;" required>
                          <option value="">---seleccione los pagos---</option>
                          <option value="0">Pagos por cobrar del día</option>
                          <option value="1">Pagos registrados del día</option>
                          <option value="2">Pagos atrasados y pendientes de cerrar</option>
                          <option value="3">Adelanto de cuotas</option>
                          </select>
            </div>
           
            <div class="col-lg-6 col-md-6 col-xs-12" id="input_p" style="display: none;">
              <label for="estado" class="col-xs-4 control-label requerido">Seleccione el prestamo</label>
                      <select name="prestamoc_id" id="prestamoc_id" class="form-control select2bs4" style="width: 80%;">
                      <option value="">---seleccione el prestamo---</option>
                      @foreach ($datasp as $id => $prestamocli)
                        <option value="{{$id}}" {{old('prestamoc_id')}} >{{$id.'=>'.$prestamocli}}</option>
                      @endforeach
                      </select>
            </div>
          </div>
          </div>
        </div>
      <div class="card-body table-responsive p-2">
        
      <table id="pago" class="table table-hover display responsive" cellspacing="0" width="100%">
       <thead>
        <tr>  
              <th>Acciones</th>
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>fecha de cuota</th>
              <th>Orden</th>
              <th>Id_prestamo</th>
              <th>Estado</th>
              <th>Monto</th>
              <th>Saldo</th>
              <th>Saldo atrasado</th>
              <th>Tipo de Pago</th>
              <th>Cuotas</th>
              <th>Cuotas atrasadas</th>
              <th>Interes</th>
              <th>Monto Total</th>
              <th>Cuota a cobrar</th>
              <th>Valor Cuotas</th>
              <th>Fecha Inicial</th>
              <th>Observación</th>
              <th>Fecha de Prestamo</th>
              
                           
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




  
<!-- /.Modal adicionar pago -->



<div class="modal fade" tabindex="-1" id ="modal-pd"  role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">
     
  <div class="row">
      <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.form-mensaje')
         <div class="card card-danger">
            <div class="card-header">
               <h6 class="modal-title-pd"></h6>
            <div class="card-tools pull-right">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
                
              </div>   
                <span id="form_result"></span>
            </div>

        <form id="form-general" name="form-general" class="form-horizontal" method="post">
          @csrf
          <div class="card-body">
                        @include('admin.pago.form-pago')
          </div>
          <!-- /.card-body -->
                       <div class="card-footer">
                          
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                            @include('includes.boton-form-registrar-pago')    
                        </div>
                         </div>
          <!-- /.card-footer -->
        </form>
                   
      
         
    </div>
  </div>
</div>
</div>
</div>

<!-- /.Modal detalle prestamo -->
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
    {{-- <div class="card-body"  id="detalles" style="display: block;">
      
  
  </div> --}}
  <!-- /.card body -->
  <!--tabla -->
      <div  class="card-body table-responsive p-2">
        
      <table id="detalleCuota" class="table table-hover  text-nowrap  table-striped table-bordered"  style="width:100%">  
            
      </table>
      </div>
      <!-- /.class-table-responsive -->
  </div>
  <!-- /.card -->

  </div>
  </div>
</div>

  


</div>



@endsection



@section("scriptsPlugins")
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/datatables-responsive/js/dataTables.responsive.min.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/js/jquery-select2/select2.min.js")}}" type="text/javascript"></script>



<script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script>
 
 $(document).ready(function(){
     
     
  $("#prestamoc_id").select2();     


  function ocultar(){
  
  if($('#customSwitch1').prop('checked') && $('#tipo_pago').val() != "Diario"){
    $("#chance_fecha").css("display", "block")
    $("#new_date").prop("required", true);
    $("#valor_abono_ocultar").css("display", "none");
    $("#valor_abono").removeAttr("required");
    $('#action_button').val('Chance');
    $('#action').val('Chance');
     }else{
      $("#chance_fecha").css("display", "none");
      $("#valor_abono_ocultar").css("display", "block");
      $("#valor_abono").prop("required", true);
      $("#new_date").removeAttr("required");
      $('#action_button').val('Add');
      $('#action').val('Add');
        }

    }

 $("#customSwitch1").change(ocultar);
 
 function ocultardiv(){
  
  if( $('#estado_pago').val() == "3" ){
     $("#input_p").css("display", "block")
  }
  else{
      $("#input_p").css("display", "none");
      }

  }
 
  $("#estado_pago").change(ocultardiv);
 
 



  fill_datatable();

  function fill_datatable( estado_pago = '', prestamoc_id = '')
  {
        //initiate dataTables plugin
      var datatable = 
        $('#pago')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        responsive: true,
        processing: true,
        lengthMenu: [ [50, 100, 500, -1 ], [50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        aaSorting: [[ 4, "asc" ]],
        
        ajax:{
          url:"{{ route('pago')}}",
          type:"get",
          data: {estado_pago:estado_pago, prestamoc_id:prestamoc_id}
              },
        columns: [
          {data:'action',
           name:'action',
           orderable: false
          },
          {data:'nombres',
          name:'nombres'
          },
          {data:'apellidos',
          name:'apellidos'
          },
          {data:'fecha_cuota',
          name:'fecha_cuota'
          },
          {data:'consecutivo',
          name:'consecutivo'
          },
          {data:'idp',
          name:'idp'
          },
          {data:'estado',
          name:'estado'
          },
          {data:'monto',
          name:'monto'}
          ,
          {data:'monto_pendiente',
           name:'monto_pendiente'
          },
          {data:'monto_atrasado',
           name:'monto_atrasado'
          },
          {data:'tipo_pago',
          name:'tipo_pago'
          },
          {data:'cuotas',
          name:'cuotas'
          },
          {data:'cuotas_atrasadas',
          name:'cuotas_atrasadas'
          },
          {data:'interes',
          name:'interes'
          },
          {data:'monto_total',
          name:'monto_total'
          },
          {data:'d_numero_cuota',
          name:'d_numero_cuota'
          },
          {data:'valor_cuota',
          name:'valor_cuota'
          },
          {data:'fecha_inicial',
          name:'fecha_inicial'
          },
          {data:'observacion_prestamo',
          name:'observacion_prestamo'
          },
         
          {data:'created_at',
          name:'created_at'
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
                   
                    "createdRow": function(row, data, dataIndex) { 
                    if (data["estado"] == "A") { 
                    $(row).css("background-color", "#d66745"); 
                    $(row).addClass("warning");
                    $('#pagodes', row).eq(0).css("display", "none");
                    }else if(data["estado"] == "P"){
                    $(row).css("background-color", "#50b7d6"); 
                    $(row).addClass("warning"); 
                    }else if (data["estado"] == "C") { 
                    $('#edicdes', row).eq(0).css("display", "none");
                    }
        
                   }


        
    
        });

  }


  $("#estado_pago").change(function(){
  
  var estado_pago = $('#estado_pago').val();
  var prestamoc_id = $('#prestamoc_id').val();

  if(estado_pago != '' || prestamoc_id !=  '' ){
    
       $('#pago').DataTable().destroy();
       fill_datatable(estado_pago, prestamoc_id);
    
    }
  });
   
  $("#prestamoc_id").change(function(){
  
  var estado_pago = $('#estado_pago').val();
  var prestamoc_id = $('#prestamoc_id').val();

  if(estado_pago != '' || prestamoc_id != '' ){
    
       $('#pago').DataTable().destroy();
       fill_datatable(estado_pago,prestamoc_id );
    
    }
  });


 //Registrar pago

   

  $('#form-general').on('submit', function(event){
    event.preventDefault(); 
    var text = '';
    var urlp = '';
    var methodp = '';

  if($('#action').val() == 'Add')
  {
    text = "Estás por registrar un pago";
    urlp = "{{route('guardar_pago')}}";
    methodp = 'post';
  }
  
  if($('#action').val() == 'Edit')
  {
    var id = $('#hidden_id').val();
    text = "Estás por actualizar un pago";
    urlp = "pago/"+id;
    methodp = 'put';
  }  

  if($('#action').val() == 'Chance')
  {
    text = "Estás por cambiar fecha del pago";
    urlp = "{{route('actualizar_cuota_fecha')}}";
    methodp = 'post';
  }


  Swal.fire({
     title: "¿Estás seguro?",
     text: text,
     icon: "warning", 
     showCancelButton: true,
     showCloseButton: true,
     allowOutsideClick: false,
     confirmButtonText: 'Aceptar',
      }).then((result)=>{
     if(result.value){
         Swal.fire({
                title: 'Espere por favor !',
                html: 'Realizando el pago',// add html attribute if you want or remove
                showConfirmButton: false,
                allowOutsideClick: false,
                willOpen: () => {
                Swal.showLoading()
                },
            }),
    $.ajax({
           url:urlp,
           method:methodp,
           data:$(this).serialize(),
           dataType:"json",
           success:function(data){
            if(data.success == 'ok') {
                      $('#form-general')[0].reset();
                      $('#modal-pd').modal('hide');
                      $('#pago').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'info',
                          title: 'Pago registrado correctamente',
                          showConfirmButton: false,
                          timer: 2500
                        }
                      )
                      // Manteliviano.notificaciones('Pago registrado correctamente', 'Sistema Ventas', 'success');
                      
                                    }else if(data.success == 'total') {
                      $('#form-general')[0].reset();
                      $('#modal-pd').modal('hide');
                      $('#pago').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'info',
                          title: 'Pago cancelado en su totalidad',
                          showConfirmButton: true,
                          timer: 2500
                        }
                      )
                      // Manteliviano.notificaciones('Pago registrado correctamente', 'Sistema Ventas', 'success');
                      
                                    }else if(data.success == 'oka') {
                      $('#form-general')[0].reset();
                      $('#modal-pd').modal('hide');
                      $('#pago').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'info',
                          title: 'Pago actualizado correctamente',
                          showConfirmButton: false,
                          timer: 2500
                        }
                      )
                      // Manteliviano.notificaciones('Pago registrado correctamente', 'Sistema Ventas', 'success');
                      
                                    }else if(data.success == 'okdate') {
                      $('#form-general')[0].reset();
                      $('#modal-pd').modal('hide');
                      $('#pago').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'info',
                          title: 'Fecha de cuota actualizada correctamente',
                          showConfirmButton: false,
                          timer: 2500
                        }
                      )
                      // Manteliviano.notificaciones('Pago registrado correctamente', 'Sistema Ventas', 'success');
                      
                                    }else if(data.success == 'noat'){
                                      $('#form-general')[0].reset();
                                      $('#modal-pd').modal('hide');
                                      $('#pago').DataTable().ajax.reload();
                                      Swal.fire(
                                                  {
                                                    icon: 'success',
                                                    title: 'El pago del atraso y cuota se realizo correctamente',
                                                    showConfirmButton: true,
                                                    timer: false
                                                  }
                                                )

                                      // Manteliviano.notificaciones('El pago supera el atrasado debe activar el boton abono', 'Sistema Ventas', 'success');

                                    }else if(data.success == 'adelantos'){
                                      Swal.fire(
                                                  {
                                                    icon: 'warning',
                                                    title: 'Para pagos adelantados selecciona de la lista Seleccione Pagos: el item adelanto de cuota',
                                                    showConfirmButton: true,
                                                    timer: false
                                                  }
                                                )

                                      

                                    }else if(data.success == 'adelantosa'){
                                      Swal.fire(
                                                  {
                                                    icon: 'warning',
                                                    title: 'Para pagos adelantados y pendientes de atrasos, debes registrar primero el pago total de atrasos + cuota diaria y luego selecciona de la lista Seleccione Pagos: el item adelanto de cuota para realizar los otros pagos',
                                                    showConfirmButton: true,
                                                    timer: false
                                                  }
                                                )

                                      

                                    }else if(data.success == 'vcda'){
                                      Swal.fire(
                                                  {
                                                    icon: 'warning',
                                                    title: 'Como el valor no supera los atrasos, debes seleccionar de la lista Seleccione Pagos: el item Pagos atrasados y editar la cuota a abonar',
                                                    showConfirmButton: true,
                                                    timer: false
                                                  }
                                                )

                                      

                                    }else if(data.success == 'abono') {
                                    $('#form-general')[0].reset();
                                    $('#modal-pd').modal('hide');
                                    $('#pago').DataTable().ajax.reload();
                                    Swal.fire(
                                      {
                                        icon: 'info',
                                        title: 'Pago adicionado a la cuota',
                                        showConfirmButton: true,
                                        timer: 2500
                                      }
                                    )
                                    
                      
                                }else if(data.success == 'okadelanto') {
                                    $('#form-general')[0].reset();
                                    $('#modal-pd').modal('hide');
                                    $('#pago').DataTable().ajax.reload();
                                    Swal.fire(
                                      {
                                        icon: 'info',
                                        title: 'Pago de adelanto de cuota fue registrado correctamente',
                                        showConfirmButton: true,
                                        timer: 2500
                                      }
                                    )
                                    
                      
                                }else if(data.success == 'noadelanto') {
                                    $('#form-general')[0].reset();
                                    $('#modal-pd').modal('hide');
                                    $('#pago').DataTable().ajax.reload();
                                   Swal.fire(
                                      {
                                        icon: 'info',
                                        title: 'No puedes hacer abonos mayores a la cuota diaria, debes de hacer cada abono correspondientes al valor de la cuota diaria',
                                        showConfirmButton: true,
                                        timer: false
                                      }
                                    )
                                    
                      
                                }else if(data.success == 'error') {
                                    $('#form-general')[0].reset();
                                    $('#modal-pd').modal('hide');
                                    $('#pago').DataTable().ajax.reload();
                                  Swal.fire(
                                      {
                                        icon: 'info',
                                        title: 'No puedes pagar cuotas adelantadas sin antes pagar las atrasadas',
                                        showConfirmButton: true,
                                        timer: false
                                      }
                                    )
                                    
                      
                                }else if(data.success == 'okca') {
                                    $('#form-general')[0].reset();
                                    $('#modal-pd').modal('hide');
                                    $('#pago').DataTable().ajax.reload();
                                  Swal.fire(
                                      {
                                        icon: 'info',
                                        title: 'Pago atrasado registrado correctamente',
                                        showConfirmButton: true,
                                        timer: false
                                      }
                                    )
                                    
                      
                                }else if(data.success == 'okcaerror') {
                                    $('#form-general')[0].reset();
                                    $('#modal-pd').modal('hide');
                                    $('#pago').DataTable().ajax.reload();
                                  Swal.fire(
                                      {
                                        icon: 'info',
                                        title: 'No puedes pagar un monto mayor o menor al atrasado de la cuota',
                                        showConfirmButton: true,
                                        timer: false
                                      }
                                    )
                                    
                      
                                }
                                 
                                 
                                 
                        }

               });
           }
       });

  });




//Pago a registrar

$(document).on('click', '.pay', function(){
    var id = $(this).attr('id');
    
  $.ajax({
    url:"pago/"+id+"/editar",
    dataType:"json",
    success:function(data){
      $.each(data.result, function(i, items){
      $('#nombres').val(items.nombres+' '+items.apellidos);
      $('#tipo_pago').val(items.tipo_pago);
      $('#idp').val(items.idp);
      $('#monto').val(items.monto_total);
      $('#monto_pendiente').val(items.monto_pendiente);
      $('#monto_atrasado').val(items.monto_atrasado);
      $('#n_cuota').val(items.d_numero_cuota);
      $('#cuotas_atrasadas').val(items.cuotas_atrasadas);
      $('#cuotas').val(items.cuotas);
      $('#valor_cuota').val(items.valor_cuota);
      $('#fecha_cuota').val(items.fecha_cuota);
      $('#valor_abono').val(items.valor_abono);
      $('#estado_cuota').val(items.estado);
      $('#vatraso').val(Math.round(parseFloat(items.valor_cuota)) - Math.round(parseFloat(items.valor_abono)));
      $('#hidden_id').val(items.usuario_id);
      $('.modal-title-pd').text('Registar pago');
      $('#action_button').val('Add');
      $('#action').val('Add');
      $('#modal-pd').modal('show');
              
    });
    }
    

  }).fail( function( jqXHR, textStatus, errorThrown ) {

if (jqXHR.status === 403) {

  Manteliviano.notificaciones('No tienes permisos para realizar esta accion', 'Sistema Ventas', 'warning');

}});

 });


//Pago a editar 

$(document).on('click', '.editpay', function(){
    var id = $(this).attr('id');
    
  $.ajax({
    url:"pago/"+id+"/editpay",
    dataType:"json",
    success:function(data){
      $.each(data, function(i, items){
      $('#nombres').val(items.nombres+' '+items.apellidos);
      $('#tipo_pago').val(items.tipo_pago);
      $('#idp').val(items.idp);
      $('#monto').val(items.monto_total);
      $('#monto_pendiente').val(items.monto_pendiente);
      $('#longitud').val(items.longitud);
      $('#monto_atrasado').val(items.monto_atrasado);
      $('#n_cuota').val(items.d_numero_cuota);
      $('#cuotas_atrasadas').val(items.cuotas_atrasadas);
      $('#cuotas').val(items.cuotas);
      $('#valor_cuota').val(items.valor_cuota);
      $('#fecha_cuota').val(items.fecha_cuota);
      $('#valor_abono').val(items.valor_abono);
      $('#hidden_id').val(items.usuario_id);
      $('.modal-title-pd').text('Editar pago');
      $('#action_button').val('Edit');
      $('#action').val('Edit');
      $('#modal-pd').modal('show');
              
    });
    }
    

  }).fail( function( jqXHR, textStatus, errorThrown ) {

if (jqXHR.status === 403) {

  Manteliviano.notificaciones('No tienes permisos para realizar esta accion', 'Sistema Ventas', 'warning');

}});

 });




//Detalle pago

$(document).on('click', '.detallepay', function(){
  
  var id = $(this).attr('id');
  $("#detalleCuota").empty();
  
  $.ajax({
  url:"pago/"+id+"",
  dataType:"json",
   success:function(dataPagos){
    $("#detalleCuota").append(        
    
    //Para colocar en tabla
              '<thead><tr><th align="center" style="dislay: none;">Numero de cuota</th>'+
              '<th align="center" style="dislay: none;">Valor abono</th>'+
              '<th align="center" style="dislay: none;">Fecha de Cuota</th>'+
              '<th align="center" style="dislay: none;">Fecha de Pago</th>'+
              '</tr></thead>'
    );          
    $.each(dataPagos.result1, function(i, items){
    $("#detalleCuota").append(        
    
          
   '<tr>'+
    '<td>'+items.numero_cuota+'</td>'+
    '<td>'+items.valor_abono + '</td>'+
    '<td>'+items.fecha_pago+ '</td>'+
    '<td>'+items.updated_at+ '</td>'+
    '</tr>'
    
    );
    });
    $('.modal-title-d').text('Detalle de pagos');
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

