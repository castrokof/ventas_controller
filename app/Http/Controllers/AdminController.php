<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Cliente;
use App\Models\Seguridad\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\DataTables;



class AdminController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";


        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $empleado_id = $request->session()->get('empleado_id');
        $usuario_id = $request->session()->get('usuario_id');

        $empresaLogin = DB::table('empleado')->Join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
        ->where('empleado.ide', '=', $empleado_id)->first();
      
        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();

        $datas = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '=', $fecha_Actual]])
        ->select(DB::raw('SUM(detalle_prestamo.valor_cuota) as cobro'),
                 DB::raw('SUM(CASE WHEN detalle_prestamo.estado != 1 THEN 1 ELSE 0 END) as cobros'),
                 DB::raw('SUM(CASE WHEN detalle_prestamo.estado = "C" THEN 1 ELSE 0 END) as pendiente_cobros'),
                 DB::raw('SUM(CASE WHEN detalle_prestamo.estado = "P" THEN 1 ELSE 0 END) as pagados'),
                 DB::raw('SUM(CASE WHEN detalle_prestamo.estado = "A" THEN 1 ELSE 0 END) as atrasos')
        )
        ->get();

        
        $dataa = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.estado', '=', "A"]])
        ->whereBetween('detalle_prestamo.updated_at', [$fechaAi, $fechaAf])
        ->select(DB::raw('sum(detalle_prestamo.valor_cuota) as atrasado'))
        ->get();

        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('pago', 'prestamo.idp', '=', 'pago.prestamo_id')
        ->where('prestamo.usuario_id', '=', $usuario_id)
        ->whereBetween('pago.updated_at', [$fechaAi, $fechaAf])
        ->select(DB::raw('sum(pago.valor_abono) as cobrado'))
        ->get();
        

        $datast = DB::table('usuario')
        ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
        ->Join('prestamo', 'prestamo.usuario_id', '=', 'usuario.id')
        ->Join('usuario_rol', 'usuario.id', '=', 'usuario_rol.usuario_id')
        ->Join('detalle_prestamo', 'detalle_prestamo.prestamo_id', '=', 'prestamo.idp')
        ->where([['empleado.empresa_id', '=',$empresaLogin->empresa_id],  ['usuario_rol.rol_id', '=', 3], ['detalle_prestamo.fecha_cuota', '=', $fecha_Actual]])
        ->select(DB::raw('SUM(detalle_prestamo.valor_cuota) as cobro'),
                 DB::raw('SUM(CASE WHEN detalle_prestamo.estado != 1 THEN 1 ELSE 0 END) as cobros'),
                 DB::raw('SUM(CASE WHEN prestamo.monto_pendiente > 0 THEN 1 ELSE 0 END) as pendiente_cobros'),
                 DB::raw('SUM(CASE WHEN detalle_prestamo.estado = "P" THEN 1 ELSE 0 END) as pagados'),
                 DB::raw('SUM(CASE WHEN detalle_prestamo.estado = "A" THEN 1 ELSE 0 END) as atrasos'))
        ->get();

        
         
        return view('admin.admin.index', compact('datas', 'data', 'datast', 'dataa')); 

