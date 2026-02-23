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

.btn-flotante {
	font-size: 10px; /* Cambiar el tamaño de la tipografia */
	text-transform: uppercase; /* Texto en mayusculas */
	font-weight: bold; /* Fuente en negrita o bold */
	color: #ffffff; /* Color del texto */
	border-radius: 120px; /* Borde del boton */
	letter-spacing: 0.2px; /* Espacio entre letras */
    /*background-color: #e9321e; /* Color de fondo */
	padding: 18px 30px; /* Relleno del boton */
	transition: all 200ms ease 0ms;
	width: 83%;
	box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.5);
	border:none;
    outline:none;
}
.btn-flotante1 {
	font-size: 14px; /* Cambiar el tamaño de la tipografia */
	text-transform: uppercase; /* Texto en mayusculas */
	font-weight: bold; /* Fuente en negrita o bold */
	color: #ffffff; /* Color del texto */
	border-radius: 120px; /* Borde del boton */
	/*background-color: #e9321e; /* Color de fondo */
	padding: 18px 30px; /* Relleno del boton */
	transition: all 200ms ease 0ms;
	box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.5);
	border:none;
    outline:none;
    width: 80%;
}

.btn-flotante2 {
	font-size: 10px; /* Cambiar el tamaño de la tipografia */
	text-transform: uppercase; /* Texto en mayusculas */
	font-weight: bold; /* Fuente en negrita o bold */
	color: #ffffff; /* Color del texto */
	border-radius: 60px; /* Borde del boton */
	/*background-color: #e9321e; /* Color de fondo */
	padding: 2px 2px 2px; /* Relleno del boton */
	transition: all 200ms ease 0ms;
	box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.5);
	border:none;
    outline:none;
    height:50px;
}
.btn-flotante3 {
	font-size: 10px; /* Cambiar el tamaño de la tipografia */
	text-transform: uppercase; /* Texto en mayusculas */
	font-weight: bold; /* Fuente en negrita o bold */
	color: #ffffff; /* Color del texto */
	border-radius: 60px; /* Borde del boton */
	/*background-color: #e9321e; /* Color de fondo */
	padding: 2px 2px 2px; /* Relleno del boton */
	transition: all 200ms ease 0ms;
	box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.5);
	border:none;
    outline:none;
    height:50px;
    width: 40%;
}

.badged {
    font-size: 10px;
    font-weight: 400;
    position: absolute;
    left: -12px;
    top: -10px;
}

