@extends("theme.$theme.layout")
@section('titulo')
  Ordenar ruta 
@endsection

@section("styles")
{{-- <link href="{{asset("assets/$theme/plugins/jquery-ui/jquery-ui.min.css")}}" rel="stylesheet" type="text/css"/> --}}
{{-- <link rel="stylesheet" href="{{asset("assets/$theme/dist/css/adminlte.min.css")}}"> --}}
{{-- <link rel="stylesheet" href="{{asset("assets/$theme/plugins/overlayScrollbars/css/OverlayScrollbars.min.css")}}> --}}
<style> 

  
  #menu li { 
    list-style-type: none;
    padding: 10px;
    width: 10 0%;
    margin: 3px 0;
    font-size: 15px;
    color: rgb(15, 19, 19);
       }
    .loader { visibility: hidden; background-color: rgba(252, 249, 249, 0.952); 
    position: absolute; 
    left: 0;
    bottom: 0; 
    right: 0; 
    top: 0; 
    margin: auto; /* Centramos vertical y horizontalmente */
    z-index: +100 !important; 
    width: 100%;  
    height:100%; } 
    .loader img { position: relative; 
    /* top:100%; left:50%; */
    left: 50%;
    bottom: 0; 
    right: 0; 
    top: 40%; 
    margin: auto; /* Centramos vertical y horizontalmente */
      width: 100px; height: 100px; } 
  </style>
@endsection

@section("scriptsPlugins")
<!-- jQuery ui -->
<script src="{{asset("assets/$theme/plugins/jquery-ui/jquery-ui.min.js")}}"></script>
<script src="{{asset("assets/$theme/plugins/jquery-touch/jquery.ui.touch-punch.min.js")}}"></script>

@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/ruta/index.js")}}" type="text/javascript"></script>       
@endsection

@section('contenido')
<div class="row">
  <div class="loader"> <img src="{{asset("assets/$theme/dist/img/loader6.gif")}}" class="" /> </div>
<div class="col-lg-12">
  @include('includes.form-mensaje')    
<div class="card card-primary  p-2">
    <div class="card-header layout-fixed">
      <h3 class="card-title">Ordenar ruta --- arrastre cualquier cliente para realizar el enrutamiento hacia arriba o hacia abajo</h3>
      <div class="card-tools pull-right">
      
      </div>  
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      @csrf
      <div class="form-group row">
      <div class="col-lg-1">
      </div>
      <div class="col-lg-10">
      <div class="sidebar-white-primary elevation-4" >
      
      
        <div class="card-body table-responsive p-2">
          
          <div class="col-lm-3">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview"  id="menu" >
              <th>
              @foreach ($datas as $item)
              <li class="col-xs-12 nav-item has-treeview" id="{{$item->id}}">
              
              <a href="#" class="nav-link active">
               
               <i class="fas fa-bars"></i>{{$item->consecutivo}} <i class="left fas fa-angle-left"></i> <i class="fas fa-route"></i> {{$item->nombres}}---{{$item->direccion}}
              
                  
                </a>
              
              </li>  
              @endforeach 
            </th>
            </ul>  
          </div>
        </div>
      </div>
      </div>
      <div class="col-lg-1">
      </div>
      </div>  
    </div>
  </div>
 
</div>
</div>

@endsection