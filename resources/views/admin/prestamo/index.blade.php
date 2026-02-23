@extends("theme.$theme.layout")

@section('titulo')
    Prestamo
@endsection
@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet" type="text/css"/>
<link href="{{asset("assets/css/select2-bootstrap.min.css")}}" rel="stylesheet" type="text/css"/>
<link href="{{asset("assets/css/select2.min.css")}}" rel="stylesheet" type="text/css"/>

<style>
  .loader { 
   
  visibility: hidden; 
  background-color: rgba(255, 253, 253, 0.952); 
  position: absolute;
  z-index: +100 !important;
  width: 100%;  
  height:100%;
 }
    .loader img { position: relative; top:50%; left:40%;
      width: 180px; height: 180px; }
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
          <h3 class="card-title">Prestamo</h3>
          <div class="card-tools pull-right">
            <button type="button" name="create_prestamo" id="create_prestamo" class="btn btn-default" data-toggle="modal" data-target="#modal-pc"><i class="fa fa-fw fa-plus-circle"></i>Crear prestamo</button>
            </button>
          </div>
        </div>
      <div class="card-body table-responsive p-2">
        
      <table id="prestamo" class="table table-hover  text-nowrap">
        {{-- <table id="cliente" class="table table-striped table-bordered"> --}}
        <thead>
        <tr>  
              <th>Acciones</th>
              <th>Consecutivo</th>
              <th>Id_prestamo</th>
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>Monto</th>
              <th>Monto Total</th>
              <th>Saldo</th>
              <th>Saldo atrasado</th>
              <th>Cuotas atrasadas</th>
              <th>Tipo de Pago</th>
              <th>Cuotas</th>
              <th>Cuotas Pendientes</th>
              <th>Interes</th>
              <th>Valor Cuotas</th>
              <th>Fecha Inicial </th>
              <!--<th>Observación</th>
              <th>Estado</th>
              <th>Usuario</th>
              <th>Cliente</th>
              <th>Fecha de Prestamo</th>-->
              
                           
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




  
<!-- /.Modal crear prestamo -->



<div class="modal fade" tabindex="-1" id ="modal-pc"  role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">
     
  <div class="row">
      <div class="col-lg-12">
        <div class="loader col-lg-12"><img src="{{asset("assets/$theme/dist/img/loader6.gif")}}" class="" /> </div>
        @include('includes.form-error')
        @include('includes.form-mensaje')
         <div class="card card-danger">
          <div class="card-header">
              <h6 class="modal-title-pc"></h6>
            <div class="card-tools pull-right">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
                
              </div>   
                <span id="form_result"></span>
            </div>

        <form id="form-general" name="form-general" class="form-horizontal" method="post">
          @csrf
          <div class="card-body">
                        @include('admin.prestamo.form-prestamo')
          </div>
          <!-- /.card-body -->
                       <div class="card-footer">
                          
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                            @include('includes.boton-form-crear-prestamo')    
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
        
      <table id="detalleCuota" class="table table-hover  table-striped table-bordered"  style="width:100%">  
            
      </table>
      </div>
      <!-- /.class-table-responsive -->
  </div>
  <!-- /.card -->

  </div>
  </div>
</div>

<!-- /.Modal detalle pago -->
<div class="modal fade" tabindex="-1" id ="modal-p"  role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">


  <!-- Default box -->
  <div class="card">
    <div class="card-header">
      <h6 class="modal-title-p"></h6>
      <div class="card-tools pull-right">
          <button type="button" class="btn btn-block bg-gradient-primary btn-sm" data-dismiss="modal">Close</button>
        </div>
    </div>
    {{-- <div class="card-body"  id="detalles" style="display: block;">
      
  
  </div> --}}
  <!-- /.card body -->
  <!--tabla -->
      <div  class="card-body table-responsive p-2">
        
      <table id="detallePago" class="table table-hover  table-striped table-bordered"  style="width:100%">  
            
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
<script src="{{asset("assets/js/jquery-select2/select2.min.js")}}" type="text/javascript"></script>



