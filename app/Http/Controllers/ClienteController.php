<?php

namespace App\Http\Controllers;

use App\Models\Admin\Cliente;
use App\Models\Admin\Empleado;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;


class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
         $id_usuario = Session()->get('usuario_id');
         
         $usuarios = Usuario::orderBy('id')->where('id', '=', $id_usuario)->pluck('usuario', 'id')->toArray();
         
    
        if($request->ajax()){

            $datas = Cliente::where('usuario_id', '=', $id_usuario)->orderBy('usuario_id')->orderBy('consecutivo')->get();
            return  DataTables()->of($datas)
            // ->addColumn('editar', '<a href="{{url("cliente/$id/editar")}}" class="btn-accion-tabla tooltipsC" title="Editar este cliente">
            //       <i class="fa fa-fw fa-pencil-alt"></i>
            //     </a>')
            ->addColumn('action', function($datas){
          $button = '<button type="button" name="edit" id="'.$datas->id.'"
          class = "edit btn-float  bg-gradient-primary btn-sm tooltipsC"  title="Editar Cliente"><i class="far fa-edit"></i></button>';
          $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->id.'"
          class = "prestamo btn-float  bg-gradient-warning btn-sm tooltipsC" title="Agregar Prestamo"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>';
          $button .='&nbsp;<button type="button" name="detalle" id="'.$datas->id.'"
          class = "detalle btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Prestamos"><i class="fas fa-atlas"></i></i></button>';
          return $button;

            }) 
            ->rawColumns(['action'])
            ->make(true);
            }
        return view('admin.cliente.index', compact('usuarios', 'datas'));
    }
    
    
    

    public function indexcli(Request $request)
    {
        
         $id_usuario = Session()->get('usuario_id');
         
         $usuarios = Usuario::orderBy('id')->where('id', '=', $id_usuario)->pluck('usuario', 'id')->toArray();

        $id_empleados = $request->id;

        if($request->ajax()){

           if($id_empleados != 0 || $id_empleados != null){

             $datas = DB::table('usuario')
            ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
            ->Join('cliente', 'usuario.id', '=', 'cliente.usuario_id')
            ->where('empleado.ide', '=', $id_empleados)
            ->get();
            return  DataTables()->of($datas)
            //   ->addColumn('action', function($datas){
            //   $button = '<button type="button" name="edit" id="'.$datas->id.'"
            //   class = "edit btn-float  bg-gradient-primary btn-sm tooltipsC"  title="Editar Cliente"><i class="far fa-edit"></i></button>';
            //   $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->id.'"
            //   class = "prestamo btn-float  bg-gradient-warning btn-sm tooltipsC" title="Agregar Prestamo"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>';
            //   $button .='&nbsp;<button type="button" name="detalle" id="'.$datas->id.'"
            //   class = "detalle btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Prestamos"><i class="fas fa-atlas"></i></i></button>';
            //   return $button;
    
            //     }) 
                //->rawColumns(['action'])
                ->make(true);
            }else{
                $datas = DB::table('usuario')
                ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
                ->Join('cliente', 'usuario.id', '=', 'cliente.usuario_id')
                ->where('empleado.ide', '=', $id_empleados)
                ->get();
                return  DataTables()->of($datas)
                ->make(true);

            }


        return view('admin.empleado.index', compact('usuarios', 'datas'));
        }
    }

    public function indexCliente(Request $request, $id)
    {
        
         $id_empleado = $id;
         
         
         $id_usuarios = Usuario::where('empleado_id','=', $id_empleado)->first();
         $id_usuario = $id_usuarios->id;
         $usuarios = Usuario::orderBy('id')->where('id', '=', $id_usuario)->pluck('usuario', 'id')->toArray();
         
            
        if($request->ajax()){

            $datas = Cliente::where('usuario_id', '=', $id_usuario)->orderBy('usuario_id')->orderBy('consecutivo')->get();
            return  DataTables()->of($datas)
            ->addColumn('action', function($datas){
          $button = '<button type="button" name="edit" id="'.$datas->id.'"
          class = "edit btn-float  bg-gradient-primary btn-sm tooltipsC"  title="Editar Cliente"><i class="far fa-edit"></i></button>';
          $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->id.'"
          class = "prestamo btn-float  bg-gradient-warning btn-sm tooltipsC" title="Agregar Prestamo"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>';
          $button .='&nbsp;<button type="button" name="detalle" id="'.$datas->id.'"
          class = "detalle btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Prestamos"><i class="fas fa-atlas"></i></i></button>';
          return $button;

            }) 
            ->rawColumns(['action'])
            ->make(true);
            }
        return view('admin.cliente.index', compact('usuarios', 'datas'));
    }
    
    
        public function index_card(Request $request)
    {
        
         $id_usuario = Session()->get('usuario_id');
         
         $usuario = Usuario::orderBy('id')->where('id', '=', $id_usuario)->pluck('usuario', 'id')->toArray();
         
            if($request->ajax()){
           
            $datas = DB::table('cliente')
            ->where('cliente.usuario_id', '=', $id_usuario)
            ->select('cliente.nombres', 'cliente.id', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo',  'cliente.telefono', 'cliente.celular', 'cliente.usuario_id')
            ->groupBy('cliente.nombres', 'cliente.id','cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'cliente.telefono', 'cliente.celular', 'cliente.usuario_id')
            ->get();
            return  DataTables()->of($datas)
                 ->addColumn('datos', function($datas){
                 $datosp ='<div class="row">
                             <div class="col-md-12 col-xs-3">
                                        <div class="small-box bg-info">
                                          <div class="inner">
                                            <h4>'.$datas->nombres.'</h4>
                                            <h4>'.$datas->apellidos.'</h4>
                            
                                            <p><i class="fas fa-map-marked"></i>: '.$datas->direccion.'</p>
                                             <p><i class="fas fa-phone-square-alt"></i>: '.$datas->telefono.'</p>
                                             <p><a href="tel:+'.$datas->celular.'"> <i class="fas fa-mobile-alt"></i>: '.$datas->celular.'</a></p>
                                             <p><i class="fas fa-sort-amount-down-alt"></i>: '.$datas->consecutivo.'</p>
                                             
                                          </div>
                                        
                                          <div class="row col-md-12 col-xs-12 ">
                                          <div class="col-md-6 col-xs-6">
                                          <button type="button"  class="detallecli btn btn-flotante3 btn-app bg-warning small-box-footer" id="'.$datas->id.'">
                                          <span class="badge bg-teal">prestamos</span>
                                            <i class="fa fa-atlas fa-w-14 fa-lg"></i>Detalle 
                                          </button>
                                         
                                           <button type="button"  class="editcli btn btn-flotante3 btn-app bg-primary small-box-footer" id="'.$datas->id.'">
                                          <span class="badge bg-teal">Editar: '.$datas->id.'</span>
                                          <i class="far fa-edit"></i>
                                          </button>
                                          </div>
                                          </div>
                                        </div>
                                    </div>    
                             </div>   
                          </div>';
               
                return $datosp;
                
                
            })
            ->rawColumns(['action','datos'])
            ->make(true);


            }
        
        return view('admin.pago_card.index', compact('usuario', 'datas'));
    }
    
    
    
    
    
    
    
    
    
    

    public function ruta()
    {
        
         $id_usuario = Session()->get('usuario_id');
         $datas = Cliente::where('usuario_id', '=', $id_usuario)->orderBy('usuario_id')->orderBy('consecutivo')->get();
           
        return view('admin.cliente.ruta.index', compact('datas'));  
    }

    public function rutaGuardar()
    {
        
         $id_usuario = Session()->get('usuario_id');
         $datas = Cliente::where('usuario_id', '=', $id_usuario)->orderBy('usuario_id')->orderBy('consecutivo')->get();
         $itemId = Input::get('itemId');
         $itemConsecutivo = Input::get('itemConsecutivo');
         foreach ($datas as $item) {
            return Cliente::where('id', '=', $itemId)
            ->update(array('consecutivo' => $itemConsecutivo));
         }
        
    }

    /**
     * Show the form for creating a new resource.
     *s
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        $rules = array(
            'nombres'  => 'required|max:100',
            'apellidos'  => 'required|max:100',
            'documento' => 'numeric|required|min:10000|max:999999999999',
            'celular' => 'numeric|required|min:10000|max:9999999999999999',
            'tipo_documento' => 'required',
            'usuario_id' => 'required',
            'ciudad' => 'required',
            'pais' => 'required',
            'estado' => 'required',
            'direccion' => 'required',
            'consecutivo' => 'numeric|required|min:1|max:9999999999',
            'activo' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        
        Cliente::create($request->all());
            return response()->json(['success' => 'ok']);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function editar($id)
    // {
    //         $id_usuario = Session()->get('usuario_id');
         
    //         $usuarios = Usuario::orderBy('id')->where('id', '=', $id_usuario)->pluck('usuario', 'id')->toArray();
            
    //         $data = Cliente::findOrFail($id);
      
       
    //     return view('admin.cliente.editar', compact('data','usuarios'));
    // }

    public function editar($id)
    {
        $id_usuario = Session()->get('usuario_id');
         
       $usuarios = Usuario::orderBy('id')->where('id', '=', $id_usuario)->pluck('usuario', 'id')->toArray();

        if(request()->ajax()){
        $data = Cliente::findOrFail($id);
            return response()->json(['result'=>$data]);

        }
        return view('admin.cliente.index', compact('usuarios'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(Request $request, $id)
    {
        $rules = array(
            'nombres'  => 'required|max:100',
            'apellidos'  => 'required|max:100',
            'documento' => 'numeric|required|min:10000|max:9999999999',
            'celular' => 'numeric|required|min:10000|max:9999999999',
            'tipo_documento' => 'required',
            'usuario_id' => 'required',
            'ciudad' => 'required',
            'pais' => 'required',
            'estado' => 'required',
            'direccion' => 'required',
            'consecutivo' => 'numeric|required|min:1|max:9999999999',
            'activo' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        
        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());
        return response()->json(['success' => 'ok1']);
            
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function index_cli_app(Request $request)
    {
     
            $useractivo = Usuario::where([
            ['usuario', '=', $request->usuario],
            ['activo', '=', 1]
        
            ])->count();
            
            
            if($useractivo >= 1){  
                
                
             $datascli = DB::table('cliente')
            ->where('cliente.usuario_id', '=', $request->usuario_id)
            ->select('cliente.tipo_documento', 'cliente.documento', 'cliente.nombres', 'cliente.id', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo',  'cliente.telefono', 'cliente.celular', 'cliente.usuario_id', 'cliente.ciudad', 'cliente.pais', 'cliente.estado', 'cliente.activo')
            ->orderBy('cliente.consecutivo')
            ->get();    
            
            
       
            
            return Response()->json(
             $datascli
            , 200);

            } else {
              return response()->json(['error'=> 'Unauthorised'], 400);
            }

  
         
         
           
        
    }
    
    
      public function actualizar_cli_app(Request $request)
    {
        
         $useractivo = Usuario::where([
            ['usuario', '=', $request->usuario],
            ['activo', '=', 1]])->count();
            
         if($useractivo >= 1){  
        
        $rules = array(
            'nombres'  => 'required|max:100',
            'apellidos'  => 'required|max:100',
            'documento' => 'numeric|required|min:10000|max:9999999999',
            'celular' => 'numeric|required|min:10000|max:9999999999',
            'tipo_documento' => 'required',
            'usuario_id' => 'required',
            'ciudad' => 'required',
            'pais' => 'required',
            'estado' => 'required',
            'direccion' => 'required',
            'consecutivo' => 'numeric|required|min:1|max:9999999999',
            'activo' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        
        $cliente = Cliente::findOrFail($request->id);
        $cliente->update($request->all());
        return response()->json(['success' => 'Cliente actualizado exitosamente!'], 200);
            
    }else{
         return response()->json(['abort' => 'No estas autorizado'], 400);
        
    }
   }
   
    public function guardar_cli_app(Request $request)
    {
           $useractivo = Usuario::where([
            ['usuario', '=', $request->usuario],
            ['activo', '=', 1]])->count();
            
         if($useractivo >= 1){  
        
        
        $rules = array(
            'nombres'  => 'required|max:100',
            'apellidos'  => 'required|max:100',
            'documento' => 'numeric|required|min:10000|max:999999999999',
            'celular' => 'numeric|required|min:10000|max:9999999999999999',
            'tipo_documento' => 'required',
            'usuario_id' => 'required',
            'ciudad' => 'required',
            'pais' => 'required',
            'estado' => 'required',
            'direccion' => 'required',
            'consecutivo' => 'numeric|required|min:1|max:9999999999',
            'activo' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        
        Cliente::create($request->all());
           return response()->json(['success' => 'Cliente Creado exitosamente!'], 200);
        
    } else{
         return response()->json(['abort' => 'No estas autorizado'], 400);
        
    }
    }
}