.badged {
    display: inline-block;
    padding: .25em .4em;
    font-size: 100%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

.letra {
	font-size: 11px; /* Cambiar el tamaño de la tipografia */
	font-weight: bold; /* Fuente en negrita o bold */
	color: #070cad; /* Color del texto */
}

</style>
@endsection


@section('scripts')
<!-- jQuery ui -->

<script src="{{asset("assets/pages/scripts/admin/pagocalender/index.js")}}" type="text/javascript"></script> 
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
     <div class="card card-primary card-tabs">
    <nav class="main-header navbar navbar-light">
                 <!-- Left navbar links -->
        
         

    <div class="card-header p-0 pt-1">
        
              <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                 <ul class="navbar-nav">
                <li class="nav-item">
                 <button class="btn btn-primary" data-widget="pushmenu"><i class="fas fa-bars"></i></button>
                </li>
                 
                </ul>      
                <li class="nav-item">
                  <a class="nav-link active letra" 
                  id="custom-tabs-one-datos-del-pago-tab" 
                  data-toggle="pill" 
                  href="#custom-tabs-one-datos-del-pago" 
                  role="tab" 
                  aria-controls="custom-tabs-one-datos-del-pago" 
                  aria-selected="false">Pagos</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link letra" 
                  id="custom-tabs-one-prestamos-tab" 
                  data-toggle="pill" 
                  href="#custom-tabs-one-prestamos" 
                  role="tab" 
                  aria-controls="custom-tabs-one-prestamos" 
                  aria-selected="false">Prestamos</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link letra" 
                  id="custom-tabs-one-clientes-tab" 
                  data-toggle="pill" 
                  href="#custom-tabs-one-clientes" 
                  role="tab" 
                  aria-controls="custom-tabs-one-clientes" 
                  aria-selected="false">Clientes</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link letra" 
                  id="custom-tabs-one-anulados-tab" 
                  data-toggle="pill" 
                  href="#custom-tabs-one-anulados" 
                  role="tab" 
                  aria-controls="custom-tabs-one-anulados" 
                  aria-selected="false">Anulados</a>
                </li>
                
                </ul>
    </div>
    </nav>
      <div class="tab-content" id="custom-tabs-one-tabContent">
                <div class="tab-pane fade active show" id="custom-tabs-one-datos-del-pago" role="tabpanel" aria-labelledby="custom-tabs-one-datos-del-pago-tab">
                    
                      @include('admin.pago_card.tab-pago')
                  
                </div>
                 <div class="tab-pane fade" id="custom-tabs-one-prestamos" role="tabpanel" aria-labelledby="custom-tabs-one-prestamos-tab">
                 
                   
                       @include('admin.pago_card.tab-prestamos')
                               
                     
                </div>
              
                <div class="tab-pane fade" id="custom-tabs-one-clientes" role="tabpanel" aria-labelledby="custom-tabs-one-clientes-tab">
                 
                   
                        @include('admin.pago_card.tab-clientes')
                           

                </div>
                
                 <div class="tab-pane fade" id="custom-tabs-one-anulados" role="tabpanel" aria-labelledby="custom-tabs-one-anulados-tab">
                 
                   
                        @include('admin.pago_card.tab-anulados')
                           

                </div>

               

              

               </div>
    
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
                        @include('admin.pago_card.form-pago')
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
                
              <h6 class="modal-title-dp"></h6>
              <div class="btn-group center" id="btnar">
                
                 
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

        <form id="form-general1" name="form-general" class="form-horizontal" method="post">
          @csrf
          <div class="card-body">
                        @include('admin.pago_card.form-prestamo')
          </div>
          <!-- /.card-body -->
                       <div class="card-footer">
                          
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                            @include('includes.boton-form-crear-prestamocard')    
                        </div>
                         </div>
          <!-- /.card-footer -->
        </form>
                   
      
         
    </div>
  </div>
</div>
</div>
</div>

<!--  Modal de refinanciar -->
<div class="modal fade" tabindex="-1" id="modal-refi"  role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">
     
  <div class="row">
      <div class="col-lg-12">
        <div class="loader col-lg-12"><img src="{{asset("assets/$theme/dist/img/loader6.gif")}}" class="" /> </div>
        @include('includes.form-error')
        @include('includes.form-mensaje')
         <div class="card card-olive">
          <div class="card-header">
              <h6 class="modal-title-refi"></h6>
            <div class="card-tools pull-right">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
                
          </div>   
                <span id="form_result"></span>
         </div>

        <form id="form-generalrefi" name="form-general" class="form-horizontal" method="post">
          @csrf
          <div class="card-body">
                        @include('admin.pago_card.form-refi')
          </div>
          <!-- /.card-body -->
                       <div class="card-footer">
                          
                            <div class="col-lg-3"></div>
                            <div class="col-lg-6">
                            @include('includes.boton-form-crear-refinanciarcard')    
                        </div>
                         </div>
          <!-- /.card-footer -->
        </form>
                   
      
             
     </div>
   </div>
   </div>
   </div>
</div>


<!-- /.Modal crear cliente -->



    <div class="modal fade" tabindex="-1" id ="modal-u-cli"  role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
           
        <div class="row">
            <div class="col-lg-12">
              @include('includes.form-error')
              @include('includes.form-mensaje')
               <div class="card card-info">
                <div class="card-header">
                     <h6 class="modal-title-cli"></h6>
                  <div class="card-tools pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                      
                    </div>   
                      <span id="form_result1"></span>
                  </div>

              <form id="form-generalcli" name="form-general" class="form-horizontal" method="post">
                @csrf
                <div class="card-body">
                              @include('admin.pago_card.form-cli')
                </div>
                <!-- /.card-body -->
                             <div class="card-footer">
                                
                                  <div class="col-lg-3"></div>
                                  <div class="col-lg-6">
                                  @include('includes.boton-form-crear-cli')    
                              </div>
                               </div>
                <!-- /.card-footer -->
              </form>
                         
            
               
          </div>
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
     
     
$("#cliente_id").select2();




        //initiate dataTables
    var datatable = $('#clientecard').DataTable({
        language: idioma_espanol,
        processing: true,
        lengthMenu: [ [10, 25, 50, 100, 500, -1 ], [10, 25, 50, 100, 500, "Mostrar Todo"] ],
        processing: true,
        serverSide: true,
        aaSorting: [[ 1, "asc" ]],
        ajax:{
          url:"{{route('cliente_card')}}",
              },
        columns: [
          {data:'datos',
           name:'datos',
           orderable: false
          },
          {data:'consecutivo',
          name:'consecutivo'
          }
                   
        ],

         //Botones----------------------------------------------------------------------
         
         "dom":'<"row"<"col-xs-1 form-inline"><"col-md-4 form-inline"l><"col-md-5 form-inline"f><"col-md-3 form-inline">>rt<"row"<"col-md-8 form-inline"i> <"col-md-4 form-inline"p>>',
         
         

                 
        "columnDefs": [
                                     { "visible": false,  "targets": [1] }
                      ]


        
    
        });


$('#create_cliente').click(function(){
  $('#form-generalcli')[0].reset();
  $('.modal-title-cli').text('Agregar Nuevo cliente');
  $('#action_button_cli').val('Add');
  $('#action_cli').val('Add');
  $('#form_result1').html('');
  $('#modal-u-cli').modal('show');
 });

$('#form-generalcli').on('submit', function(event){
    event.preventDefault(); 
    var url = '';
    var method = '';
    var text = '';

  if($('#action_cli').val() == 'Add')
  {
    text = "Estás por crear un cliente"
    url = "{{route('guardar_cliente')}}";
    method = 'post';
  }  

  if($('#action_cli').val() == 'Edit')
  {
    text = "Estás por actualizar un cliente"
    var updateid = $('#hidden_id_cli').val();
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
                         for (var count = 0; count < data.errors.length; count++)
                    {
                        Swal.fire(
                        {
                          icon: 'error',
                          title: data.errors[count],
                          showConfirmButton: false,
                          timer: 1500
                          
                        }
                      )
                      
                    }
                        

                   /* html = '<div class="alert alert-danger alert-dismissible" data-auto-dismiss="3000">'
                      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'
                        '<h5><i class="icon fas fa-check"></i> Mensaje Ventas</h5>';
                                     
                    for (var count = 0; count < data.errors.length; count++)
                    {
                      html += '<p>' + data.errors[count]+'<p>';
                    }         
                    html += '</div>';*/
                    }
                    if(data.success == 'ok') {
                      $('#form-generalcli')[0].reset();
                      $('#modal-u-cli').modal('hide');
                      $('#clientecard').DataTable().ajax.reload();
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
                      $('#form-generalcli')[0].reset();
                      $('#modal-u-cli').modal('hide');
                      $('#clientecard').DataTable().ajax.reload();
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
                    $('#form_result1').html(html)  
              }


           });
          }
        });
          

  });


 

