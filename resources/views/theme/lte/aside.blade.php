 <!-- Main Sidebar Container -->
  <aside class="main-sidebar  sidebar-light-info elevation-4 ">
    <!-- Brand Logo -->
    <a href="#" class="brand-link"><i class="fab fa-cc-amazon-pay fa-w-18 fa-2x"></i> 
      <!--<img src="{{asset("assets/$theme/dist/img/logo_gota.gif")}}"
           alt="Sinteco"
           class="brand-image img-circle elevation-5"
           style="opacity: .8">-->
      <span class="brand-text font-weight-light">Coll-System</span>
    </a>  

    <!-- Sidebar -->
    <div class="sidebar sidebar-light-info sidebar-collapse">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset("assets/$theme/dist/img/user_default.jpg  ")}}" class="img-circle elevation-5" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{Session()->get('usuario') ?? ''}}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul id="sidebar-menu" class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <div class="user-panel mt-1 pb-1 mb-1 d-flex">
           <div class="info">
            <i class="fa fa-bars" aria-hidden="false"></i>
           </div>
          <div class="info">
            <i class="nav-item has-treeview">
            <H5 alignt="center"> Menú Principal</H5>
            </i>
         </div>
          </div>
          
           @foreach ($menusComposer as $key => $item)
               @if($item["menu_id"] != 0)
                 @break
               @endif 
               @include("theme.$theme.menu-item", ["item" => $item]) 
           @endforeach  
          </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
