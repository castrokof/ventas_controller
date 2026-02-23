<?php

namespace App\Http\Controllers;

use App\Models\Admin\Cliente;
use App\Models\Admin\DetallePrestamo;
use App\Models\Admin\Prestamo;
use App\Models\Seguridad\Usuario;
use App\Models\Admin\Pago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrestamoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $usuario_id = $request->session()->get('usuario_id');
        
        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();
                            
        $usuarios = Usuario::orderBy('id')->where('id', '=', $usuario_id)->pluck('usuario', 'id')->toArray();

     
        if($request->ajax()){

            $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where([['prestamo.usuario_id', '=', $usuario_id],['prestamo.monto_pendiente', '>', 0], ['prestamo.delete_at', '=', null]])->get();
            return  DataTables()->of($datas)
            // ->addColumn('editar', '<a href="{{url("cliente/$id/editar")}}" class="btn-accion-tabla tooltipsC" title="Editar este cliente">
            //       <i class="fa fa-fw fa-pencil-alt"></i>
            //     </a>')
            ->addColumn('action', function($datas){
        //   $button = '<button type="button" name="edit" id="'.$datas->prestamo->id.'"
        //   class = "edit btn-float  bg-gradient-primary btn-sm tooltipsC"  title="Editar Prestamo"><i class="far fa-edit"></i></button>';
          $button ='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
          class = "pagos btn-float  bg-gradient-warning btn-sm tooltipsC" title="Detalle de Pagos"><i class="fas fa-atlas"></i><i class="fas fa-money-bill-alt"></i></button>';
          $button .='&nbsp;<button type="button" name="detalle" id="'.$datas->idp.'"
          class = "detalle btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Cuotas"><i class="fas fa-atlas"></i></i></button>';
          return $button;

            }) 
            ->rawColumns(['action'])
            ->make(true);
            }
        return view('admin.prestamo.index', compact('usuarios', 'clientes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function guardar(Request $request)
    {
        $rules = array(
            'monto'  => 'required',
            'tipo_pago'  => 'required',
            'cuotas' => 'required',
            'interes' => 'required',
            'valor_cuota' => 'required',
            'fecha_inicial' => 'required',
            'usuario_id' => 'required',
            'activo' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        Prestamo::create($request->all());

        $id = DB::table('prestamo')->orderBy('idp','desc')->limit(1)->select('idp')->get();
       
        $cuotas = $request->cuotas;
        $fechaInicial = $request->fecha_inicial;
        $tipo_pago = $request->tipo_pago;
        $valorcuota = $request->valor_cuota;
        
        if( $tipo_pago == "Semanal"){
        
         $numero_cuota = 1;
       
        foreach($id as $ids) {
        for ($i=0; $i < $cuotas; $i++) { 
        
        DB::table('detalle_prestamo')
        ->insert([
        'd_numero_cuota'=> $numero_cuota,
        'valor_cuota'=> $valorcuota,
        'fecha_cuota'=> $fechaInicial,
        'estado'=> 'C',
        'activo'=> 1,
        'prestamo_id'=> $ids->idp,
        'created_at'=> now()
        ]);

        $numero_cuota++;
       
         //comprueba si el año es bisiesto o no
         if(date('L', strtotime($fechaInicial)) == 1){

            //Comprueba si es diciembre
            if(date('m', strtotime($fechaInicial)) == 12){

            if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 352 || date('z', strtotime($fechaInicial)) == 359)){
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
            else if((date('N', strtotime($fechaInicial)) == 5 || date('N', strtotime($fechaInicial)) == 1) && (date('z', strtotime($fechaInicial)) == 352 || date('z', strtotime($fechaInicial)) == 359)) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(8)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 351 || date('z', strtotime($fechaInicial)) == 358)) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(9)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) != 6 && date('N', strtotime($fechaInicial)) != 5 && (date('z', strtotime($fechaInicial)) == 352 || date('z', strtotime($fechaInicial)) == 359) ) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) == 6 ) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
            else
            {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
            }
            else if(date('N', strtotime($fechaInicial)) == 6 && date('m', strtotime($fechaInicial)) != 12) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
            else if(date('m', strtotime($fechaInicial)) != 12)
            { 
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
                    
        
         }else{
         //Comprueba si es diciembre
        if(date('m', strtotime($fechaInicial)) == 12){

        if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 351 || date('z', strtotime($fechaInicial)) == 358)){
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(9)->toDateString();
        }
        else if((date('N', strtotime($fechaInicial)) == 5 || date('N', strtotime($fechaInicial)) == 1) && (date('z', strtotime($fechaInicial)) == 351 || date('z', strtotime($fechaInicial)) == 358)) {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(8)->toDateString();
        }
        else if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 350 || date('z', strtotime($fechaInicial)) == 357)) {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(8)->toDateString();
        }
        else if(date('N', strtotime($fechaInicial)) != 6 && date('N', strtotime($fechaInicial)) != 5 && date('N', strtotime($fechaInicial)) != 1 && (date('z', strtotime($fechaInicial)) == 351 || date('z', strtotime($fechaInicial)) == 358) ) {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
        }
        else if(date('N', strtotime($fechaInicial)) == 6 ) {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
        }
        else
        {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
        } 
        // meses normales sin diciembre
        }
        else if(date('N', strtotime($fechaInicial)) == 6 && date('m', strtotime($fechaInicial)) != 12) {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
        }
        else if(date('m', strtotime($fechaInicial)) != 12)
        { 
           $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
        }
                
        }

        }
        }
        
        }else if ($tipo_pago == "Quincenal") {
            $numero_cuota = 1;
       
            foreach($id as $ids) {
            for ($i=0; $i < $cuotas; $i++) { 
            
            DB::table('detalle_prestamo')
            ->insert([
            'd_numero_cuota'=> $numero_cuota,
            'valor_cuota'=> $valorcuota,
            'fecha_cuota'=> $fechaInicial,
            'estado'=> 'C',
            'activo'=> 1,
            'prestamo_id'=> $ids->idp,
            'created_at'=> now()
            ]);
    
            $numero_cuota++;
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(15)->toDateString();
            
                    
            }
    
            }
        }else if ($tipo_pago == "Mensual") {
            $numero_cuota = 1;
       
            foreach($id as $ids) {
            for ($i=0; $i < $cuotas; $i++) { 
            
            DB::table('detalle_prestamo')
            ->insert([
            'd_numero_cuota'=> $numero_cuota,
            'valor_cuota'=> $valorcuota,
            'fecha_cuota'=> $fechaInicial,
            'estado'=> 'C',
            'activo'=> 1,
            'prestamo_id'=> $ids->idp,
            'created_at'=> now()
            ]);
    
            $numero_cuota++;
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(30)->toDateString();
            
                    
            }
    
            }
        }else if ($tipo_pago == "Diario") {
            $numero_cuota = 1;
       
            foreach($id as $ids) {
            for ($i=0; $i < $cuotas; $i++) { 
            
             DB::table('detalle_prestamo')
            ->insert([
            'd_numero_cuota'=> $numero_cuota,
            'valor_cuota'=> $valorcuota,
            'fecha_cuota'=> $fechaInicial,
            'estado'=> 'C',
            'activo'=> 1,
            'prestamo_id'=> $ids->idp,
            'created_at'=> now()
            ]);
             
            

            $numero_cuota++;
            
          
            //comprueba si el año es bisiesto o no
            if(date('L', strtotime($fechaInicial)) == 1){

                //Comprueba si es diciembre
                if(date('m', strtotime($fechaInicial)) == 12){

                if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 358 || date('z', strtotime($fechaInicial)) == 365)){
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
                }
                else if(date('N', strtotime($fechaInicial)) == 5 && (date('z', strtotime($fechaInicial)) == 358 || date('z', strtotime($fechaInicial)) == 365)) {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(3)->toDateString();
                }
                else if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 357 || date('z', strtotime($fechaInicial)) == 364)) {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(3)->toDateString();
                }
                else if(date('N', strtotime($fechaInicial)) != 6 && date('N', strtotime($fechaInicial)) != 5 && (date('z', strtotime($fechaInicial)) == 358 || date('z', strtotime($fechaInicial)) == 365) ) {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
                }
                else if(date('N', strtotime($fechaInicial)) == 6 ) {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
                }
                else
                {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(1)->toDateString();
                }
                }
                else if(date('N', strtotime($fechaInicial)) == 6 && date('m', strtotime($fechaInicial)) != 12) {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
                }
                else if(date('m', strtotime($fechaInicial)) != 12)
                { 
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(1)->toDateString();
                }
                        
            
             }else{
             //Comprueba si es diciembre
            if(date('m', strtotime($fechaInicial)) == 12){

            if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 357 || date('z', strtotime($fechaInicial)) == 364)){
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) == 5 && (date('z', strtotime($fechaInicial)) == 357 || date('z', strtotime($fechaInicial)) == 364)) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(3)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 356 || date('z', strtotime($fechaInicial)) == 363)) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(3)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) != 6 && date('N', strtotime($fechaInicial)) != 5 && (date('z', strtotime($fechaInicial)) == 357 || date('z', strtotime($fechaInicial)) == 364) ) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) == 6 ) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
            }
            else
            {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(1)->toDateString();
            } 
            // meses normales sin diciembre
            }
            else if(date('N', strtotime($fechaInicial)) == 6 && date('m', strtotime($fechaInicial)) != 12) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
            }
            else if(date('m', strtotime($fechaInicial)) != 12)
            { 
               $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(1)->toDateString();
            }
            }
            
            }
    
            }
        }
        
            return response()->json(['success' => 'ok']);
        }
    
   public function anularp($id)
    {
        
        if(request()->ajax()){

        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->where('prestamo.idp', '=', $id)
        ->update([
            'prestamo.estado' => 'A',
            'prestamo.delete_at' => now()
            ]);
       
        
            return response()->json(['success'=>'ok']);

        }
        



    }
    
   public function refinanciar(Request $request, $id)
    {
        
        if(request()->ajax()){

        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->where('prestamo.idp', '=', $id)
        ->select('cliente.nombres as nombres', 'cliente.apellidos as apellidos', 'prestamo.idp as idp', 'cliente.id as id',
        'prestamo.monto_total as monto_total', 'detalle_prestamo.d_numero_cuota as d_numero_cuota', 'detalle_prestamo.fecha_cuota as fecha_cuota', 'prestamo.monto as monto', 
        'prestamo.cuotas as cuotas', 'prestamo.interes as interes', 'prestamo.valor_cuota as valor_cuota', 'prestamo.tipo_pago as tipo_pago', 'prestamo.usuario_id as usuario_id', 'prestamo.monto_pendiente as monto_pendiente'
                )->get();
        
        
            return response()->json(['result'=>$data]);

        }
        



    }    
    
    
    public function refiguardar(Request $request)
    {
        $rules = array(
            'monto'  => 'required',
            'tipo_pago'  => 'required',
            'cuotas' => 'required',
            'interes' => 'required',
            'valor_cuota' => 'required',
            'fecha_inicial' => 'required',
            'usuario_id' => 'required',
            'activo' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

            Pago::create($request->all());

            DB::table('detalle_prestamo')
            ->where('prestamo_id', '=', $request->prestamo_id)
            ->update([
            'estado'=>'T',
            'updated_at'=> now() 
            ]);
            
            DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['d_numero_cuota', '=', $request->numero_cuota]])
            ->update([
            'valor_cuota_pagada'=>$request->valor_abono,
            'updated_at'=> now() 
            ]);

            $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
           
            $cuotasat = $saldoa->cuotas_atrasadas;
            $saldoat = $saldoa->monto_atrasado;
            
            //Actualizo todo los campos en ceros de cuotas atrasadas, monto atrasado, capital y el estado cambia a cancelado
            DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_atrasado'=> 0,
            'cuotas_atrasadas'=> 0,
            'monto_pendiente'=>($saldop - $request->valor_abono),
            'observacion_prestamo'=>'Cancelado',
            'estado'=>'P',
            'updated_at'=> now()
            ]);
            
          

        Prestamo::create($request->all());

        $id = DB::table('prestamo')->orderBy('idp','desc')->limit(1)->select('idp')->get();
       
        $cuotas = $request->cuotas;
        $fechaInicial = $request->fecha_inicial;
        $tipo_pago = $request->tipo_pago;
        $valorcuota = $request->valor_cuota;
        
        if( $tipo_pago == "Semanal"){
        
         $numero_cuota = 1;
       
        foreach($id as $ids) {
        for ($i=0; $i < $cuotas; $i++) { 
        
        DB::table('detalle_prestamo')
        ->insert([
        'd_numero_cuota'=> $numero_cuota,
        'valor_cuota'=> $valorcuota,
        'fecha_cuota'=> $fechaInicial,
        'estado'=> 'C',
        'activo'=> 1,
        'prestamo_id'=> $ids->idp,
        'created_at'=> now()
        ]);

        $numero_cuota++;
       
         //comprueba si el año es bisiesto o no
         if(date('L', strtotime($fechaInicial)) == 1){

            //Comprueba si es diciembre
            if(date('m', strtotime($fechaInicial)) == 12){

            if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 352 || date('z', strtotime($fechaInicial)) == 359)){
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
            else if((date('N', strtotime($fechaInicial)) == 5 || date('N', strtotime($fechaInicial)) == 1) && (date('z', strtotime($fechaInicial)) == 352 || date('z', strtotime($fechaInicial)) == 359)) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(8)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 351 || date('z', strtotime($fechaInicial)) == 358)) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(9)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) != 6 && date('N', strtotime($fechaInicial)) != 5 && (date('z', strtotime($fechaInicial)) == 352 || date('z', strtotime($fechaInicial)) == 359) ) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) == 6 ) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
            else
            {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
            }
            else if(date('N', strtotime($fechaInicial)) == 6 && date('m', strtotime($fechaInicial)) != 12) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
            else if(date('m', strtotime($fechaInicial)) != 12)
            { 
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
            }
                    
        
         }else{
         //Comprueba si es diciembre
        if(date('m', strtotime($fechaInicial)) == 12){

        if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 351 || date('z', strtotime($fechaInicial)) == 358)){
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(9)->toDateString();
        }
        else if((date('N', strtotime($fechaInicial)) == 5 || date('N', strtotime($fechaInicial)) == 1) && (date('z', strtotime($fechaInicial)) == 351 || date('z', strtotime($fechaInicial)) == 358)) {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(8)->toDateString();
        }
        else if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 350 || date('z', strtotime($fechaInicial)) == 357)) {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(8)->toDateString();
        }
        else if(date('N', strtotime($fechaInicial)) != 6 && date('N', strtotime($fechaInicial)) != 5 && date('N', strtotime($fechaInicial)) != 1 && (date('z', strtotime($fechaInicial)) == 351 || date('z', strtotime($fechaInicial)) == 358) ) {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
        }
        else if(date('N', strtotime($fechaInicial)) == 6 ) {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
        }
        else
        {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
        } 
        // meses normales sin diciembre
        }
        else if(date('N', strtotime($fechaInicial)) == 6 && date('m', strtotime($fechaInicial)) != 12) {
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
        }
        else if(date('m', strtotime($fechaInicial)) != 12)
        { 
           $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(7)->toDateString();
        }
                
        }

        }
        }
        
        }else if ($tipo_pago == "Quincenal") {
            $numero_cuota = 1;
       
            foreach($id as $ids) {
            for ($i=0; $i < $cuotas; $i++) { 
            
            DB::table('detalle_prestamo')
            ->insert([
            'd_numero_cuota'=> $numero_cuota,
            'valor_cuota'=> $valorcuota,
            'fecha_cuota'=> $fechaInicial,
            'estado'=> 'C',
            'activo'=> 1,
            'prestamo_id'=> $ids->idp,
            'created_at'=> now()
            ]);
    
            $numero_cuota++;
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(15)->toDateString();
            
                    
            }
    
            }
        }else if ($tipo_pago == "Mensual") {
            $numero_cuota = 1;
       
            foreach($id as $ids) {
            for ($i=0; $i < $cuotas; $i++) { 
            
            DB::table('detalle_prestamo')
            ->insert([
            'd_numero_cuota'=> $numero_cuota,
            'valor_cuota'=> $valorcuota,
            'fecha_cuota'=> $fechaInicial,
            'estado'=> 'C',
            'activo'=> 1,
            'prestamo_id'=> $ids->idp,
            'created_at'=> now()
            ]);
    
            $numero_cuota++;
            $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(30)->toDateString();
            
                    
            }
    
            }
        }else if ($tipo_pago == "Diario") {
            $numero_cuota = 1;
       
            foreach($id as $ids) {
            for ($i=0; $i < $cuotas; $i++) { 
            
             DB::table('detalle_prestamo')
            ->insert([
            'd_numero_cuota'=> $numero_cuota,
            'valor_cuota'=> $valorcuota,
            'fecha_cuota'=> $fechaInicial,
            'estado'=> 'C',
            'activo'=> 1,
            'prestamo_id'=> $ids->idp,
            'created_at'=> now()
            ]);
             
            

            $numero_cuota++;
            
          
            //comprueba si el año es bisiesto o no
            if(date('L', strtotime($fechaInicial)) == 1){

                //Comprueba si es diciembre
                if(date('m', strtotime($fechaInicial)) == 12){

                if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 358 || date('z', strtotime($fechaInicial)) == 365)){
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
                }
                else if(date('N', strtotime($fechaInicial)) == 5 && (date('z', strtotime($fechaInicial)) == 358 || date('z', strtotime($fechaInicial)) == 365)) {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(3)->toDateString();
                }
                else if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 357 || date('z', strtotime($fechaInicial)) == 364)) {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(3)->toDateString();
                }
                else if(date('N', strtotime($fechaInicial)) != 6 && date('N', strtotime($fechaInicial)) != 5 && (date('z', strtotime($fechaInicial)) == 358 || date('z', strtotime($fechaInicial)) == 365) ) {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
                }
                else if(date('N', strtotime($fechaInicial)) == 6 ) {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
                }
                else
                {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(1)->toDateString();
                }
                }
                else if(date('N', strtotime($fechaInicial)) == 6 && date('m', strtotime($fechaInicial)) != 12) {
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
                }
                else if(date('m', strtotime($fechaInicial)) != 12)
                { 
                    $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(1)->toDateString();
                }
                        
            
             }else{
             //Comprueba si es diciembre
            if(date('m', strtotime($fechaInicial)) == 12){

            if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 357 || date('z', strtotime($fechaInicial)) == 364)){
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) == 5 && (date('z', strtotime($fechaInicial)) == 357 || date('z', strtotime($fechaInicial)) == 364)) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(3)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) == 6 && (date('z', strtotime($fechaInicial)) == 356 || date('z', strtotime($fechaInicial)) == 363)) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(3)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) != 6 && date('N', strtotime($fechaInicial)) != 5 && (date('z', strtotime($fechaInicial)) == 357 || date('z', strtotime($fechaInicial)) == 364) ) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
            }
            else if(date('N', strtotime($fechaInicial)) == 6 ) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
            }
            else
            {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(1)->toDateString();
            } 
            // meses normales sin diciembre
            }
            else if(date('N', strtotime($fechaInicial)) == 6 && date('m', strtotime($fechaInicial)) != 12) {
                $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(2)->toDateString();
            }
            else if(date('m', strtotime($fechaInicial)) != 12)
            { 
               $fechaInicial = Carbon::createFromFormat('Y-m-d',$fechaInicial)->addDay(1)->toDateString();
            }
            }
            
            }
    
            }
        }
        
            return response()->json(['success' => 'ok']);
        }
    
    
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detalle($id)
    {
        
        if(request()->ajax()){

        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->where('prestamo.cliente_id', '=', $id)->get();
        
            $datacuotas = DB::table('prestamo')
             ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
             ->where('detalle_prestamo.prestamo_id', '=', $id)->get();
    
        // $data = Prestamo::with('Cliente:nombres')->where('cliente_id', $id)->first();
            return response()->json(['result'=>$data, 'result1'=>$datacuotas ]);

        }
        



    }
    
    
     public function detallep($id)
    {
        
        if(request()->ajax()){

        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->where('prestamo.idp', '=', $id)->get();
       
        
            return response()->json(['result'=>$data]);

        }
        



    }
    
    
    
    
    
    public function detallepn($id)
    {
        
        if(request()->ajax()){

        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->select('cliente.nombres', 'cliente.apellidos', 'cliente.direccion', 'cliente.celular', 'prestamo.monto',
        'prestamo.interes', 'prestamo.monto_total', 'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas', 'prestamo.tipo_pago', 'prestamo.idp', 'prestamo.cuotas_atrasadas', 'prestamo.monto_atrasado',
        'prestamo.fecha_inicial', 'cliente.id', 'prestamo.created_at')
        ->where('prestamo.idp', '=', $id)->get();
       
        
            return response()->json(['result'=>$data]);

        }
        



    }

     public function indexPrestamoApp(Request $request)
    {
     
            $useractivo = Usuario::where([
            ['usuario', '=', $request->usuario],
            ['activo', '=', 1]
        
            ])->count();
            
            
            if($useractivo >= 1){  
                
            $usuario_id = $request->usuario_id;
        
       
            $datasprestamo = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where([['prestamo.usuario_id', '=', $usuario_id],['prestamo.monto_pendiente', '>', 0], ['prestamo.delete_at', '=', null]])->orderBy('cliente.consecutivo')->get(); 
            
       
            
            
               return Response()->json(['datasprestamo'=> $datasprestamo], 200);

            } else {
              return response()->json(['error'=> 'Unauthorised'], 400);
            }

  
         
         
           
        
    }
    
      public function DetallePrestamoApp(Request $request)
    {
        
         $useractivo = Usuario::where([
            ['usuario', '=', $request->usuario],
            ['activo', '=', 1]
        
            ])->count();
            
               if($useractivo >= 1){  
                
             $id = $request->prestamo_id;
        
       
            $datacuotas = DB::table('prestamo')
             ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
             ->where('detalle_prestamo.prestamo_id', '=', $id)->get();
            
       
            
            
               return Response()->json(['datasprestamodetalle'=> $datacuotas], 200);

            } else {
              return response()->json(['error'=> 'Unauthorised'], 400);
            }

            


        }
        



    
}
