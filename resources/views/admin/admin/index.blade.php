@extends("theme.$theme.layout")

@section('titulo')
    Dashboard   
@endsection

@section("styles")
<link href="{{asset("assets/$theme/plugins/datatables-bs4/css/dataTables.bootstrap4.css")}}" rel="stylesheet" type="text/css"/>       

@endsection


@section('scripts')
<script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
@endsection

@section('contenido')
<div class="content-wrapper" style="min-height: 543px;">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
          </div><!-- /.col -->
          
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">  {{-- 4 Widgets  --}}
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h5>EMPLEADOS</h5>

                <p>Listar empleados</p>
              </div>
              <div class="icon">
                <i class="fas fa-motorcycle"></i>
              </div>
            <a href="{{route('empleado')}}" class="small-box-footer">Listar empleados <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h5>CLIENTES<sup style="font-size: 20px"></sup></h5>

                <p>Listar clientes</p>
              </div>
              <div class="icon">
                <i class="fas fa-handshake"></i>
              </div>
              <a href="{{route('cliente')}}" class="small-box-footer">Listar clientes <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h5>PRESTAMOS</h5>

                <p>Listar prestamos</p>
              </div>
              <div class="icon">
                <i class="fas fa-money-bill-alt"></i>
              </div>
              <a href="{{route('prestamo')}}" class="small-box-footer">Listar prestamos<i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h5>RUTAS</h5>

                <p>Listar ruta Card</p>
              </div>
              <div class="icon">
                <i class="fas fa-route"></i>
              </div>
              <a href="{{route('pagoccp')}}" class="small-box-footer">Listar Ruta Card <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        
      </div><!-- /.container-fluid -->
      <div class="container-fluid"> {{-- 1 Widgets  --}}
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-6 col-12">
            <!-- small box -->
            <div class="small-box bg-warning">

             
              <div class="inner">
               
                <h4>CARTERA A COBRAR DIARIA DE RUTA</h4>
                @foreach ($datas as $item)
                 <p><h5>Total a cobrar : <i class="fas fa-dollar-sign"></i> {{ $item->cobro}}</h5></p>
                 @foreach ($data as $item1)
                 <p><h5>Total cobrado : <i class="fas fa-dollar-sign"></i> {{ $item1->cobrado}}</h5></p>
                 @foreach ($dataa as $itema)
                 <p><h5>Total atrasado : <i class="fas fa-dollar-sign"></i> {{ $itema->atrasado}}</h5></p>
                 @endforeach
                 @endforeach
                 <p><h5>Clientes a cobrar: {{ $item->cobros}}</h5></p>
                 <p><h5>Clientes pendientes de cobro: {{ $item->pendiente_cobros}}</h5></p>
                 <p><h5>Clientes cobrados: {{ $item->pagados}}</h5></p>
                 <p><h5>Clientes con atrasos: {{ $item->atrasos}}</h5></p>
                @endforeach
              </div>
             
              <div class="icon">
                <i class="fas fa-money-bill-alt"></i>
              </div>
            <a href="#" class="small-box-footer"><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-6 col-12">
            <!-- small box -->
            @if(session()->get('rol_id') == 1 || session()->get('rol_id') == 2)
            <div class="small-box bg-success">
              
              <div class="inner">
                <h4>CARTERA A COBRAR DIARIA DE EMPLEADOS</h4>
                @foreach ($datast as $item)
                 <p><h5>Total a cobrar : <i class="fas fa-dollar-sign"></i> {{ $item->cobro}}</h5></p>
                 @foreach ($datat as $item1)
                 <p><h5>Total cobrado : <i class="fas fa-dollar-sign"></i> {{ $item1->cobrado}}</h5></p>
                 <p><h5>Total atrasado : <i class="fas fa-dollar-sign"></i> {{ $item->cobro - $item1->cobrado}}</h5></p>
                 @endforeach
                 <p><h5>Clientes a cobrar: {{ $item->cobros}}</h5></p>
                 <p><h5>Clientes pendientes de cobro: {{ $item->pendiente_cobros}}</h5></p>
                 <p><h5>Clientes cobrados: {{ $item->pagados}}</h5></p>
                 <p><h5>Clientes con atrasos: {{ $item->atrasos}}</h5></p>
                @endforeach
              </div>
            
              <div class="icon">
                <i class="fas fa-handshake"></i>
              </div>
              <a href="#" class="small-box-footer"><i class="fas fa-arrow-circle-right"></i></a>
            </div>
            @endif  
          </div>
          <!-- ./col -->
         
        </div>
        <!-- /.row -->
        <!-- Main row -->
        
      </div><!-- /.container-fluid -->
      
    </section>
    <!-- /.content -->
  </div>
 