// -----------------------------------------------------------------------------
       
      
      
    
   
    }
    
    
    
    //Informes de widget admin

    public function informes(Request $request)
    {  

        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";

        $empleado_id = $request->session()->get('empleado_id');
        $usuario_id = $request->session()->get('usuario_id');

        $empresaLogin = DB::table('empleado')->Join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
        ->where('empleado.ide', '=', $empleado_id)->first();
        
        $usuarios = Usuario::orderBy('id')
        ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
        ->where('empleado.empresa_id', '=',$empresaLogin->empresa_id)
        ->pluck('usuario', 'id')->toArray();

        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();

         
        $usuario = $request->usuario;
        $fechaini = $request->fechaini;
        $fechafin = $request->fechafin;

        if(request()->ajax()){
        
            if(!empty($fechaini) && !empty($fechafin) && !empty($usuario) ){
           
            $fechaini = $request->fechaini." 00:00:01";
            $fechafin = $request->fechafin." 23:59:59";

                   
            $dataa = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where([['prestamo.usuario_id', '=', $usuario], ['detalle_prestamo.estado', '=', "A"]])
            ->whereBetween('detalle_prestamo.updated_at', [ $fechaini, $fechafin])
            ->select(DB::raw('sum(detalle_prestamo.valor_cuota) as atrasado'))
            ->get();
    
            $data = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('pago', 'prestamo.idp', '=', 'pago.prestamo_id')
            ->where('prestamo.usuario_id', '=', $usuario)
            ->whereBetween('pago.created_at', [ $fechaini, $fechafin])
            ->select(DB::raw('sum(pago.valor_abono) as cobrado'))
            ->get();
    
            $datap = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where('prestamo.usuario_id', '=', $usuario)
            ->whereBetween('prestamo.created_at', [ $fechaini, $fechafin])
            ->select(DB::raw('sum(prestamo.monto) as prestamos'))
            ->get();
            
            $datag = DB::table('gasto')
            ->Join('usuario', 'gasto.usuario_id', '=', 'usuario.id')
            ->where('gasto.usuario_id', '=', $usuario)
            ->whereBetween('gasto.created_at', [ $fechaini, $fechafin])
            ->select(DB::raw('sum(gasto.monto) as gastos'))
            ->get();
            
            return response()->json(['result'=>$data, 'result1'=>$dataa, 'result2'=>$datap, 'result3'=>$datag ]);
           
        
        }else{
            
        $dataa = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.estado', '=', "A"]])
        ->whereBetween('detalle_prestamo.updated_at', [$fechaAi, $fechaAf])
        ->select(DB::raw('sum(detalle_prestamo.valor_cuota) as atrasado'))
        ->get();

        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('pago', 'prestamo.idp', '=', 'pago.prestamo_id')
        ->where('prestamo.usuario_id', '=', $usuario_id)
        ->whereBetween('pago.updated_at', [$fechaAi, $fechaAf])
        ->select(DB::raw('sum(pago.valor_abono) as cobrado'))
        ->get();

        $datap = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->where('prestamo.usuario_id', '=', $usuario_id)
        ->whereBetween('prestamo.created_at', [$fechaAi, $fechaAf])
        ->select(DB::raw('sum(prestamo.monto) as prestamos'))
        ->get();
        
        $datag = DB::table('gasto')
        ->Join('usuario', 'gasto.usuario_id', '=', 'usuario.id')
        ->where('gasto.usuario_id', '=', $usuario_id)
        ->whereBetween('gasto.created_at', [$fechaAi, $fechaAf])
        ->select(DB::raw('sum(gasto.monto) as gastos'))
        ->get();
        

     
        return response()->json(['result'=>$data, 'result1'=>$dataa, 'result2'=>$datap, 'result3'=>$datag ]);  
        }
    }
        
        return view('admin.admin.informes', compact('usuarios'));  
}

    
// Detalle de pagos para informe
    public function informesp(Request $request)
    {  

        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";
        $empleado_id = $request->session()->get('empleado_id');
        $usuario_id = $request->session()->get('usuario_id');
        $empresaLogin = DB::table('empleado')->Join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
        ->where('empleado.ide', '=', $empleado_id)->first();
        
        $usuario = $request->usuario;
        $fechaini = $request->fechaini;
        $fechafin = $request->fechafin;

    if(request()->ajax()){

        if(!empty($fechaini) && !empty($fechafin) && !empty($usuario) ){
           
            $fechaini = $request->fechaini." 00:00:01";
            $fechafin = $request->fechafin." 23:59:59";
        
        $data = DB::table('pago')
        ->Join('prestamo', 'pago.prestamo_id', '=', 'prestamo.idp')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('usuario', 'pago.usuario_id', '=', 'usuario.id')
        ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
        ->where([['empleado.empresa_id', '=',$empresaLogin->empresa_id],['usuario.id', '=', $usuario]])
        ->whereBetween('pago.created_at', [$fechaini, $fechafin])
        ->select('pago.valor_cuota as vc','pago.valor_abono as va','pago.numero_cuota as c','pago.observacion_pago as obsp','pago.prestamo_id as pid','pago.created_at as fhp',
        'usuario.usuario as emp',
        DB::raw('CONCAT(cliente.nombres," ",cliente.apellidos) as cli'));
        return  DataTables()->of($data)
        ->make(true);
    
        }else{

            $data = DB::table('pago')
            ->Join('prestamo', 'pago.prestamo_id', '=', 'prestamo.idp')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('usuario', 'pago.usuario_id', '=', 'usuario.id')
            ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
            ->where([['empleado.empresa_id', '=',$empresaLogin->empresa_id],['usuario.id', '=', $usuario_id]])
            ->whereBetween('pago.created_at', [$fechaAi, $fechaAf])
            ->select('pago.valor_cuota as vc','pago.valor_abono as va','pago.numero_cuota as c','pago.observacion_pago as obsp','pago.prestamo_id as pid','pago.created_at as fhp',
            'usuario.usuario as emp',
            DB::raw('CONCAT(cliente.nombres," ",cliente.apellidos) as cli'));
            return  DataTables()->of($data)
            ->make(true);

        }
    }

    }

