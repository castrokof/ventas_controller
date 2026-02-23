@extends("theme.$theme.layout")

@section('titulo')
    Informes   
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





<div class="content-wrapper col-mb-12" style="min-height: 543px;" >
    <!-- Content Header (Page header) -->
<div class="row">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-12">
          <div class="col-sm-12">
            <h1 class="m-0 text-dark">Informes</h1>
          </div><!-- /.col -->
                       
          @csrf
          <div class="card-body">
          <div class="row col-lg-12">  
            
            @include('admin.admin.form')
            
          </tr>
          </td> 
          </div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
  </div>
</div>
    <!-- /.content-header -->

    <!-- Main content -->
<section class="content">
      <div class="container-fluid"> 
        <div class="row">
          <div class="col-lg-3 col-6" id="detalle">
          </div>
          <div class="col-lg-3 col-6" id="detalle1">
          </div>
          <div class="col-lg-3 col-6" id="detalle2">
          </div>
          <div class="col-lg-3 col-6" id="detalle3">
          </div>
        
        </div>
      </div>
      
      <div class="row">
        <div class="col-12">
          <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
              <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" 
                  id="custom-tabs-one-datos-del-pago-tab" 
                  data-toggle="pill" 
                  href="#custom-tabs-one-datos-del-pago" 
                  role="tab" 
                  aria-controls="custom-tabs-one-datos-del-pago" 
                  aria-selected="false">Pagos</a>
                </li>
                <!--<li class="nav-item">
                  <a class="nav-link" 
                  id="custom-tabs-one-atrasos-tab" 
                  data-toggle="pill" 
                  href="#custom-tabs-one-atrasos" 
                  role="tab" 
                  aria-controls="custom-tabs-one-atrasos" 
                  aria-selected="false">Atrasos</a>
                </li>-->
                <li class="nav-item">
                  <a class="nav-link" 
                  id="custom-tabs-one-prestamos-tab" 
                  data-toggle="pill" 
                  href="#custom-tabs-one-prestamos" 
                  role="tab" 
                  aria-controls="custom-tabs-one-prestamos" 
                  aria-selected="false">Prestamos</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" 
                  id="custom-tabs-one-gastos-tab" 
                  data-toggle="pill" 
                  href="#custom-tabs-one-gastos" 
                  role="tab" 
                  aria-controls="custom-tabs-one-gastos" 
                  aria-selected="false">Gastos</a>
                </li>
                
                </ul>
            </div>
           
              <div class="tab-content" id="custom-tabs-one-tabContent">
                <div class="tab-pane fade active show" id="custom-tabs-one-datos-del-pago" role="tabpanel" aria-labelledby="custom-tabs-one-datos-del-pago-tab">
                
                  <form  id="form-general" class="form-horizontal" method="POST">
                      @csrf      
                      @include('admin.admin.form-pagos')
                  
                </div>
                                
                <!--<div class="tab-pane fade" id="custom-tabs-one-atrasos" role="tabpanel" aria-labelledby="custom-tabs-one-atrasos-tab">
                  <div class="card-body">
                    
                      @include('admin.admin.form-atrasos')
                    
                   </div>
                </div>-->

                <div class="tab-pane fade" id="custom-tabs-one-prestamos" role="tabpanel" aria-labelledby="custom-tabs-one-prestamos-tab">
                 
                   
                       @include('admin.admin.form-prestamos')
                               
                     
                </div>
              
                <div class="tab-pane fade" id="custom-tabs-one-gastos" role="tabpanel" aria-labelledby="custom-tabs-one-gastos-tab">
                 
                   
                        @include('admin.admin.form-gastos')
                               
                   
                  
                    </form>
                </div>

               

              

               </div>
          
            <!-- /.card -->
          </div>
        </div>
        
      </div>   
</section>
    <!-- /.content -->

</div>

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
        
   
      <!--tabla -->
          <div  class="card-body table-responsive p-2">
            
          <table id="detallePagos" class="table table-hover  text-nowrap  table-striped table-bordered"  style="width:100%">  
                
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