// Edición de cliente

 $(document).on('click', '.editcli', function(){
    var id = $(this).attr('id');
    
  $.ajax({
    url:"cliente/"+id+"/editar",
    dataType:"json",
    success:function(data){
      $('#nombrescli').val(data.result.nombres);
      $('#apellidoscli').val(data.result.apellidos);
      $('#tipo_documentocli').val(data.result.tipo_documento);
      $('#documentocli').val(data.result.documento);
      $('#paiscli').val(data.result.pais);
      $('#estadocli').val(data.result.estado);
      $('#ciudadcli').val(data.result.ciudad);
      $('#barriocli').val(data.result.barrio);
      $('#sectorcli').val(data.result.sector);
      $('#direccioncli').val(data.result.direccion);
      $('#celularcli').val(data.result.celular);
      $('#telefonocli').val(data.result.telefono);
      $('#consecutivocli').val(data.result.consecutivo);
      $('#observacioncli').val(data.result.observacion_cli);
      $('#usuario_idcli').val(data.result.usuario_id);
      $('#activocli').val(data.result.activo);
      $('#hidden_id_cli').val(id);
      $('.modal-title-cli').text('Editar Cliente');
      $('#action_button_cli').val('Edit');
      $('#action_cli').val('Edit');
      $('#modal-u-cli').modal('show');
     
    }
    

  }).fail( function( jqXHR, textStatus, errorThrown ) {

if (jqXHR.status === 403) {

  Manteliviano.notificaciones('No tienes permisos para realizar esta accion', 'Sistema Ventas', 'warning');

}});

 });

   //Calculo de monto total diario, semanal, quincenal y mensual al realizar cualqiuier cambio en los input

