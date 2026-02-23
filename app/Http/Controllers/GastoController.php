<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidacionEmpleado;
use App\Models\Admin\Empleado;
use App\Models\Admin\Empresa;
use App\Models\Admin\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GastoController extends Controller
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

            $usuario_id = $request->session()->get('usuario_id');
        
            
   
           
            if($request->ajax()){
    
                                 
                $datas = DB::table('gasto')
                ->join('usuario', 'gasto.usuario_id', '=', 'usuario.id')
                ->orderBy('gasto.idg')
                ->select('gasto.idg as id', 'gasto.monto as monto', 'gasto.descripcion as descripcion', 'gasto.created_at as created_at')
                ->get();
                return  DataTables()->of($datas)
                ->addColumn('editar', function($datas){
                $button = '<button type="button" name="edit" id="'.$datas->id.'"
                class = "edit btn btn-primary btn-sm">Editar</button>';
               
                return $button;
    
                }) 
                ->rawColumns(['editar'])
                ->make(true);
                    
                }
           
            return view('admin.gasto.index', compact('usuario_id'));

        }else{
        
        $usuario_id = $request->session()->get('usuario_id');
        
        
        
        
        if($request->ajax()){

                                         
            $datas = DB::table('gasto')
            ->join('usuario', 'gasto.usuario_id', '=', 'usuario.id')
            ->where('gasto.usuario_id', $usuario_id)
            ->select('gasto.idg as id', 'gasto.monto as monto', 'gasto.descripcion as descripcion', 'gasto.created_at as created_at')
            ->orderBy('gasto.idg')->get();
            return  DataTables()->of($datas)
                ->addColumn('editar', function($datas){
                $button = '<button type="button" name="edit" id="'.$datas->id.'"
                class = "edit btn btn-primary btn-sm">Editar</button>';
               
                return $button;

            }) 
            ->rawColumns(['editar'])
            ->make(true);
                }
            }
        
        return view('admin.gasto.index', compact('usuario_id'));
        
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        $rules = array(
            'monto'  => 'numeric|required|min:1|max:9999999999',
            'descripcion'  => 'required|max:150'
            
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        
        Gasto::create($request->all());
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


      if(request()->ajax()){
            $data = Gasto::where('idg', '=', $id)->first();
            return response()->json(['result'=>$data]);
        
        }
      
        return view('admin.gasto.index');

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
            'monto'  => 'numeric|required|min:1|max:9999999999',
            'descripcion'  => 'required|max:150'
            
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
     
        if(request()->ajax()){
            
         Gasto::where('idg', '=', $id)
        ->update([
            'monto' => $request->monto,
            'descripcion' => $request->descripcion,
            'updated_at' => now()
        ]);     
            
       
        }
        return response()->json(['success' => 'ok1']);
    
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