<script>
  $(document).ready(function() {

fill_datatable();
fill_datatable1();
fill_datatable2();
fill_datatable3();  

 function fill_datatable(fechaini = '', fechafin = '', usuario = '' )
         {
          var datatable = $('#tpago').DataTable
          ({
              language: idioma_espanol,
              lengthMenu: [ -1],
              processing: true,
              serverSide: true,
              aaSorting: [[ 5, "asc" ]],
              
                                
          ajax:{
                url:"{{route('informesp')}}",
                data:{fechaini:fechaini, fechafin:fechafin,usuario:usuario }
              },
              columns: [
                {
                    data:'pid',
                    name:'pid'
                },
                {
                    data:'cli',
                    name:'cli'
                },
                {
                  data:'va',
                  name:'va'
                 
                },
                {
                  data:'vc',
                  name:'vc'                
                },
                {
                    data:'c',
                    name:'c'
                },
                {
                    data:'fhp',
                    name:'fhp'
                },
                {
                    data:'obsp',
                    name:'obsp'
                },
                {
                    data:'emp',
                    name:'emp'
                }
              ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
  
            
            // var intVal = function ( i ) {
            //     return typeof i === 'string' ?
            //         i.replace(/[\$.]/g, '')*1 :
            //         typeof i === 'number' ?
            //             i : 0;
            // };
  
            
            valorp = api
                .column(2, { page: 'current'})
                .data()
                .reduce(function (a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);

            
            $(api.column(2).footer()).html(valorp);
          

          },
              //Botones----------------------------------------------------------------------
        "dom":'Brtip',
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

    

function fill_datatable2(fechaini = '', fechafin = '', usuario = '' )
         {
          var datatable2 = $('#tprestamo').DataTable
          ({
              language: idioma_espanol,
              lengthMenu: [ -1],
              processing: true,
              serverSide: true,
              aaSorting: [[ 8, "asc" ]],
                                
          ajax:{
                url:"{{route('informespo')}}",
                data:{fechaini:fechaini, fechafin:fechafin,usuario:usuario }
              },
              columns: [
                {
                    data:'poid',
                    name:'poid'
                },
                {
                    data:'cli',
                    name:'cli'
                },
                {
                  data:'vm',
                  name:'vm'
                 
                },
                {
                  data:'tp',
                  name:'tp'
                 
                },
                {
                  data:'in',
                  name:'in'
                },
                {
                    data:'tc',
                    name:'tc'
                },
                {
                    data:'vmt',
                    name:'vmt'
                },
                {
                    data:'vc',
                    name:'vc'
                },
                {
                    data:'fhpo',
                    name:'fhpo'
                },
                {
                    data:'obspo',
                    name:'obspo'
                },
                {
                    data:'emp',
                    name:'emp'
                }
              ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
  
            
            // var intVal = function ( i ) {
            //     return typeof i === 'string' ?
            //         i.replace(/[\$.]/g, '')*1 :
            //         typeof i === 'number' ?
            //             i : 0;
            // };
  
            
            valorpo = api
                .column(2, { page: 'current'})
                .data()
                .reduce(function (a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);

            
            $(api.column(2).footer()).html(valorpo);
          

          },
              //Botones----------------------------------------------------------------------
        "dom":'Brtip',
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


function fill_datatable3(fechaini = '', fechafin = '', usuario = '' )
         {
          var datatable = $('#tgasto').DataTable
          ({
              language: idioma_espanol,
              lengthMenu: [ -1],
              processing: true,
              serverSide: true,
              aaSorting: [[ 3, "asc" ]],
                                
          ajax:{
                url:"{{route('informesg')}}",
                data:{fechaini:fechaini, fechafin:fechafin,usuario:usuario }
              },
              columns: [
                {
                    data:'id',
                    name:'id'
                },
                {
                    data:'monto',
                    name:'monto'
                },
                {
                  data:'descripcion',
                  name:'descripcion'
                 
                },
                {
                  data:'created_at',
                  name:'created_at'                
                },
                {
                    data:'emp',
                    name:'emp'
                }
              ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
  
            
            // var intVal = function ( i ) {
            //     return typeof i === 'string' ?
            //         i.replace(/[\$.]/g, '')*1 :
            //         typeof i === 'number' ?
            //             i : 0;
            // };
  
            
            valorg = api
                .column(1, { page: 'current'})
                .data()
                .reduce(function (a, b) {
                    return parseInt(a) + parseInt(b);
                }, 0);

            
            $(api.column(1).footer()).html(valorg);
          

          },
              //Botones----------------------------------------------------------------------
        "dom":'Brtip',
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

       var fechaini = $('#fechaini').val();
       var fechafin = $('#fechafin').val();
       var usuario = $('#usuario').val();

        if(fechaini != '' && fechafin != '' && usuario != ''){

            $('#tpago').DataTable().destroy();
            $('#tprestamo').DataTable().destroy();
            $('#tgasto').DataTable().destroy();

            fill_datatable(fechaini, fechafin, usuario);
            fill_datatable1(fechaini, fechafin, usuario);
            fill_datatable2(fechaini, fechafin, usuario);
            fill_datatable3(fechaini, fechafin, usuario);

        }else{
        
             swal({
            title: 'Debes digitar fecha inicial, fecha final y usuario',
            icon: 'warning',
            buttons:{
                cancel: "Cerrar"
                
                    }
              })
        }
        
});        


$('#reset').click(function(){
        $('#fechaini').val('');
        $('#fechafin').val('');
        $('#usuario').val('');
        $('#tpago').DataTable().destroy();
        $('#tprestamo').DataTable().destroy();
        $('#tgasto').DataTable().destroy();
        fill_datatable();
        fill_datatable1();
        fill_datatable2();
        fill_datatable3();
      });
});

//Detalle pagos

function fill_datatable1(fechaini = '', fechafin = '', usuario = '' )
{
 $("#detalle").empty();
 $("#detalle1").empty();
 $("#detalle2").empty();
 $("#detalle3").empty();
  $.ajax({
  url:"{{route('informes')}}",
  data:{fechaini:fechaini, fechafin:fechafin,usuario:usuario },
  dataType:"json",
  success:function(data){
    $.each(data.result, function(i, item){
      
    $("#detalle").append(
        '<div class="small-box bg-info"><div class="inner">'+
        '<h5>TOTAL PAGOS</h5>'+
        '<p><h5><i class="fas fa-dollar-sign"></i>'+item.cobrado+'</h5></p>'+
        '</div><div class="icon"><i class="fas fa-motorcycle"></i></div></div>'
     );
     
  }),
  $.each(data.result1, function(i, item1){
      
    $("#detalle1").append(
        
          '<div class="small-box bg-success"><div class="inner">'+
          '<h5>TOTAL ATRASOS<sup style="font-size: 20px"></sup></h5>'+
          '<p><h5><i class="fas fa-dollar-sign"></i>'+item1.atrasado+'</h5></p>'+
          '</div><div class="icon"><i class="fas fa-handshake"></i></div></div>'
     );
       
    }),
    $.each(data.result2, function(i, item2){
      
      $("#detalle2").append(
          
            '<div class="small-box bg-warning"><div class="inner">'+
            '<h5>TOTAL PRESTAMOS</h5>'+
            '<p><h5><i class="fas fa-dollar-sign"></i>'+item2.prestamos+'</h5></p>'+
            '</div><div class="icon"><i class="fas fa-money-bill-alt"></i></div></div>'

         );
         
      }),
    $.each(data.result3, function(i, item3){
      
      $("#detalle3").append(
          
            '<div class="small-box bg-danger"><div class="inner">'+
            '<h5>TOTAL GASTOS</h5>'+
            '<p><h5><i class="fas fa-dollar-sign"></i>'+item3.gastos+'</h5></p>'+
            '</div><div class="icon"><i class="fas fa-route"></i></div></div>'
        
         );
         
      });
  }
  
});
}

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