function montop(){
  
  if( $('#tipo_pagop').val() == "Diario"){

    $('#monto_totalp').val(Math.round(parseFloat($("#montop").val()) +
     parseFloat((($("#montop").val() * ($("#interes").val()/100)) * ($("#cuotas").val()/$("#cuotas").val())))));
     $('#monto_pendientep').val($("#monto_totalp").val());
     $('#valor_cuotap').val(Math.round( $('#monto_totalp').val()/$("#cuotas").val()));

    }else if( $('#tipo_pagop').val() == "Mensual"){

      $('#monto_totalp').val(parseFloat($("#montop").val()) +
      parseFloat((($("#montop").val() * ($("#interes").val()/100)) * $("#cuotas").val())));
      $('#monto_pendientep').val($("#monto_totalp").val());

      $('#valor_cuotap').val(Math.round( $('#monto_totalp').val()/$("#cuotas").val()));


    }else if( $('#tipo_pagop').val() == "Quincenal"){

    $('#monto_totalp').val(Math.round(parseFloat($("#montop").val()) +
    parseFloat((($("#montop").val() * ($("#interes").val()/100)) * ($("#cuotas").val()/$("#cuotas").val())))));
    
    $('#monto_pendientep').val($("#monto_totalp").val());

    $('#valor_cuotap').val(Math.round( $('#monto_totalp').val()/$("#cuotas").val()));


    }else if( $('#tipo_pagop').val() == "Semanal"){

    $('#monto_totalp').val(Math.round(parseFloat($("#montop").val()) +
    parseFloat((($("#montop").val() * ($("#interes").val()/100)) * ($("#cuotas").val()/$("#cuotas").val())))));
    $('#monto_pendientep').val($("#monto_totalp").val());

    $('#valor_cuotap').val(Math.round( $('#monto_totalp').val()/$("#cuotas").val()));


    }

    
}

 $("#cuotas").change(montop);
 $("#interes").change(montop); 
 $("#montop").change(montop);
 $("#tipo_pagop").change(montop);

// funcion Cuota------------------------------------------------------------------------

function cuota(){

 if( $('#monto_totalp').val() > 0){

  }   
}

$("#interes").change(cuota); 






     
//Crear prestamos
 

