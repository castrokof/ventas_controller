<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidacionEmpleado;
use App\Models\Admin\Empleado;
use App\Models\Admin\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
       
        $rol_id = $request->session()->get('rol_id');

        if($rol_id == 1){

            $empleado_id = $request->session()->get('empleado_id');
        
            $empresa = Empresa::orderBy('id')->pluck('id','nombre')->toArray();
   
           
            if($request->ajax()){
    
                                 
                $datas = DB::table('empleado')
                ->join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
                ->orderBy('empleado.ide')->get();
                return  DataTables()->of($datas)
                // ->addColumn('editar', '<a href="{{url("empleado/$id/editar")}}" class="btn-accion-tabla tooltipsC" title="Editar este empleado">
                //       <i class="fa fa-fw fa-pencil-alt"></i>
                //     </a>')
                ->addColumn('editar', function($datas){
              $button = '<button type="button" name="edit" id="'.$datas->ide.'"
              class = "edit btn btn-primary btn-sm">Editar</button>';
              $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->ide.'"
              class = "clientes btn-float  bg-gradient-warning btn-sm tooltipsC" title="Ver Cliente"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-user"></i></button>';
              return $button;
    
                }) 
                ->rawColumns(['editar'])
                ->make(true);
                    
                }
            
            return view('admin.empleado.index', compact('empresa'));

        }else{
        
        $empleado_id = $request->session()->get('empleado_id');
        
        $empresa = DB::table('empleado')->Join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
        ->where('empleado.ide', '=', $empleado_id)->pluck('empresa.id', 'empresa.nombre')->toArray();

        $empresaLogin = DB::table('empleado')->Join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
        ->where('empleado.ide', '=', $empleado_id)->select('empleado.empresa_id')->get();

       
        if($request->ajax()){

            foreach ($empresaLogin as $empresa) {
                             
                $datas = DB::table('empleado')
                ->join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
                ->where('empresa_id', '=', $empresa->empresa_id)->orderBy('empleado.ide')->get();
                return  DataTables()->of($datas)
                ->addColumn('editar', function($datas){
                $button = '<button type="button" name="edit" id="'.$datas->ide.'"
                class = "edit btn btn-primary btn-sm">Editar</button>';
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->ide.'"
                class = "clientes btn-float  bg-gradient-warning btn-sm tooltipsC" title="Ver Clientes"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-user"></i></button>';
                return $button;

            }) 
            ->rawColumns(['editar'])
            ->make(true);
                }
            }
        
        return view('admin.empleado.index', compact('empresa'));
        }
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        $rules = array(
            'nombres'  => 'required|max:100',
            'apellidos'  => 'required|max:100',
            'documento' => 'numeric|required|min:10000|max:9999999999',
            'tipo_documento' => 'required',
            'empresa_id' => 'required',
            'ciudad' => 'required',
            'pais' => 'required',
            'barrio' => 'required',
            'direccion' => 'required',
            'activo' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        
        Empleado::create($request->all());
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
    public function editar($id)
    { 


        $empresa = Empresa::orderBy('id')->pluck('id','nombre')->toArray();
        
        if(request()->ajax()){
            $data = Empleado::where('ide', '=', $id)->first();
            return response()->json(['result'=>$data]);
        
        }
      
        return view('admin.empleado.index', compact('data','empresa'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar(ValidacionEmpleado $request, $id)
    {
        if(request()->ajax()){
        Empleado::where('ide', '=', $id)
        ->update([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'tipo_documento' => $request->tipo_documento,
            'documento'  => $request->documento,
            'pais' => $request->pais,
            'ciudad' => $request->ciudad,
            'barrio' => $request->barrio,
            'direccion' => $request->direccion,
            'celular' => $request->celular,
            'telefono' => $request->telefono,
            'activo' => $request->activo,
            'empresa_id' => $request->empresa_id,
            'updated_at' => now()
        ]);  
        return response()->json(['success' => 'ok1']);
        }
        return redirect('empleado')->with('mensaje', 'Empleado actualizada con exito!!');
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