@endsection

@section("scriptsPlugins")
<script src="{{asset("assets/$theme/plugins/datatables/jquery.dataTables.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/$theme/plugins/datatables-bs4/js/dataTables.bootstrap4.js")}}" type="text/javascript"></script>

<script>
  $(document).ready(function() {

         fill_datatable();   

          function fill_datatable(periodo1 = '', zona1 = '' )
         {
          var datatable = $('#tablero').DataTable({
              language: idioma_espanol,
              lengthMenu: [ -1],
              processing: true,
              serverSide: true,
              
                  
          ajax:{
                url:"{{ route('tablero')}}",
                data:{periodo1:periodo1, zona1:zona1}
              },
              columns: [
                {
                    data:'Ciclo',
                    name:'Ciclo'
                },
                {
                    data:'Periodo',
                    name:'Periodo'
                },
                {
                  data:'nombreu'
                 
                },
                {
                  data:'Usuario'
                 
                },
                {
                    data:'idDivision',
                    name:'idDivision'
                },
                {
                    data:'Asignados',
                    name:'Asignados'
                },
                {
                    data:'Pendientes',
                    name:'Pendientes'
                },
                {
                    data:'Ejecutadas',
                    name:'Ejecutadas'
                },
                 {
                    data:'Altos',
                    name:'Altos'
                },
                {
                    data:'Bajos',
                    name:'Bajos'
                },
                {
                    data:'Negativo',
                    name:'Negativo'
                },
                {
                    data:'Consumo_cero',
                    name:'Consumo_cero'
                },
                {
                    data:'Normales',
                    name:'Normales'
                },
                {
                    data:'Causados',
                    name:'Causados'
                },
                
                {
                    data:'inicio',
                    name:'inicio'
                },
                {
                    data:'Final',
                    name:'Final'
                }

              ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
  
            
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
  
            
           asignadas = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            
            pendientes = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            
            ejecutadas = api
                .column( 7, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            altos = api
                .column( 8, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            
            bajos = api
                .column( 9, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            
            negativo = api
                .column( 10, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
            consumo_cero = api
                .column( 11, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            
            normales = api
                .column( 12, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            
            causados = api
                .column( 13, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );    
            
                      
                     
  
            
            $( api.column( 5 ).footer() ).html(
                asignadas 
            );
            $( api.column( 6 ).footer() ).html(
                pendientes 
            );
            $( api.column( 7 ).footer() ).html(
                ejecutadas 
            );
            $( api.column( 8 ).footer() ).html(
                altos 
            );
            $( api.column( 9 ).footer() ).html(
                bajos 
            );
            $( api.column( 10 ).footer() ).html(
                negativo 
            );
            $( api.column( 11 ).footer() ).html(
                consumo_cero 
            );
            $( api.column( 12 ).footer() ).html(
                normales 
            );
            $( api.column( 13 ).footer() ).html(
                causados 
            );
                
                
                
           
        },
              //Botones----------------------------------------------------------------------
        "dom":'Bfrtip',
               buttons: [
                   {

               extend:'copyHtml5',
               titleAttr: 'Copy',
               className: "btn btn-info"


                  },
                  {

               extend:'excelHtml5',
               titleAttr: 'Excel',
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
               ]
             });
}    

    
        
      $('#buscar').click(function(){

       var periodo1 = $('#periodo1').val();
       var zona1 = $('#zona1').val();

        if(periodo1 != '' && zona1 != ''){

            $('#tablero').DataTable().destroy();
            fill_datatable(periodo1, zona1);

        }else{
        
             swal({
            title: 'Debes digitar periodo y zona',
            icon: 'warning',
            buttons:{
                cancel: "Cerrar"
                
                    }
              })
        }
        
    });        

      $('#reset').click(function(){
        $('#zona1').val('');
        $('#periodo1').val('');
        $('#tablero').DataTable().destroy();
        fill_datatable();
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
                } ;
                
           
  
         
  </script>
  


@endsection