$('#create_prestamo').click(function(){
      $('#form-general1')[0].reset();
      $('.modal-title-pc').text('Crear prestamo');
      $('#action_button1').val('Add');
      $('#action1').val('Add');
      $('#form_result').html('');
      $('#modal-pc').modal('show');
        
    
      $('#form-general1').on('submit', function(event){
        event.preventDefault(); 
       
        if($('#action1').val() == 'Add')
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
                          $('#prestamos').DataTable().ajax.reload();
                          Swal.fire(
                            {
                              icon: 'success',
                              title: 'prestamo agregado correctamente',
                              showConfirmButton: false,
                              timer: 1500
                              
                            }
                          )
                          // Manteliviano.notificaciones('prestamo agregado correctamente', 'Sistema Ventas', 'success');
                          
                        }else if(data.success != 'ok'){
                            
                            
                           Swal.fire(
                            {
                              icon: 'warning',
                              title: 'Revisar datos del prestamo',
                              showConfirmButton: false,
                              timer: 1500
                              
                            }
                          ) 
                            
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
 

      //initiate dataTables plugin


        var datatable = 
        $('#prestamos')
        //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
        .DataTable({
        language: idioma_espanol,
        responsive: false,
        processing: true,
        lengthMenu: [ [10, 25, 50, 100, 500, -1 ], [10, 25, 50, 100, 500, "Mostrar Todo"] ],
        serverSide: true,
        aaSorting: [[ 2, "asc" ]],
        
        ajax:{
          url:"{{ route('pagoccp')}}",
          type:"get"
        },
        columns: [
          {data:'action',
           name:'action',
           orderable: false
          },
          {data:'datos',
           name:'datos',
           orderable: false
          },
          {data:'consecutivo',
          name:'consecutivo'
          }
        ],
        "columnDefs": [
                                     { "visible": false,  "targets": [2] }
                      ],

         //Botones----------------------------------------------------------------------
         
         "dom":'<"row"<"col-xs-1 form-inline"><"col-md-4 form-inline"l><"col-md-5 form-inline"f><"col-md-3 form-inline">>rt<"row"<"col-md-8 form-inline"i> <"col-md-4 form-inline"p>>',
         
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
        lengthMenu:  [ [10, 25, 50, 100, 500, -1 ], [10, 25, 50, 100, 500, "Mostrar Todo"] ],
        serverSide: true,
        aaSorting: [[ 2, "asc" ]],
        
        ajax:{
          url:"{{ route('pagocc')}}",
          type:"get",
          data: {estado_pago:estado_pago, prestamoc_id:prestamoc_id}
              },
        columns: [
          {data:'action',
           name:'action',
           orderable: false
          },
          {data:'datos',
           name:'datos',
           orderable: false
          },
          {data:'consecutivo',
          name:'consecutivo'
          }
        ],
        "columnDefs": [
                                     { "visible": false,  "targets": [2] }
                      ],

         //Botones----------------------------------------------------------------------
         
         "dom":'<"row"<"col-xs-1 form-inline"><"col-md-4 form-inline"l><"col-md-5 form-inline"f><"col-md-3 form-inline">>rt<"row"<"col-md-8 form-inline"i> <"col-md-4 form-inline"p>>',
         
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
                          $('#prestamos').DataTable().ajax.reload();
                          
                        
                          
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
                         // $('#registradosp').DataTable().ajax.reload();
                        
                           $('#prestamos').DataTable().ajax.reload();
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
                                           $('#prestamos').DataTable().ajax.reload();
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
                                         $('#prestamos').DataTable().ajax.reload();
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
                                         $('#prestamos').DataTable().ajax.reload();
                                        
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
                                         $('#prestamos').DataTable().ajax.reload();
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
                                        
                          
                                    }else if(data.success == 'abonoa') {
                                        $('#form-general')[0].reset();
                                        $('#modal-pd').modal('hide');
                                        $('#pago').DataTable().ajax.reload();
                                         $('#prestamos').DataTable().ajax.reload();
                                        Swal.fire(
                                          {
                                            icon: 'info',
                                            title: 'Pago adicionado a la cuota atrasada',
                                            showConfirmButton: true,
                                            timer: 2500
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
    
    $('.detalle').prop('disabled', true);
    
    $("#detalleCuota").empty();
    $("#detalles").empty();
    $("#btnar").empty();
    $.ajax({
    url:"prestamopn/"+id+"",
    dataType:"json",
    success:function(data){
      $.each(data.result, function(i, item){
         $("#btnar").append( 
        '<button type="button" id="'+item.idp+'" class="anular btn btn-flotante2  btn-app bg-danger btn-sm" ><i class="fas fa-power-off"></i><span class="badged bg-success">Credito:'+item.idp+'</span>Anular</button>'+
        '<button type="button" id="'+item.idp+'" idc="'+item.id+'" class="refinanciarp btn btn-flotante2  btn-app bg-gradient-warning btn-sm" ><i class="fas fa-money-bill-wave-alt"></i>Refinanciar</button>'+
        '<button type="button" class="btn btn-flotante2 btn-app bg-gradient-primary btn-sm" data-dismiss="modal"><i class="fas fa-window-close"></i>Close</button>'
          )
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
    $('.modal-title-dp').text('');
    $('#modal-dp').modal('show');
    $('.detalle').prop('disabled', false);
    }
    
  });
});

//Anular Prestamo

$(document).on('click', '.anular', function(){
    var id = $(this).attr('id');
    
Swal.fire({
     title: "¿Estás por anular un prestamo?",
     text: "Esta seguro?",
     icon: "warning", 
     showCancelButton: true,
     showCloseButton: true,
     confirmButtonText: 'Aceptar',
     }).then((result)=>{
    if(result.value){  
        Swal.fire({
                title: 'Espere por favor !',
                html: 'Realizando la anulación',// add html attribute if you want or remove
                showConfirmButton: false,
                allowOutsideClick: false,
                willOpen: () => {
                    Swal.showLoading()
                },
            }), 
    $.ajax({
           url:"anularp/"+id+"",
           method:"put",
           data:{"_token": $("meta[name='csrf-token']").attr("content")},
           dataType:"json",
           success:function(data){
                    if(data.success == 'ok') {
                      $('#modal-dp').modal('hide');
                      $('#prestamos').DataTable().ajax.reload();
                      $('#pago').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'success',
                          title: 'Prestamo anulado',
                          showConfirmButton: false,
                          timer: 1500
                          
                        }
                      )
                        
                    }else{
                      $('#modal-dp').modal('hide');
                      $('#prestamos').DataTable().ajax.reload();
                      $('#pago').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'warning',
                          title: 'Prestamo no se anulo',
                          showConfirmButton: false,
                          timer: 1500
                          
                        }
                      )
                      
                    } 
                   
              }


           });
          }
        });

 });
 
 
 
 
 
   //Calculo de monto total diario, semanal, quincenal y mensual al realizar cualqiuier cambio en los input

function montop_refi(){
  
  if( $('#tipo_pagop_refi').val() == "Diario"){

    $('#monto_totalp_refi').val(Math.round(parseFloat($("#montop_refi").val()) +
     parseFloat((($("#montop_refi").val() * ($("#interes").val()/100)) * ($("#cuotas_refi").val()/$("#cuotas_refi").val())))));
     $('#monto_pendientep_refi').val($("#monto_totalp_refi").val());
     $('#valor_cuotap_refi').val(Math.round( $('#monto_totalp_refi').val()/$("#cuotas_refi").val()));

    }else if( $('#tipo_pagop_refi').val() == "Mensual"){

      $('#monto_totalp_refi').val(parseFloat($("#montop_refi").val()) +
      parseFloat((($("#montop_refi").val() * ($("#interes_refi").val()/100)) * $("#cuotas_refi").val())));
      $('#monto_pendientep_refi').val($("#monto_totalp_refi").val());

      $('#valor_cuotap_refi').val(Math.round( $('#monto_totalp_refi').val()/$("#cuotas_refi").val()));


    }else if( $('#tipo_pagop_refi').val() == "Quincenal"){

    $('#monto_totalp_refi').val(Math.round(parseFloat($("#montop_refi").val()) +
    parseFloat((($("#montop_refi").val() * ($("#interes_refi").val()/100)) * ($("#cuotas_refi").val()/$("#cuotas_refi").val())))));
    
    $('#monto_pendientep_refi').val($("#monto_totalp_refi").val());

    $('#valor_cuotap_refi').val(Math.round( $('#monto_totalp_refi').val()/$("#cuotas_refi").val()));


    }else if( $('#tipo_pagop_refi').val() == "Semanal"){

    $('#monto_totalp_refi').val(Math.round(parseFloat($("#montop_refi").val()) +
    parseFloat((($("#montop_refi").val() * ($("#interes_refi").val()/100)) * ($("#cuotas_refi").val()/$("#cuotas_refi").val())))));
    $('#monto_pendientep_refi').val($("#monto_totalp_refi").val());

    $('#valor_cuotap_refi').val(Math.round( $('#monto_totalp_refi').val()/$("#cuotas_refi").val()));


    }

    
}

 $("#cuotas_refi").change(montop_refi);
 $("#interes_refi").change(montop_refi); 
 $("#montop_refi").change(montop_refi);
 $("#tipo_pagop_refi").change(montop_refi);

// funcion Cuota------------------------------------------------------------------------

function cuota_refi(){

 if( $('#monto_totalp_refi').val() > 0){

  }   
}

$("#interes_refi").change(cuota); 



//Refinanciar prestamo


$(document).on('click', '.refinanciarp', function(){
        var id = $(this).attr('id');
        var idc = $(this).attr('idc');
       
      $.ajax({
        url:"refinanciar/"+id+"/prestamo",
        data:idc,
        dataType:"json",
        success:function(data){
          $.each(data.result, function(i, items){
          $('#cliente_refi').val(items.nombres+' '+items.apellidos);
          $('#prestamo_id_refi').val(items.idp);
          $('#cliente_id_refi').val(items.id);
          $('#monto_pendiente_refi').val(items.monto_pendiente);
          $('#monto_pendiente_enviar').val(items.monto_total);
          $('#valor_abono_refi').val(items.monto_pendiente);
          $('#numero_cuota_refi').val(items.d_numero_cuota);
          $('#fecha_pago_refi').val(items.fecha_cuota);
          $('#sync_refi').val('1');
          $('#abono_refi').val('1');
          $('#monto_refi').val(items.monto);
          $('#monto_entregar').val(parseFloat(items.monto) - parseFloat(items.monto_pendiente));
          $('#cuotas_refi').val(items.cuotas);
          $('#interes_refi').val(items.interes);
          $('#monto_totalp_refi').val(items.monto_total);
          $('#valor_cuotap_refi').val(items.valor_cuota);
          $('#tipo_pagop_refi').val(items.tipo_pago);
          $('#activo_Refi').val('1');
          $('#hidden_idrefi').val(items.usuario_id);
          $('#usuario_idrefi').val(items.usuario_id);
          $('.modal-title-refi').text('Refinanciar prestamo');
          $('#action_buttonrefi').val('Add');
          $('#actionrefi').val('Add');
          $('#modal-dp').modal('hide');
          $('#modal-refi').modal('show');
                  
        });
        }
        
    
      }).fail( function( jqXHR, textStatus, errorThrown ) {
    
    if (jqXHR.status === 403) {
    
      Manteliviano.notificaciones('No tienes permisos para realizar esta accion', 'Sistema Ventas', 'warning');
    
    }});
    
     });

 
  $('#form-generalrefi').on('submit', function(event){
    event.preventDefault(); 
   
    if($('#actionrefi').val() == 'Add')
  {
    urlrefi = "{{route('guardar_prestamorefi')}}";
    methodrefi = 'post';
  }
    $.ajax({
          beforeSend: function(){ 
          $('.loader').css("visibility", "visible"); },
           url:urlrefi,
           method:methodrefi,
           data:$(this).serialize(),
           dataType:"json",
           success:function(data){
            if(data.success == 'ok') {
                      $('#form-generalrefi')[0].reset();
                      $('#modal-refi').modal('hide');
                      $('#prestamos').DataTable().ajax.reload();
                      Swal.fire(
                        {
                          icon: 'success',
                          title: 'Prestamo refinanciado',
                          showConfirmButton: false,
                          timer: 1500
                          
                        }
                      )
                      // Manteliviano.notificaciones('prestamo agregado correctamente', 'Sistema Ventas', 'success');
                      
                    }else if(data.success != 'ok'){
                        
                        
                       Swal.fire(
                        {
                          icon: 'warning',
                          title: 'Revisar datos del prestamo',
                          showConfirmButton: false,
                          timer: 1500
                          
                        }
                      ) 
                        
                    }
  
     
          },
          complete: function(){ 
          $('.loader').css("visibility", "hidden");
          }

            });
      
      });



// Mostrar cuotas a adelantar

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
        lengthMenu: [ [10, 50, 100, 500, -1 ], [10, 50, 100, 500, "Mostrar Todo"] ],
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

// Tabla atrasos

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
        lengthMenu: [ [10, 50, 100, 500, -1 ], [10, 50, 100, 500, "Mostrar Todo"] ],
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
        lengthMenu: [ [10, 50, 100, 500, -1 ], [10, 50, 100, 500, "Mostrar Todo"] ],
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
            lengthMenu: [ [10, 50, 100, 500, -1 ], [10, 50, 100, 500, "Mostrar Todo"] ],
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
   
   
// Funcion multimodal
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

