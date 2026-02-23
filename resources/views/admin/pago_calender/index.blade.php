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
<!-- jQuery ui -->

<script src="{{asset("assets/pages/scripts/admin/pagocalender/index.js")}}" type="text/javascript"></script> 
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
                         <option value="6">Pagos por cobrar del día</option>
                          <option value="1">Pagos registrados del día</option>
                          <!--<option value="2">Pagos atrasados y pendientes de cerrar</option>-->
                          <option value="4">Pagos por cobrar del día por prestamo</option>
                          <option value="5">Pagos registrados del día por prestamo</option>
                          </select>
            </div>
    
          </div>
          </div>
        </div>
      <div class="card-body table-responsive p-2">
        
      <table id="pago" class="table table-hover table-sm display responsive" cellspacing="0" width="100%">
       <thead>
        <tr>  
              <th>Acciones</th>
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>Orden</th>
              <th>Direccion</th>
              <th>Id_prestamo</th>
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



<div class="modal fade" tabindex="-1" id ="modal-pd" style="overflow-y: scroll;" role="dialog" aria-labelledby="myLargeModalLabel">
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
                        @include('admin.pago_calender.form-pago')
          </div>
          <!-- /.card-body -->
                       <div class="card-footer">
                          
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                              @include('includes.boton-form-registrar-pago')
                          <!--  @include('includes.boton-form-registrar-pago2') -->
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
    
  <!-- /.card body -->
  <!--tabla -->
      <div  class="card-body table-responsive p-2">
        
      <table id="detalleCuota" class="table  table-sm text-nowrap  table-striped table-bordered"  style="width:100%">  
            
      </table>
      </div>
      <!-- /.class-table-responsive -->
  </div>
  <!-- /.card -->

  </div>
  </div>
</div>

  


<div class="modal fade" tabindex="-1" id ="modal-dp"  role="dialog" aria-labelledby="myLargeModalLabel">
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
        </div>
    </div>
    </div>
</div>
  
 
 

<div class="modal fade" tabindex="-1" id ="modal-acuotas"  style="overflow-y: scroll;" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">


  <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h6 class="modal-title-acuotas"></h6>
          <div class="card-tools pull-right">
              <button type="button" class="btn btn-block bg-gradient-primary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    
    
          <div class="card-body table-responsive p-2">
            
          <table id="pagoa" class="table table-hover table-sm responsive" cellspacing="0" width="100%">
           <thead class="thead-light"
            <tr>  
                  <th>Acciones</th>
                  <th># Cuota</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                  <th>Nombres</th>
                  <th>Apellidos</th>
                  <th>Id_prestamo</th>
            </tr>
            </thead>
            <tbody>
               
            </tbody>
          </table>
         </div>
     
     </div>
      <!-- /.card -->

  </div>
  </div>
</div>


<div class="modal fade" tabindex="-1" id ="modal-atrasosp"  style="overflow-y: scroll;" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">


  <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h6 class="modal-title-atrasosp"></h6>
          <div class="card-tools pull-right">
              <button type="button" class="btn btn-block bg-gradient-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    
    
          <div class="card-body table-responsive p-2">
            
          <table id="atrasosp" class="table table-hover table-sm responsive" cellspacing="0" width="100%">
           <thead class="thead-light"
            <tr>  
                  <th>Acciones</th>
                  <th># Cuota</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                  <th>Nombres</th>
                  <th>Apellidos</th>
                  <th>Id_prestamo</th>
            </tr>
            </thead>
            <tbody>
               
            </tbody>
          </table>
         </div>
     
     </div>
      <!-- /.card -->

  </div>
  </div>
</div>


<div class="modal fade" tabindex="-1" id ="modal-registradosp"  style="overflow-y: scroll;" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">


  <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h6 class="modal-title-registradosp"></h6>
          <div class="card-tools pull-right">
              <button type="button" class="btn btn-block bg-gradient-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    
    
          <div class="card-body table-responsive p-2">
            
          <table id="registradosp" class="table table-hover table-sm responsive" cellspacing="0" width="100%">
           <thead class="thead-light"
            <tr>  
                  <th>Acciones</th>
                  <th># Cuota</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                  <th>Nombres</th>
                  <th>Apellidos</th>
                  <th>Id_prestamo</th>
            </tr>
            </thead>
            <tbody>
               
            </tbody>
          </table>
         </div>
     
     </div>
      <!-- /.card -->

  </div>
  </div>
</div>