<script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script>
 
 $(document).ready(function(){

$("#cliente_id").select2();




   //Calculo de monto total diario, semanal, quincenal y mensual al realizar cualqiuier cambio en los input

function monto(){
  
  if( $('#tipo_pago').val() == "Diario"){

    $('#monto_total').val(Math.round(parseFloat($("#monto").val()) +
     parseFloat((($("#monto").val() * ($("#interes").val()/100)) * ($("#cuotas").val()/$("#cuotas").val())))));
     $('#monto_pendiente').val($("#monto_total").val());
     $('#valor_cuota').val(Math.round( $('#monto_total').val()/$("#cuotas").val()));

    }else if( $('#tipo_pago').val() == "Mensual"){

      $('#monto_total').val(parseFloat($("#monto").val()) +
      parseFloat((($("#monto").val() * ($("#interes").val()/100)) * $("#cuotas").val())));
      $('#monto_pendiente').val($("#monto_total").val());

      $('#valor_cuota').val(Math.round( $('#monto_total').val()/$("#cuotas").val()));


    }else if( $('#tipo_pago').val() == "Quincenal"){

    $('#monto_total').val(Math.round(parseFloat($("#monto").val()) +
    parseFloat((($("#monto").val() * ($("#interes").val()/100)) * ($("#cuotas").val()/$("#cuotas").val())))));
    
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
        $('#prestamo')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
          // rowCallback: function(row, data, index){
  	      // if(data[7]<1){
    	    // $(row).find('td:eq(7)').css('color', 'blue');
          //               }
          // },  
        language: idioma_espanol,
        processing: true,
        lengthMenu: [ [25, 50, 100, 500, -1 ], [25, 50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        aaSorting: [[ 1, "asc" ]],
        
        ajax:{
          url:"{{ route('prestamo')}}",
              },
        columns: [
          {data:'action',
           name:'action',
           orderable: false
          },
          {data:'consecutivo',
          name:'consecutivo'
          },
          {data:'idp',
          name:'idp'
          },
          {data:'nombres',
          name:'nombres'
          },
          {data:'apellidos',
          name:'apellidos'
          },
          {data:'monto',
          name:'monto'
          },
          {data:'monto_total',
          name:'monto_total'
          },
          {data:'monto_pendiente',
           name:'monto_pendiente'
          },
          {data:'monto_atrasado',
           name:'monto_atrasado'
          },
          {data:'cuotas_atrasadas',
          name:'cuotas_atrasadas'
          },
          {data:'tipo_pago',
          name:'tipo_pago'
          },
          {data:'cuotas',
          name:'cuotas'
          },
          {data:'cuotas_pendientes',
          name:'cuotas_pendientes'
          },
          {data:'interes',
          name:'interes'
          },
          
          {data:'valor_cuota',
          name:'valor_cuota'
          },
          {data:'fecha_inicial',
          name:'fecha_inicial'
          }/*,
         
          {data:'observacion_prestamo',
          name:'observacion_prestamo'
          },
          {data:'activo',
          name:'activo'
          },
         
          {data:'usuario_id',
          name:'usuario_id'
          },
          {data:'cliente_id',
          name:'cliente_id'
          },
          {data:'created_at',
          name:'created_at'
          },*/
                   
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
                    if (data["monto_atrasado"] > 0) { 
                    $(row).css("background-color", "#d66745"); 
                    $(row).addClass("warning"); 
                    }else if(data["monto_pendiente"] == 0){
                    $(row).css("background-color", "#80c261"); 
                    $(row).addClass("warning"); 

                    }
                  }


        
    
        });



 //Crear prestamos
 

 $('#create_prestamo').click(function(){
  $('#form-general')[0].reset();
  $('.modal-title-pc').text('Crear prestamo');
  $('#action_button').val('Add');
  $('#action').val('Add');
  $('#form_result').html('');
  $('#modal-pc').modal('show');
    

  $('#form-general').on('submit', function(event){
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
                      $('#form-general')[0].reset();
                      $('#modal-pc').modal('hide');
                      $('#prestamo').DataTable().ajax.reload();
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
  
  $.ajax({
  url:"prestamo/"+id+"",
  dataType:"json",
  success:function(dataCuotas){
    $("#detalleCuota").append( 
      '<thead><tr><th align="center" style="dislay: none;">Numero de cuota</th>'+
              '<th align="center" style="dislay: none;">Valor cuota</th>'+
              '<th align="center" style="dislay: none;">Fecha de Cuota</th>'+
              '<th align="center" style="dislay: none;">Valor abonado</th>'+
              '<th align="center" style="dislay: none;">estado</th>'+
              '</tr></thead>'
      );
    $.each(dataCuotas.result1, function(i, items){
    $("#detalleCuota").append(        
    
    //Para colocar en tabla
             
   '<tr>'+
    '<td>'+items.d_numero_cuota+'</td>'+
    '<td>'+items.valor_cuota+ '</td>'+
    '<td>'+items.fecha_cuota+ '</td>'+
    '<td>'+items.valor_cuota_pagada+ '</td>'+
    '<td>'+items.estado+ '</td>'+
    
    '</tr>'
    
    );
    });
    $('.modal-title-d').text('Detalle de prestamo');
    $('#modal-d').modal('show');  
    
  
  } 
 
  
  
});
});
 
//Detalle pagos

$(document).on('click', '.pagos', function(){
  
  var id = $(this).attr('id');
  $("#detallePago").empty();
  
  $.ajax({
  url:"pago/"+id+"",
  dataType:"json",
  success:function(dataPagos){
    $("#detallePago").append( 
      '<thead><tr><th align="center" style="dislay: none;">Numero de Prestamo</th>'+
              '<th align="center" style="dislay: none;">Numero de cuota</th>'+
              '<th align="center" style="dislay: none;">Valor Abono</th>'+
              '<th align="center" style="dislay: none;">Observacion de Abono</th>'+
              '<th align="center" style="dislay: none;">Fecha de cuota</th>'+
              '<th align="center" style="dislay: none;">Fecha y hora de pago</th>'+
                            '</tr></thead>'
      );
    $.each(dataPagos.result1, function(i, items){
    $("#detallePago").append(        
    
    //Para colocar en tabla
             
   '<tr>'+
    '<td>'+items.prestamo_id+'</td>'+
    '<td>'+items.numero_cuota + '</td>'+
    '<td>'+items.valor_abono+ '</td>'+
    '<td>'+items.observacion_pago+ '</td>'+
    '<td>'+items.fecha_pago+ '</td>'+
    '<td>'+items.created_at+ '</td>'+
    '</tr>'
    
    );
    });
    $('.modal-title-p').text('Detalle de prestamo');
    $('#modal-p').modal('show');  
    
  
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

