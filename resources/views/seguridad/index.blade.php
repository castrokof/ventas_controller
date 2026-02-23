<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Coll-System | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{asset("assets/$theme/plugins/animate.css/animate.min.css")}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset("assets/$theme/plugins/fontawesome-free/css/all.min.css")}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset("assets/$theme/dist/css/adminlte.min.css")}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
 
  <style>
    .spinner {
  display: inline-block;
  opacity: 0;
  width: 0;

  -webkit-transition: opacity 0.25s, width 0.25s;
  -moz-transition: opacity 0.25s, width 0.25s;
  -o-transition: opacity 0.25s, width 0.25s;
  transition: opacity 0.25s, width 0.25s;
}

.has-spinner.active {
  cursor:progress;
}

.has-spinner.active .spinner {
  opacity: 1;
  width: auto; /* This doesn't work, just fix for unkown width elements */
}

.has-spinner.btn-mini.active .spinner {
    width: 10px;
}

.has-spinner.btn-small.active .spinner {
    width: 13px;
}

.has-spinner.btn.active .spinner {
    width: 16px;
}

.has-spinner.btn-large.active .spinner {
    width: 19px;
}
  </style>  
  
</head>
<body class="hold-transition login-page">

<div class="simple-page-form animated flipInY" id="login-form">
<div class="login-box">
  <div class="login-logo">
    <div class="row">
        
        
      </div>          
    <a href="{{route('inicio')}}"></a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    
    <div class="card-body login-card-body">
        <img src="{{asset("assets/img/logo_collsystem.png")}}") alt="User Avatar" class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center img-size-500 mr-12 " >
       <p class="login-box-msg"> <b>Coll-System</b></p>
       <div class="card"></div><hr />
      <p class="login-box-msg">Inicie Sesion</p>
      @if ($errors->any())
      <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h5><i class="icon fas fa-ban"></i></h5>
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
      @endif
      
    <form action="{{route('login_post')}}" method="post">
        @csrf
        <div class="input-group mb-3">
        <input type="text" name="usuario" class="form-control" value="{{old('usuario')}}" placeholder="Usuario">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Contraseña">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6 col-mb-6 col-xs-12">
          </div> 
          <!-- /.col -->
          <div class="col-lg-6 col-mb-6 col-xs-12">
            <p>
            <button type="submit" class="btn  btn-info has-spinner" style="width:100%">
              Acceder
              <span class="spinner"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i></span>
              
              </button>
            </p>
          </div>
          <!-- /.col -->
        </div>
      </form>

     
      <!-- /.social-auth-links -->

     
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
</div>
<!-- /.login-box -->

<!-- jQuery -->

<script src="{{asset("assets/$theme/plugins/jquery/jquery.min.js")}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset("assets/$theme/plugins/bootstrap/js/bootstrap.bundle.min.js")}}"></script>
<!-- AdminLTE App -->
<script src="{{asset("assets/$theme/dist/js/adminlte.min.js")}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset("assets/$theme/dist/js/demo.js")}}"></script>
<script>
  $(document).ready(function(){
   $('a, button').click(function() {
       $(this).toggleClass('active');
   });
 });
 
 </script>

</body>
</html>