<div class="modal fade" tabindex="-1" id ="modal-pagonow"  style="overflow-y: scroll;" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">


  <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h6 class="modal-title-pagonow"></h6>
          <div class="card-tools pull-right">
              <button type="button" class="btn btn-block bg-gradient-danger btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    
    
          <div class="card-body table-responsive p-2">
            
          <table id="pagonow" class="table table-hover table-sm responsive" cellspacing="0" width="100%">
           <thead class="thead-light"
            <tr>  
                  <th>Acciones</th>
                  <th># Cuota</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                  <th>Nombres</th>
                  <th>Apellidos</th>
                  <th>Id_prestamo</th>
            </tr>
            </thead>
            <tbody>
               
            </tbody>
          </table>
         </div>
     
     </div>
      <!-- /.card -->

  </div>
  </div>
</div>
@endsection



@section("scriptsPlugins")
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/datatables-responsive/js/dataTables.responsive.min.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/js/jquery-select2/select2.min.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/fullcalendar-5.7.0/lib/main.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/fullcalendar-5.7.0/lib/locales/es.js")}}" type="text/javascript"></script>

<script src="{{asset("assets/$theme/plugins/jquery-ui/jquery-ui.min.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/jquery-touch/jquery.ui.touch-punch.min.js")}}"></script>

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
        responsive: false,
        processing: true,
        lengthMenu: [ [50, 100, 500, -1 ], [50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        aaSorting: [[ 3, "asc" ]],
        
        ajax:{
          url:"{{ route('pagoc')}}",
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
          {data:'consecutivo',
          name:'consecutivo'
          },
          {data:'direccion',
          name:'direccion'
          },
          {data:'idp',
          name:'idp'
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
                    if (data["estado"] == "A" ) { 
                    $(row).css("background-color", "#d66745"); 
                    $(row).addClass("warning");
                    $('#pagodes', row).eq(0).css("display", "none");
                    }else if(data["estado"] == "P"){
                    $(row).css("background-color", "#50b7d6"); 
                    $(row).addClass("warning");
                    }else if (data["monto_atrasado"] > 0 && data["cuotas_atrasadas"] < 3) { 
                    $(row).css("background-color", "#f59211"); 
                    $(row).addClass("warning");
                    }else if (data["monto_atrasado"] > 0 && data["cuotas_atrasadas"] > 2) { 
                    $(row).css("background-color", "#f23333"); 
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
    urlp = "{{route('guardar_pagoc')}}";
    methodp = 'post';
  }
  
  if($('#action').val() == 'Edit')
  {
    var id = $('#hidden_id').val();
    text = "Estás por actualizar un pago";
    urlp = "pagoc/"+id;
    methodp = 'put';
  }  

  if($('#action').val() == 'Chance')
  {
    text = "Estás por cambiar fecha del pago";
    urlp = "{{route('actualizar_cuota_fecha')}}";
    methodp = 'post';
  }


  Swal.fire({
     target: document.getElementById('modal-pd'),
     title: "¿Estás seguro?",
     text: text,
     icon: "info", 
     showCancelButton: true,
     showCloseButton: true,
     allowOutsideClick: false,
     confirmButtonText: 'Aceptar',
      }).then((result)=>{
     if(result.value){
         Swal.fire({
                target: document.getElementById('modal-pd'),
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
                      $('#pagonow').modal('hide');
                      $('#pago').DataTable().ajax.reload();
                    
                      
                      Swal.fire(
                        {
                          icon: 'success',
                          title: 'Pago registrado correctamente',
                          showConfirmButton: false,
                          timer: 2500
                        }
                      )
                      //$('#atrasosp').DataTable().ajax.reload();
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
                      $('#registradosp').DataTable().ajax.reload();
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
                                    $('#pagoa').DataTable().ajax.reload();
                                    
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
    //var idf = $(this).attr('idf');
      $('#customSwitch1').prop('checked', false)
      $("#chance_fecha").css("display", "none");
      $("#valor_abono_ocultar").css("display", "block");
      $("#valor_abono").prop("required", true);
      $("#new_date").removeAttr("required");
      $('#action_button').val('Add');
      $('#action').val('Add');
    
  $.ajax({
    url:"pagoc/"+id+"/editar",
    //data:idf,
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
       if(items.valor_cuota_pagada == null){
          $('#valor_abono').val(parseFloat(items.valor_cuota))
          
      }else{
          $('#valor_abono').val(Math.round(parseFloat(items.valor_cuota)) - Math.round(parseFloat(items.valor_cuota_pagada)))
      }
      
      $('#estado_cuota').val(items.estado);
      $('#vatraso').val(Math.round(parseFloat(items.valor_cuota)) - Math.round(parseFloat(items.valor_cuota_pagada)));
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

//Pago a registrar por prestamo

$(document).on('click', '.paypp', function(){
    var id = $(this).attr('id');
    var idf = $(this).attr('idf');
    
  $.ajax({
    url:"pagocp/"+id+"/editarp",
    data:idf,
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
      if(items.valor_cuota_pagada == null){
          $('#valor_abono').val(parseFloat(items.valor_cuota))
          
      }else{
          $('#valor_abono').val(Math.round(parseFloat(items.valor_cuota)) - Math.round(parseFloat(items.valor_cuota_pagada)))
      }
      
      $('#estado_cuota').val(items.estado);
      $('#vatraso').val(Math.round(parseFloat(items.valor_cuota)) - Math.round(parseFloat(items.valor_cuota_pagada)));
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
    url:"pagoc/"+id+"/editpay",
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
  url:"pagoc/"+id+"",
  dataType:"json",
   success:function(dataPagos){
    $("#detalleCuota").append(        
    
    //Para colocar en tabla
              '<thead><tr><th align="center" ># Cuota</th>'+
              '<th align="center" >Valor abono</th>'+
              '<th align="center" >Fecha de Cuota</th>'+
              '<th align="center" >Fecha de Pago</th>'+
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


//Detalle pago

$(document).on('click', '.detallepagos', function(){
  
  var id = $(this).attr('id');
  $("#detalleCuota").empty();
  
  $.ajax({
  url:"pagoc/"+id+"",
  dataType:"json",
   success:function(dataPagos){
    $("#detalleCuota").append(        
    
    //Para colocar en tabla
              '<thead><tr><th align="center" >#-Cuota</th>'+
              '<th align="center" >Valor-abono</th>'+
              '<th align="center">Fecha-Cuota</th>'+
              '<th align="center">Fecha-Pago</th>'+
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

//Detalle prestamo

$(document).on('click', '.detalle', function(){
  
    var id = $(this).attr('id');
    $("#detalleCuota").empty();
    $("#detalles").empty();
    $.ajax({
    url:"prestamopn/"+id+"",
    dataType:"json",
    success:function(data){
      $.each(data.result, function(i, item){
      $("#detalles").append(        
        '<div class="row">'+
        '<div class="col-12"><div class="info-box bg-info"><span class="info-box-icon"><i class="far fa-bookmark"></i></span><div class="info-box-content">'+
        '<span class="info-box-text">Detalle Prestamo</span>'+
        '<span class="info-box-text">'+item.nombres+' '+item.apellidos+'</span>'+
        '<span class="info-box-text">'+item.direccion+' Cel: '+item.celular+'</span>'+
        '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div></div><!-- /.col -->'+
        '</div>'+
        
        '<div class="row">'+
        '<div class="col-md-3 col-sm-6 col-6"><div class="info-box bg-info"><span class="info-box-icon"><i class="fas fa-money-check-alt"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Monto</span>'+
              '<span class="info-box-number">'+item.monto+'</span>'+
        '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+

        '<div class="col-md-3 col-sm-6 col-6"><div class="info-box bg-info"><span class="info-box-icon"><i class="far fa-bookmark"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Interes</span>'+
              '<span class="info-box-number-interes">'+item.interes+'</span>'+
        '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
        '<div class="col-md-3 col-sm-6 col-6"><div class="info-box bg-danger"><span class="info-box-icon"><i class="fas fa-money-check-alt"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Monto Total</span>'+
              '<span class="info-box-number-montototal">'+item.monto_total+'</span>'+
        '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
        '<div class="col-md-3 col-sm-6 col-6"><div class="info-box bg-warning"><span class="info-box-icon"><i class="fas fa-hashtag"></i></span><div class="info-box-content">'+
                  '<span class="info-box-text">Cuotas Pend</span>'+
                  '<span class="info-box-number-saldo">'+Math.round(parseFloat(item.monto_pendiente)/parseFloat(item.valor_cuota))+'</span>'+
                 
        '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
            
      
        '</div>'+
    // Segundo bloque de wigetds

    '<div class="row">'+
    
    '<div class="col-md-3 col-sm-6 col-6"><div class="info-box bg-success"><span class="info-box-icon"><i class="fas fa-hashtag"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">Cuotas</span>'+
              '<span class="info-box-number">'+item.cuotas+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+

    '<div class="col-md-3 col-sm-6 col-6"><div class="info-box bg-success"><span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span><div class="info-box-content">'+
              '<span class="info-box-text">valor de cuota</span>'+
              '<span class="info-box-number-valorcuota">'+item.valor_cuota+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
    
    '<div class="col-md-3 col-sm-6 col-6"><div class="info-box bg-danger"><span class="info-box-icon"><i class="fab fa-paypal"></i></span><div class="info-box-content">'+
          '<span class="info-box-text">Tipo</span>'+
          '<span class="info-box-text">'+item.tipo_pago+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
    
     '<div class="col-md-3 col-sm-6 col-6"><div class="info-box bg-info"><span class="info-box-icon"><i class="fab fa-paypal"></i></span><div class="info-box-content">'+
          '<span class="info-box-text"># Prestamo</span>'+
          '<span class="info-box-text">'+item.idp+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
    
    '<div class="col-md-3 col-sm-6 col-6"><div class="info-box bg-danger"><span class="info-box-icon"><i class="fab fa-paypal"></i></span><div class="info-box-content">'+
          '<span class="info-box-text"># Atrasos</span>'+
          '<span class="info-box-text">'+item.cuotas_atrasadas+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
    
     '<div class="col-md-3 col-sm-6 col-6"><div class="info-box bg-info"><span class="info-box-icon"><i class="fas fa-money-check-alt"></i></span><div class="info-box-content">'+
          '<span class="info-box-text">Monto atraso</span>'+
          '<span class="info-box-text">'+item.monto_atrasado+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div></div><!-- /.info-box-content --></div><!-- /.info-box --></div><!-- /.col -->'+
   
    '<div class="col-md-3 col-sm-6 col-12"><div class="info-box bg-warning"><span class="info-box-icon"><i class="far fa-calendar-alt"></i></span><div class="info-box-content">'+
          '<span class="info-box-text">Saldo Pend : '+parseFloat(item.monto_pendiente)+'</span>'+
          '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>'+
          '<span class="info-box-text">Fecha inicio prestamo</span>'+
          '<span class="info-box-date">'+item.fecha_inicial+'</span>'+
    '<div class="progress"><div class="progress-bar" style="width: 100%"></div></div>'+
    '<span class="info-box-text">Fecha creación prestamo</span>'+
          '<span class="info-box-date">'+item.created_at+'</span>'+
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
       
      });
    $('.modal-title-dp').text('Detalle de prestamo');
    $('#modal-dp').modal('show');
    }
    
  });
});




$(document).on('click', '.adelantoc', function(){
  
 $('#pagoa').DataTable().destroy();
 $('.modal-title-acuotas').text('');
  var prestamoc_id = $(this).attr('id');
  
   //initiate dataTables plugin
      var TableAdelanto1 = 
        $('#pagoa')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        responsive: false,
        processing: true,
        lengthMenu: [ [50, 100, 500, -1 ], [50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        aaSorting: [[ 1, "asc" ]],
        
        ajax:{
          url:"{{route('pagoa')}}",
          type:"get",
          data: {prestamoc_id:prestamoc_id}
              },
        columns: [
          {data:'action',
           name:'action',
           orderable: false
          },
          {data:'d_numero_cuota',
          name:'d_numero_cuota'
          },
          {data:'fecha_cuota',
           name:'fecha_cuota'
          },
          {data:'estado',
          name:'estado'
          },
          {data:'nombres',
          name:'nombres'
          },
          {data:'apellidos',
          name:'apellidos'
          },
          
          {data:'idp',
          name:'idp'
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

 $('.modal-title-acuotas').text('Adelanto de Cuotas');
 $('#modal-acuotas').modal('show');  
  

});


$(document).on('click', '.atrasosp', function(){
  
 $('#atrasosp').DataTable().destroy();
 $('.modal-title-atrasosp').text('');
  var prestamoc_id = $(this).attr('id');
  
   //initiate dataTables plugin
      var TableAtrasosP1 = 
        $('#atrasosp')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        responsive: false,
        processing: true,
        lengthMenu: [ [50, 100, 500, -1 ], [50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        aaSorting: [[ 1, "asc" ]],
        
        ajax:{
          url:"{{route('atrasosp')}}",
          type:"get",
          data: {prestamoc_id:prestamoc_id}
              },
        columns: [
          {data:'action',
           name:'action',
           orderable: false
          },
          {data:'d_numero_cuota',
          name:'d_numero_cuota'
          },
          {data:'fecha_cuota',
           name:'fecha_cuota'
          },
          {data:'estado',
          name:'estado'
          },
          {data:'nombres',
          name:'nombres'
          },
          {data:'apellidos',
          name:'apellidos'
          },
          
          {data:'idp',
          name:'idp'
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

 $('.modal-title-atrasosp').text('Cuotas atrasadas y pendientes');
 $('#modal-atrasosp').modal('show');  
  

});


$(document).on('click', '.pagosr', function(){
  
 $('#registradosp').DataTable().destroy();
 $('.modal-title-registradosp').text('');
  var prestamoc_id = $(this).attr('id');
  
   //initiate dataTables plugin
      var TableAtrasosP = 
        $('#registradosp')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        responsive: false,
        processing: true,
        lengthMenu: [ [50, 100, 500, -1 ], [50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        aaSorting: [[ 1, "asc" ]],
        
        ajax:{
          url:"{{route('pagosrs')}}",
          type:"get",
          data: {prestamoc_id:prestamoc_id}
              },
        columns: [
          {data:'action',
           name:'action',
           orderable: false
          },
          {data:'d_numero_cuota',
          name:'d_numero_cuota'
          },
          {data:'fecha_cuota',
           name:'fecha_cuota'
          },
          {data:'estado',
          name:'estado'
          },
          {data:'nombres',
          name:'nombres'
          },
          {data:'apellidos',
          name:'apellidos'
          },
          
          {data:'idp',
          name:'idp'
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

 $('.modal-title-registradosp').text('Pagos registrados');
 $('#modal-registradosp').modal('show');  
  

});


//Tabla para pago now
$(document).on('click', '.payp', function(){

 $('#pagonow').DataTable().destroy();
 $('.modal-title-pagonow').text('');
  var prestamoc_id = $(this).attr('id');
  
   //initiate dataTables plugin
      var TableAdelanto = 
        $('#pagonow')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        responsive: false,
        processing: true,
        lengthMenu: [ [50, 100, 500, -1 ], [50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        aaSorting: [[ 1, "asc" ]],
        
        ajax:{
          url:"{{route('pagonow')}}",
          type:"get",
          data: {prestamoc_id:prestamoc_id}
              },
        columns: [
          {data:'action',
           name:'action',
           orderable: false
          },
          {data:'d_numero_cuota',
          name:'d_numero_cuota'
          },
          {data:'fecha_cuota',
           name:'fecha_cuota'
          },
          {data:'estado',
          name:'estado'
          },
          {data:'nombres',
          name:'nombres'
          },
          {data:'apellidos',
          name:'apellidos'
          },
          
          {data:'idp',
          name:'idp'
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

 $('.modal-title-pagonow').text('Pago del día');
 $('#modal-pagonow').modal('show');  
  

});
(function($, window) {
    'use strict';

    var MultiModal = function(element) {
        this.$element = $(element);
        this.modalCount = 0;
    };

    MultiModal.BASE_ZINDEX = 1040;

    MultiModal.prototype.show = function(target) {
        var that = this;
        var $target = $(target);
        var modalIndex = that.modalCount++;

        $target.css('z-index', MultiModal.BASE_ZINDEX + (modalIndex * 20) + 10);

        // Bootstrap triggers the show event at the beginning of the show function and before
        // the modal backdrop element has been created. The timeout here allows the modal
        // show function to complete, after which the modal backdrop will have been created
        // and appended to the DOM.
        window.setTimeout(function() {
            // we only want one backdrop; hide any extras
            if(modalIndex > 0)
                $('.modal-backdrop').not(':first').addClass('hidden');

            that.adjustBackdrop();
        });
    };

    MultiModal.prototype.hidden = function(target) {
        this.modalCount--;

        if(this.modalCount) {
           this.adjustBackdrop();
            // bootstrap removes the modal-open class when a modal is closed; add it back
            $('body').addClass('modal-open');
        }
    };

    MultiModal.prototype.adjustBackdrop = function() {
        var modalIndex = this.modalCount - 1;
        $('.modal-backdrop:first').css('z-index', MultiModal.BASE_ZINDEX + (modalIndex * 20));
    };

    function Plugin(method, target) {
        return this.each(function() {
            var $this = $(this);
            var data = $this.data('multi-modal-plugin');

            if(!data)
                $this.data('multi-modal-plugin', (data = new MultiModal(this)));

            if(method)
                data[method](target);
        });
    }

    $.fn.multiModal = Plugin;
    $.fn.multiModal.Constructor = MultiModal;

    $(document).on('show.bs.modal', function(e) {
        $(document).multiModal('show', e.target);
    });

    $(document).on('hidden.bs.modal', function(e) {
        $(document).multiModal('hidden', e.target);
    });
}(jQuery, window));



 


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