// Detalle de prestamos para informe
public function informespo(Request $request)
{  

    $fechaAi= Carbon::now()->toDateString()." 00:00:01";
    $fechaAf= Carbon::now()->toDateString()." 23:59:59";
    $empleado_id = $request->session()->get('empleado_id');
    $usuario_id = $request->session()->get('usuario_id');
    $empresaLogin = DB::table('empleado')->Join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
    ->where('empleado.ide', '=', $empleado_id)->first();
    
    $usuario = $request->usuario;
    $fechaini = $request->fechaini;
    $fechafin = $request->fechafin;

    if(request()->ajax()){

    if(!empty($fechaini) && !empty($fechafin) && !empty($usuario) ){
       
        $fechaini = $request->fechaini." 00:00:01";
        $fechafin = $request->fechafin." 23:59:59";
    
    $data = DB::table('prestamo')
    ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
    ->Join('usuario', 'prestamo.usuario_id', '=', 'usuario.id')
    ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
    ->where([['empleado.empresa_id', '=',$empresaLogin->empresa_id],['prestamo.usuario_id', '=', $usuario]])
    ->whereBetween('prestamo.created_at', [$fechaini, $fechafin])
    ->select('prestamo.valor_cuota as vc','prestamo.monto as vm', 'prestamo.monto_total as vmt', 'prestamo.tipo_pago as tp', 'prestamo.interes as in','prestamo.cuotas as tc','prestamo.observacion_prestamo as obspo','prestamo.idp as poid','prestamo.created_at as fhpo',
    'usuario.usuario as emp',
    DB::raw('CONCAT(cliente.nombres," ",cliente.apellidos) as cli'));
    return  DataTables()->of($data)
    ->make(true);

    }else{

        $data = DB::table('prestamo')
    ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
    ->Join('usuario', 'prestamo.usuario_id', '=', 'usuario.id')
    ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
    ->where([['empleado.empresa_id', '=',$empresaLogin->empresa_id],['prestamo.usuario_id', '=', $usuario]])
    ->whereBetween('prestamo.created_at', [$fechaAi, $fechaAf])
    ->select('prestamo.valor_cuota as vc','prestamo.monto as vm', 'prestamo.monto_total as vmt', 'prestamo.tipo_pago as tp',  'prestamo.interes as in','prestamo.cuotas as tc','prestamo.observacion_prestamo as obspo','prestamo.idp as poid','prestamo.created_at as fhpo',
    'usuario.usuario as emp',
    DB::raw('CONCAT(cliente.nombres," ",cliente.apellidos) as cli'));
        return  DataTables()->of($data)
        ->make(true);

    }
    }

}

// Detalle de gastos para informe
public function informesg(Request $request)
{  

    $fechaAi= Carbon::now()->toDateString()." 00:00:01";
    $fechaAf= Carbon::now()->toDateString()." 23:59:59";
    $empleado_id = $request->session()->get('empleado_id');
    $usuario_id = $request->session()->get('usuario_id');
    $empresaLogin = DB::table('empleado')->Join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
    ->where('empleado.ide', '=', $empleado_id)->first();
    
    $usuario = $request->usuario;
    $fechaini = $request->fechaini;
    $fechafin = $request->fechafin;

    if(request()->ajax()){

    if(!empty($fechaini) && !empty($fechafin) && !empty($usuario) ){
       
        $fechaini = $request->fechaini." 00:00:01";
        $fechafin = $request->fechafin." 23:59:59";
    
    $data = DB::table('gasto')
    ->Join('usuario', 'gasto.usuario_id', '=', 'usuario.id')
    ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
    ->where([['empleado.empresa_id', '=',$empresaLogin->empresa_id],['usuario.id', '=', $usuario]])
    ->whereBetween('gasto.created_at', [$fechaini, $fechafin])
    ->select('gasto.idg as id', 'gasto.monto as monto', 'gasto.descripcion as descripcion', 'gasto.created_at as created_at',
    'usuario.usuario as emp');
    return  DataTables()->of($data)
    ->make(true);

    }else{

        $data = DB::table('gasto')
        ->Join('usuario', 'gasto.usuario_id', '=', 'usuario.id')
        ->Join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
        ->where([['empleado.empresa_id', '=',$empresaLogin->empresa_id],['usuario.id', '=', $usuario_id]])
        ->whereBetween('gasto.created_at', [$fechaAi, $fechaAf])
        ->select('gasto.idg as id', 'gasto.monto as monto', 'gasto.descripcion as descripcion', 'gasto.created_at as created_at',
        'usuario.usuario as emp');
        return  DataTables()->of($data)
        ->make(true);

    }
    }

}
   
}
