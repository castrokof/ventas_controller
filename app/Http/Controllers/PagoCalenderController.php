<?php

namespace App\Http\Controllers;

use App\Models\Admin\Cliente;
use App\Models\Admin\DetallePrestamo;
use App\Models\Admin\Pago;
use App\Models\Admin\Prestamo;
use App\Models\Seguridad\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psy\Command\WhereamiCommand;

class PagoCalenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
      public function indexcp(Request $request)
    {
       
        $usuario_id = $request->session()->get('usuario_id');
        
        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();
        $usuarioscp = Usuario::orderBy('id')->where('id', '=', $usuario_id)->pluck('usuario', 'id')->toArray();
        
   
        

      
         if($request->ajax()){
            
            $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where('prestamo.usuario_id', '=', $usuario_id)
            ->where('prestamo.monto_pendiente', '>', 0)
            ->where('prestamo.delete_at', '=', null)
            ->select('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas', 'cliente.telefono', 'cliente.celular', 'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas')
            ->groupBy('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas', 'cliente.telefono', 'cliente.celular', 'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas')
            ->get();
            return  DataTables()->of($datas)
                ->addColumn('action', function($datas){
                $button ='<div class="row">
                            <div class="col-md-6 col-xs-12">
                            <!-- Application buttons -->
                            <div class="card">
                            <div class="card-body bg-olive">
                               <a class="payp btn btn-flotante btn-app bg-secondary"  id="'.$datas->idp.'" idf="'.now()->toDateString().'">
                                  <span class="badge bg-success">Credito: '.$datas->idp.'</span>
                                  <i class="fa fa-plus-circle fa-w-16 fa-spin fa-lg"></i> Pago
                                </a>
                               <a class="detallepay btn btn-flotante btn-app bg-info" id="'.$datas->idp.'">
                                  <span class="badge bg-teal">Cuotas Pdts: '.round($datas->monto_pendiente/$datas->valor_cuota).'/'.$datas->cuotas.'</span>
                                  <i class="fas fa-book-reader"></i> Detalle Pay
                                </a>
                                <a class="adelantoc btn btn-flotante btn-app bg-warning" id="'.$datas->idp.'">
                                  <span class="badge bg-info">Valor cuota $'.$datas->valor_cuota.'</span>
                                  <i class="fa fa-credit-card"></i> Add Cuotas
                                </a>
                                <a class="atrasosp btn btn-flotante btn-app bg-danger" id="'.$datas->idp.'">
                                  <span class="badge bg-warning">'.$datas->cuotas_atrasadas.'</span>
                                  <i class="fa fa-credit-card"></i> Atrasos
                                </a>
                              </div>
                              <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                          </div>
                          </div>';
                          
                return $button;
                
                
            })  ->addColumn('datos', function($datas){
                 $datosp ='<div class="row">
                             <div class="col-md-6 col-xs-12">
                                        <div class="small-box bg-olive">
                                          <div class="inner">
                                            <h4>'.$datas->nombres.'</h4>
                                            <h4>'.$datas->apellidos.'</h4>
                            
                                            <p><i class="fas fa-map-marked"></i>: '.$datas->direccion.'</p>
                                             <p><i class="fas fa-phone-square-alt"></i>: '.$datas->telefono.'</p>
                                             <p><a href="tel:+'.$datas->celular.'"> <i class="fas fa-mobile-alt"></i>: '.$datas->celular.'</a></p>
                                             <p><i class="fas fa-sort-amount-down-alt"></i>: '.$datas->consecutivo.'</p>
                                             
                                          </div>
                                          <div class="icon">
                                           
                                          </div>
                                          <button type="button"  class="detalle btn btn-flotante1 btn-app bg-secondary small-box-footer" id="'.$datas->idp.'">
                                          <span class="badge bg-teal">Saldo: '.$datas->monto_pendiente.'</span>
                                            <i class="fa fa-atlas fa-w-14 fa-spin fa-lg"></i>#Credit '.$datas->idp.' 
                                          </button>
                                        </div>
                                    </div>    
                             </div>   
                          </div>';
               
                return $datosp;
                
                
            })
            ->rawColumns(['action','datos'])
            ->make(true);


            }
           
        return view('admin.pago_card.index', compact('datas','clientes','usuarioscp'));
    }
     
     public function indexc(Request $request)
    {
        $pagos_registrados = $request->estado_pago;
        $prestamoc_id = $request->prestamoc_id;

        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";


        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->session()->get('usuario_id');
        
        $usuarios = Usuario::orderBy('id')->where('id', '=', $usuario_id)->pluck('usuario', 'id')->toArray();
        
        $usuarioscp = Usuario::orderBy('id')->where('id', '=', $usuario_id)->pluck('usuario', 'id')->toArray();
        
        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();
        
        $datasp = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where([['prestamo.usuario_id', '=', $usuario_id], ['prestamo.estado', '!=', 'P']])
            ->pluck('cliente.nombres as nombres','prestamo.idp as idp')->toArray();
            
         if($request->ajax()){
            
            if($pagos_registrados == 0 || $pagos_registrados == null ){
               
            $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '=', $fecha_Actual]])
            ->where('prestamo.delete_at', '=', null)
            ->whereIn('detalle_prestamo.estado', ['C'])
            ->where(function ($query) {
            $fechai= Carbon::now()->toDateString()." 00:00:01";
            $query->where('detalle_prestamo.updated_at', '<', $fechai)
             ->orWhere('detalle_prestamo.updated_at', '=', null);
            })
            ->get();
            
            return  DataTables()->of($datas)
                ->addColumn('action', function($datas){
                $button ='<div class="row">
                            <div class="col-md-6 col-xs-12">
                            <!-- Application buttons -->
                            <div class="card">
                            <div class="card-body">
                               <a class="pay btn btn-flotante btn-app bg-secondary"  id="'.$datas->idd.'">
                                  <span class="badge bg-success">Cuota: '.$datas->d_numero_cuota.'</span>
                                  <i class="fa fa-plus-circle fa-lg"></i> Pago cuota
                                </a>
                               <a class="detallepay btn btn-flotante btn-app bg-info" id="'.$datas->idp.'">
                                  <span class="badge bg-teal">Cuotas Pdts: '.round($datas->monto_pendiente/$datas->valor_cuota).'/'.$datas->cuotas.'</span>
                                  <i class="fas fa-book-reader"></i> Detalle Pay
                                </a>
                                <a class="adelantoc btn btn-flotante btn-app bg-warning" id="'.$datas->idp.'">
                                  <span class="badge bg-info">Valor cuota $'.$datas->valor_cuota.'</span>
                                  <i class="fa fa-credit-card"></i> Add Cuotas
                                </a>
                                <a class="atrasosp btn btn-flotante btn-app bg-danger" id="'.$datas->idp.'">
                                  <span class="badge bg-warning">'.$datas->cuotas_atrasadas.'</span>
                                  <i class="fa fa-credit-card"></i> Atrasos
                                </a>
                              </div>
                              <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                          </div>
                          </div>';
                return $button;
                
                
            })  ->addColumn('datos', function($datas){
                 $datosp ='<div class="row">
                             <div class="col-md-6 col-xs-12">
                                        <div class="small-box ">
                                          <div class="inner">
                                            <h4>'.$datas->nombres.'</h4>
                                            <h4>'.$datas->apellidos.'</h4>
                            
                                            <p><i class="fas fa-map-marked"></i>: '.$datas->direccion.'</p>
                                             <p><i class="fas fa-phone-square-alt"></i>: '.$datas->telefono.'</p>
                                             <p><a href="tel:+'.$datas->celular.'"> <i class="fas fa-mobile-alt"></i>: '.$datas->celular.'</a></p>
                                             <p><i class="fas fa-sort-amount-down-alt"></i>: '.$datas->consecutivo.'</p>
                                             
                                          </div>
                                          <div class="icon">
                                            
                                          </div>
                                          <button type="button" class="detalle btn btn-flotante1 btn-app bg-secondary small-box-footer" id="'.$datas->idp.'">
                                          <span class="badge bg-teal">Saldo: '.$datas->monto_pendiente.'</span>
                                            <i class="fa fa-atlas fa-lg"></i>#Credit '.$datas->idp.' 
                                          </button>
                                        </div>
                                    </div>    
                             </div>   
                          </div>';
               
                return $datosp;
                
                
            })
            ->rawColumns(['action','datos'])
            ->make(true);    

         } else if($pagos_registrados == 1){

                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where('prestamo.usuario_id', '=', $usuario_id)
                ->whereBetween('detalle_prestamo.updated_at', [$fechaAi, $fechaAf])
                ->whereIn('detalle_prestamo.estado', ['P','A'])
                ->get();
                return  DataTables()->of($datas)
                ->addColumn('action', function($datas){
                $button ='<div class="row">
                            <div class="col-md-6 col-xs-12">
                            <!-- Application buttons -->
                            <div class="card">
                            <div class="card-body bg-lightblue">
                               <a class="editpay btn btn-flotante btn-app bg-secondary"  id="'.$datas->idd.'">
                                  <span class="badge bg-success">Cuota: '.$datas->d_numero_cuota.'</span>
                                  <i class="fa fa-plus-circle fa-lg"></i> Registrar pago
                                </a>
                               <a class="detallepay btn btn-flotante btn-app bg-info" id="'.$datas->idp.'">
                                  <span class="badge bg-teal">Cuotas Pdts: '.round($datas->monto_pendiente/$datas->valor_cuota).'/'.$datas->cuotas.'</span>
                                  <i class="fas fa-book-reader"></i> Detalle Pay
                                </a>
                                <a class="adelantoc btn btn-flotante btn-app bg-warning" id="'.$datas->idp.'">
                                  <span class="badge bg-info">Valor cuota $'.$datas->valor_cuota.'</span>
                                  <i class="fa fa-credit-card"></i> Add Cuotas
                                </a>
                                <a class="atrasosp btn btn-flotante btn-app bg-danger" id="'.$datas->idp.'">
                                  <span class="badge bg-warning">'.$datas->cuotas_atrasadas.'</span>
                                  <i class="fa fa-credit-card"></i> Atrasos
                                </a>
                              </div>
                              <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                          </div>
                          </div>';
                return $button;
                
                
            })  ->addColumn('datos', function($datas){
                 $datosp ='<div class="row">
                             <div class="col-md-6 col-xs-12">
                                        <div class="small-box bg-lightblue">
                                          <div class="inner">
                                            <h4>'.$datas->nombres.'</h4>
                                            <h4>'.$datas->apellidos.'</h4>
                            
                                            <p><i class="fas fa-map-marked"></i>: '.$datas->direccion.'</p>
                                             <p><i class="fas fa-phone-square-alt"></i>: '.$datas->telefono.'</p>
                                             <p><a href="tel:+'.$datas->celular.'"> <i class="fas fa-mobile-alt"></i>: '.$datas->celular.'</a></p>
                                             <p><i class="fas fa-sort-amount-down-alt"></i>: '.$datas->consecutivo.'</p>
                                             
                                          </div>
                                          <div class="icon">
                                            
                                          </div>
                                          <button type="button" class="detalle btn btn-flotante1 btn-app bg-secondary small-box-footer" id="'.$datas->idp.'">
                                          <span class="badge bg-teal">Saldo: '.$datas->monto_pendiente.'</span>
                                            <i class="fa fa-atlas fa-lg"></i>#Credit '.$datas->idp.' 
                                          </button>
                                        </div>
                                    </div>    
                             </div>   
                          </div>';
               
                return $datosp;
                
                
            })
            ->rawColumns(['action','datos'])
            ->make(true);   

            }else if($pagos_registrados == 2){

                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '<', $fecha_Actual]])
                ->where(function ($query) {
                $fechai= Carbon::now()->toDateString()." 00:00:01";
                $query->where('detalle_prestamo.updated_at', '<', $fechai)
                      ->orWhere('detalle_prestamo.updated_at', '=', null);
                      
                })
                ->whereIn('detalle_prestamo.estado', ['A','C'])
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button ='<button type="button" name="prestamo" id="'.$datas->idd.'"
                    class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i>
                    <i class="fas fa-money-bill-alt"></i></button>';
                    
                   // $button .= '<div id="edicdes"><button type="button" name="edit" id="'.$datas->idd.'"
                    //class = "editpay btn-float  bg-gradient-primary btn-sm tooltipsC"  title="Editar Pago"><i class="far fa-edit"></i>
                    //</button></div>';
                    
                   // $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    //class = "detallepay btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Pago"><i class="fas fa-atlas"></i>
                    //</i></button>';
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }else if($pagos_registrados == 3 && $prestamoc_id == null ){
               
                
                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '>', $fecha_Actual]])
                ->whereIn('detalle_prestamo.estado', ['C'])
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button ='&nbsp;<button type="button" name="prestamo" id="'.$datas->idd.'"
                    class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>';
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }else if($pagos_registrados == 4 || $pagos_registrados == null ){
               
                
            $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where('prestamo.usuario_id', '=', $usuario_id)
            ->where('prestamo.monto_pendiente', '>', 0)
            ->where(function ($query) {
            $fechai= Carbon::now()->toDateString()." 00:00:01";
            $query->where('prestamo.updated_at', '<', $fechai)
            ->orWhere('prestamo.updated_at', '=', null);})
            ->whereIn('detalle_prestamo.estado', ['A','C'])
            ->where('detalle_prestamo.fecha_cuota', '<=', Carbon::now()->toDateString())
            ->where('prestamo.delete_at', '=', null)
            ->select('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas', 'cliente.telefono', 'cliente.celular', 'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas')
            ->groupBy('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas', 'cliente.telefono', 'cliente.celular', 'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas')
            ->get();
            return  DataTables()->of($datas)
                ->addColumn('action', function($datas){
                $button ='<div class="row">
                            <div class="col-md-6 col-xs-12">
                            <!-- Application buttons -->
                            <div class="card">
                            <div class="card-body">
                               <a class="payp btn btn-flotante btn-app bg-secondary"  id="'.$datas->idp.'" idf="'.now()->toDateString().'">
                                  <span class="badge bg-success">Credito: '.$datas->idp.'</span>
                                  <i class="fa fa-plus-circle fa-w-16 fa-spin fa-lg"></i> Pago
                                </a>
                               <a class="detallepay btn btn-flotante btn-app bg-info" id="'.$datas->idp.'">
                                  <span class="badge bg-teal">Cuotas Pdts: '.round($datas->monto_pendiente/$datas->valor_cuota).'/'.$datas->cuotas.'</span>
                                  <i class="fas fa-book-reader"></i> Detalle Pay
                                </a>
                                <a class="adelantoc btn btn-flotante btn-app bg-warning" id="'.$datas->idp.'">
                                  <span class="badge bg-info">Valor cuota $'.$datas->valor_cuota.'</span>
                                  <i class="fa fa-credit-card"></i> Add Cuotas
                                </a>
                                <a class="atrasosp btn btn-flotante btn-app bg-danger" id="'.$datas->idp.'">
                                  <span class="badge bg-warning">'.$datas->cuotas_atrasadas.'</span>
                                  <i class="fa fa-credit-card"></i> Atrasos
                                </a>
                              </div>
                              <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                          </div>
                          </div>';
                return $button;
                
                
            })  ->addColumn('datos', function($datas){
                 $datosp ='<div class="row">
                             <div class="col-md-6 col-xs-12">
                                        <div class="small-box bg-info">
                                          <div class="inner">
                                            <h4>'.$datas->nombres.'</h4>
                                            <h4>'.$datas->apellidos.'</h4>
                            
                                            <p><i class="fas fa-map-marked"></i>: '.$datas->direccion.'</p>
                                             <p><i class="fas fa-phone-square-alt"></i>: '.$datas->telefono.'</p>
                                             <p><a href="tel:+'.$datas->celular.'"> <i class="fas fa-mobile-alt"></i>: '.$datas->celular.'</a></p>
                                             <p><i class="fas fa-sort-amount-down-alt"></i>: '.$datas->consecutivo.'</p>
                                             
                                          </div>
                                          <div class="icon">
                                            
                                          </div>
                                          <button type="button" class="detalle btn btn-flotante1 btn-app bg-secondary small-box-footer" id="'.$datas->idp.'">
                                          <span class="badge bg-teal">Saldo: '.$datas->monto_pendiente.'</span>
                                            <i class="fa fa-atlas fa-w-14 fa-spin fa-lg"></i>#Credit '.$datas->idp.' 
                                          </button>
                                        </div>
                                    </div>    
                             </div>   
                          </div>';
               
                return $datosp;
                
                
            })
            ->rawColumns(['action','datos'])
            ->make(true);



            }else if($pagos_registrados == 5){
               
                
                 $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where('prestamo.usuario_id', '=', $usuario_id)
            ->whereBetween('prestamo.updated_at', [$fechaAi, $fechaAf])
            ->whereIn('detalle_prestamo.estado', ['A','P'])
            ->select('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas', 'cliente.telefono', 'cliente.celular','prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas')
            ->groupBy('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas', 'cliente.telefono', 'cliente.celular', 'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas')
            ->get();
            return  DataTables()->of($datas)
                ->addColumn('action', function($datas){
                 $button ='<div class="row">
                            <div class="col-md-6 col-xs-12">
                            <!-- Application buttons -->
                            <div class="card">
                          
                              <div class="card-body">
                               <a class="pagosr btn btn-flotante btn-app bg-success"  id="'.$datas->idp.'">
                                  <span class="badge bg-success">Credito: '.$datas->idp.'</span>
                                  <i class="fa fa-plus-circle fa-w-16 fa-spin fa-lg"></i> Pay registrados
                                </a>
                                <a class="detallepay btn btn-flotante btn-app bg-info" id="'.$datas->idp.'">
                                  <span class="badge bg-teal">Cuotas Pdts: '.round($datas->monto_pendiente/$datas->valor_cuota).'/'.$datas->cuotas.'</span>
                                  <i class="fas fa-book-reader"></i> Detalle Pay
                                </a>
                                <a class="adelantoc btn btn-flotante btn-app bg-warning" id="'.$datas->idp.'">
                                  <span class="badge bg-info">Valor cuota $'.$datas->valor_cuota.'</span>
                                  <i class="fa fa-credit-card"></i> Add Cuotas
                                </a>
                                <a class="atrasosp btn btn-flotante btn-app bg-danger" id="'.$datas->idp.'">
                                  <span class="badge bg-warning">'.$datas->cuotas_atrasadas.'</span>
                                  <i class="fa fa-credit-card"></i> Atrasos
                                </a>
                              </div>
                              <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                
                          </div>
                          </div>';
               
                return $button;
                
                
            })
  ->addColumn('datos', function($datas){
                 $datosp ='<div class="row">
                             <div class="col-md-6 col-xs-12">
                                        <div class="small-box bg-warning">
                                          <div class="inner">
                                            <h4>'.$datas->nombres.'</h4>
                                            <h4>'.$datas->apellidos.'</h4>
                            
                                            <p><i class="fas fa-map-marked"></i>: '.$datas->direccion.'</p>
                                             <p><i class="fas fa-phone-square-alt"></i>: '.$datas->telefono.'</p>
                                             <p><a href="tel:+'.$datas->celular.'"> <i class="fas fa-mobile-alt"></i>: '.$datas->celular.'</a></p>
                                             <p><i class="fas fa-sort-amount-down-alt"></i>: '.$datas->consecutivo.'</p>
                                          </div>
                                          <div class="icon">
                                           
                                          </div>
                                          <button type="button" class="detalle btn btn-flotante1 btn-app bg-secondary small-box-footer" id="'.$datas->idp.'">
                                            <span class="badge bg-teal">Saldo: '.$datas->monto_pendiente.'</span>
                                            <i class="fa fa-atlas fa-w-14 fa-spin fa-lg"></i> #Credit '.$datas->idp.' 
                                          </button>
                                        </div>
                                    </div>    
                             </div>   
                          </div>';
               
                return $datosp;
                
                
            })
            ->rawColumns(['action','datos'])
            ->make(true);



            }

            


            }
        return view('admin.pago_card.index', compact('datas','clientes','datasp','usuarios','usuarioscp'));
    }
     
     
     
    public function index(Request $request)
    {
        $pagos_registrados = $request->estado_pago;
        $prestamoc_id = $request->prestamoc_id;

        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";


        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->session()->get('usuario_id');
        
        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();
        
        $datasp = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where([['prestamo.usuario_id', '=', $usuario_id], ['prestamo.estado', '!=', 'P']])
            ->pluck('cliente.nombres as nombres','prestamo.idp as idp')->toArray();
            
         if($request->ajax()){
            
            if($pagos_registrados == 6 || $pagos_registrados == null){
               
            $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '=', $fecha_Actual]])
           
            ->whereIn('detalle_prestamo.estado', ['C'])
            ->where('prestamo.delete_at', '=', null)
            ->where(function ($query) {
            $fechai= Carbon::now()->toDateString()." 00:00:01";
            $query->where('detalle_prestamo.updated_at', '<', $fechai)
             ->orWhere('detalle_prestamo.updated_at', '=', null);
            })
            ->get();
            return  DataTables()->of($datas)
                ->addColumn('action', function($datas){
                $button ='<button type="button" name="prestamo" id="'.$datas->idd.'" 
                class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i></button>
                ';
                $button .='&nbsp;<button type="button" name="detalle" id="'.$datas->idp.'"
                class = "detalle btn-float  bg-gradient-info btn-sm tooltipsC"><i class="fas fa-atlas"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                class = "detallepay btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Pago"><i class="fas fa-book-reader"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    class = "adelantoc btn-float  bg-gradient-secondary btn-sm tooltipsC" title="Adelanto de Cuotas"><i class="fa fa-credit-card"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                class = "atrasosp btn-float  bg-gradient-danger btn-sm tooltipsC" title="atrasos y pendiente"><i class="fa fa-credit-card"></i></button>';
               
                return $button;
                
                
            }) 
            ->rawColumns(['action'])
            ->make(true);

         } else if($pagos_registrados == 1 ){

                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where('prestamo.usuario_id', '=', $usuario_id)
                ->whereBetween('detalle_prestamo.updated_at', [$fechaAi, $fechaAf])
                ->whereIn('detalle_prestamo.estado', ['P','A'])
                ->where('prestamo.delete_at', '=', null)
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button = '<button type="button" name="edit" id="'.$datas->idd.'"
                    class = "editpay btn-float  bg-gradient-primary btn-sm tooltipsC"  title="Editar Pago"><i class="far fa-edit"></i></button>';
                    
                    $button .='&nbsp;<button type="button" name="detalle" id="'.$datas->idp.'"
                    class = "detalle btn-float  bg-gradient-info btn-sm tooltipsC"><i class="fas fa-atlas"></i></button>';
                
                    $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    class = "detallepay btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Pago"><i class="fas fa-book-reader"></i></button>';
                    
                    $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    class = "adelantoc btn-float  bg-gradient-secondary btn-sm tooltipsC" title="Adelanto de Cuotas"><i class="fa fa-credit-card"></i></button>';
                    
                    $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    class = "atrasosp btn-float  bg-gradient-danger btn-sm tooltipsC" title="atrasos y pendiente"><i class="fa fa-credit-card"></i></button>';
                    
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }else if($pagos_registrados == 2){

                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '<', $fecha_Actual]])
                ->where(function ($query) {
                $fechai= Carbon::now()->toDateString()." 00:00:01";
                $query->where('detalle_prestamo.updated_at', '<', $fechai)
                      ->orWhere('detalle_prestamo.updated_at', '=', null);
                      
                })
                ->whereIn('detalle_prestamo.estado', ['A','C'])
                ->where('prestamo.delete_at', '=', null)
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button ='<button type="button" name="prestamo" id="'.$datas->idd.'"
                    class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i>
                    <i class="fas fa-money-bill-alt"></i></button>';
                    
                   // $button .= '<div id="edicdes"><button type="button" name="edit" id="'.$datas->idd.'"
                    //class = "editpay btn-float  bg-gradient-primary btn-sm tooltipsC"  title="Editar Pago"><i class="far fa-edit"></i>
                    //</button></div>';
                    
                   // $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    //class = "detallepay btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Pago"><i class="fas fa-atlas"></i>
                    //</i></button>';
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }else if($pagos_registrados == 3 && $prestamoc_id == null ){
               
                
                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '>', $fecha_Actual]])
                ->whereIn('detalle_prestamo.estado', ['C'])
                ->where('prestamo.delete_at', '=', null)
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button ='&nbsp;<button type="button" name="prestamo" id="'.$datas->idd.'"
                    class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>';
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }else if($pagos_registrados == 4  ){
               
                
            $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where('prestamo.usuario_id', '=', $usuario_id)
            ->where('prestamo.monto_pendiente', '>', 0)
            ->where(function ($query) {
            $fechai= Carbon::now()->toDateString()." 00:00:01";
            $query->where('prestamo.updated_at', '<', $fechai)
            ->orWhere('prestamo.updated_at', '=', null);})
            ->whereIn('detalle_prestamo.estado', ['A','C'])
            ->where('detalle_prestamo.fecha_cuota', '<=', Carbon::now()->toDateString())
            ->where('prestamo.delete_at', '=', null)
            ->select('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas')
            ->groupBy('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas')
            ->get();
            return  DataTables()->of($datas)
                ->addColumn('action', function($datas){
                $button ='<button type="button" name="prestamo" id="'.$datas->idp.'" idf="'.now()->toDateString().'"
                class = "payp btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i></button>
                ';
                $button .='&nbsp;<button type="button" name="detalle" id="'.$datas->idp.'"
                class = "detalle btn-float  bg-gradient-info btn-sm tooltipsC"><i class="fas fa-atlas"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                class = "detallepay btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Pago"><i class="fas fa-book-reader"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    class = "adelantoc btn-float  bg-gradient-secondary btn-sm tooltipsC" title="Adelanto de Cuotas"><i class="fa fa-credit-card"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                class = "atrasosp btn-float  bg-gradient-danger btn-sm tooltipsC" title="atrasos y pendiente"><i class="fa fa-credit-card"></i></button>';
               
                return $button;
                
                
            }) 
            ->rawColumns(['action'])
            ->make(true);



            }else if($pagos_registrados == 5){
               
                
                 $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where('prestamo.usuario_id', '=', $usuario_id)
            ->whereBetween('prestamo.updated_at', [$fechaAi, $fechaAf])
            ->whereIn('detalle_prestamo.estado', ['A','P'])
            ->where('prestamo.delete_at', '=', null)
            ->select('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado')
            ->groupBy('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado')
            ->get();
            return  DataTables()->of($datas)
                ->addColumn('action', function($datas){
                /*$button ='<button type="button" name="prestamo" id="'.$datas->idp.'" idf="'.now()->toDateString().'"
                class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i></button>
                ';*/
                $button ='&nbsp;<button type="button" name="detalle" id="'.$datas->idp.'"
                class = "detalle btn-float  bg-gradient-info btn-sm tooltipsC"><i class="fas fa-atlas"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                class = "detallepay btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Pago"><i class="fas fa-book-reader"></i></button>';
               
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                class = "pagosr btn-float  bg-gradient-info btn-sm tooltipsC" title="pagos registrados"><i class="fa fa-credit-card"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    class = "adelantoc btn-float  bg-gradient-secondary btn-sm tooltipsC" title="Adelanto de Cuotas"><i class="fa fa-credit-card"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                class = "atrasosp btn-float  bg-gradient-danger btn-sm tooltipsC" title="atrasos y pendiente"><i class="fa fa-credit-card"></i></button>';
               
                return $button;
                
                
            }) 
            ->rawColumns(['action'])
            ->make(true);



            }

            


            }
        return view('admin.pago_calender.index', compact('datas','clientes','datasp'));
    }

     public function indexPrestamo(Request $request)
    {
        $pagos_registrados = $request->estado_pago;
        $prestamoc_id = $request->prestamoc_id;

        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";


        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->session()->get('usuario_id');
        
        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();
        
        $datasp = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where([['prestamo.usuario_id', '=', $usuario_id], ['prestamo.estado', '!=', 'P']])
            ->where('prestamo.delete_at', '=', null)
            ->pluck('cliente.nombres as nombres','prestamo.idp as idp')->toArray();
            
         if($request->ajax()){
            
            if($pagos_registrados == 0 || $pagos_registrados == null){
               
            $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            //->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '=', $fecha_Actual]])
            //->where([['prestamo.usuario_id', '=', $usuario_id], ['prestamo.monto_pendiente', '>', 0]])
            ->where('prestamo.usuario_id', '=', $usuario_id)
            //->whereIn('detalle_prestamo.estado', ['C'])
            //->where(function ($query) {
             // $fechai= Carbon::now()->toDateString()." 00:00:01";
              //$query->where('detalle_prestamo.updated_at', '<', $fechai)
                //      ->orWhere('detalle_prestamo.updated_at', '=', null);
            //})
            ->where(function ($query) {
            $fechai= Carbon::now()->toDateString()." 00:00:01";
            $query->where('prestamo.updated_at', '<', $fechai)
            ->orWhere('prestamo.updated_at', '=', null);
            })
            ->whereIn('detalle_prestamo.estado', ['A','C'])
            ->where('detalle_prestamo.fecha_cuota', '<=', Carbon::now()->toDateString())
            ->select('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado')
            ->groupBy('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado')
            ->get();
            return  DataTables()->of($datas)
                ->addColumn('action', function($datas){
                $button ='<button type="button" name="prestamo" id="'.$datas->idp.'" idf="'.now()->toDateString().'"
                class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i></button>
                ';
                $button .='&nbsp;<button type="button" name="detalle" id="'.$datas->idp.'"
                class = "detalle btn-float  bg-gradient-info btn-sm tooltipsC"><i class="fas fa-atlas"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                class = "detallepay btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Pago"><i class="fas fa-book-reader"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    class = "adelantoc btn-float  bg-gradient-secondary btn-sm tooltipsC" title="Adelanto de Cuotas"><i class="fa fa-credit-card"></i></button>';
                
                $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                class = "atrasosp btn-float  bg-gradient-danger btn-sm tooltipsC" title="atrasos y pendiente"><i class="fa fa-credit-card"></i></button>';
               
                return $button;
                
                
            }) 
            ->rawColumns(['action'])
            ->make(true);

         } else if($pagos_registrados == 1){

                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where('prestamo.usuario_id', '=', $usuario_id)
                ->whereBetween('detalle_prestamo.updated_at', [$fechaAi, $fechaAf])
                ->whereIn('detalle_prestamo.estado', ['P','A'])
                ->where('prestamo.delete_at', '=', null)
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button = '<button type="button" name="edit" id="'.$datas->idp.'"
                    class = "editpay btn-float  bg-gradient-primary btn-sm tooltipsC"  title="Editar Pago"><i class="far fa-edit"></i></button>';
                    
                    $button .='&nbsp;<button type="button" name="detalle" id="'.$datas->idp.'"
                    class = "detalle btn-float  bg-gradient-info btn-sm tooltipsC"><i class="fas fa-atlas"></i></button>';
                
                    $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    class = "detallepay btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Pago"><i class="fas fa-book-reader"></i></button>';
                    
                    $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    class = "adelantoc btn-float  bg-gradient-secondary btn-sm tooltipsC" title="Adelanto de Cuotas"><i class="fa fa-credit-card"></i></button>';
                    
                    $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    class = "atrasosp btn-float  bg-gradient-danger btn-sm tooltipsC" title="atrasos y pendiente"><i class="fa fa-credit-card"></i></button>';
                    
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }else if($pagos_registrados == 2){

                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '<', $fecha_Actual]])
                ->where(function ($query) {
                $fechai= Carbon::now()->toDateString()." 00:00:01";
                $query->where('detalle_prestamo.updated_at', '<', $fechai)
                      ->orWhere('detalle_prestamo.updated_at', '=', null);
                      
                })
                ->whereIn('detalle_prestamo.estado', ['A','C'])
                ->where('prestamo.delete_at', '=', null)
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button ='<button type="button" name="prestamo" id="'.$datas->idd.'"
                    class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i>
                    <i class="fas fa-money-bill-alt"></i></button>';
                    
                   // $button .= '<div id="edicdes"><button type="button" name="edit" id="'.$datas->idd.'"
                    //class = "editpay btn-float  bg-gradient-primary btn-sm tooltipsC"  title="Editar Pago"><i class="far fa-edit"></i>
                    //</button></div>';
                    
                   // $button .='&nbsp;<button type="button" name="prestamo" id="'.$datas->idp.'"
                    //class = "detallepay btn-float  bg-gradient-success btn-sm tooltipsC" title="Detalle de Pago"><i class="fas fa-atlas"></i>
                    //</i></button>';
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }else if($pagos_registrados == 3 && $prestamoc_id == null ){
               
                
                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '>', $fecha_Actual]])
                ->where('prestamo.delete_at', '=', null)
                ->whereIn('detalle_prestamo.estado', ['C'])
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button ='&nbsp;<button type="button" name="prestamo" id="'.$datas->idd.'"
                    class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>';
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }else if($pagos_registrados == 3 && $prestamoc_id > 0 ){
               
                
                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '>', $fecha_Actual], ['prestamo.idp', '=', $prestamoc_id]])
                ->whereIn('detalle_prestamo.estado', ['C'])
                ->where('prestamo.delete_at', '=', null)
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button ='&nbsp;<button type="button" name="prestamo" id="'.$datas->idd.'"
                    class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>';
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }

            


            }
        return view('admin.pago_calender.index', compact('datas','clientes','datasp'));
    }
    
    public function indexAdelanto(Request $request)
    {
        $prestamoc_id = $request->prestamoc_id;

        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";


        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->session()->get('usuario_id');
        
        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();
        
        $datasp = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where([['prestamo.usuario_id', '=', $usuario_id], ['prestamo.estado', '!=', 'P']])
            ->pluck('cliente.nombres as nombres','prestamo.idp as idp')->toArray();
            
         if($request->ajax()){
            
           
                
                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '>', $fecha_Actual], ['prestamo.idp', '=', $prestamoc_id]])
                //->where([['prestamo.usuario_id', '=', $usuario_id],['prestamo.idp', '=', $prestamoc_id]])
                ->whereIn('detalle_prestamo.estado', ['C'])
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button ='&nbsp;<button type="button" name="prestamo" id="'.$datas->idd.'"
                    class = "pay btn-float  bg-gradient-success btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>';
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }
        return view('admin.pago_calender.index', compact('datas','clientes','datasp'));
    }
    
    
    public function indexAtrasosp(Request $request)
    {
        $prestamoc_id = $request->prestamoc_id;

        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";


        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->session()->get('usuario_id');
        
        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();
        
        $datasp = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where([['prestamo.usuario_id', '=', $usuario_id], ['prestamo.estado', '!=', 'P']])
            ->pluck('cliente.nombres as nombres','prestamo.idp as idp')->toArray();
            
         if($request->ajax()){
            
           
                
                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '<', $fecha_Actual], ['prestamo.idp', '=', $prestamoc_id]])
                //->where([['prestamo.usuario_id', '=', $usuario_id],['prestamo.idp', '=', $prestamoc_id]])
                ->where(function ($query) {
                $fechai= Carbon::now()->toDateString()." 23:59:01";
                $query->where('detalle_prestamo.updated_at', '<=', $fechai)
                         ->orWhere('detalle_prestamo.updated_at', '=', null);
                })
                ->whereIn('detalle_prestamo.estado', ['A','C'])
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button ='&nbsp;<button type="button" name="prestamo" id="'.$datas->idd.'"
                    class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>';
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }
            
        
        return view('admin.pago_calender.index', compact('datas','clientes','datasp'));
    }
    
    
     public function indexRegistrados(Request $request)
    {
        $prestamoc_id = $request->prestamoc_id;

        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";


        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->session()->get('usuario_id');
        
        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();
        
        $datasp = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where([['prestamo.usuario_id', '=', $usuario_id], ['prestamo.estado', '!=', 'P']])
            ->pluck('cliente.nombres as nombres','prestamo.idp as idp')->toArray();
            
         if($request->ajax()){

                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id],['prestamo.idp', '=', $prestamoc_id]])
                ->whereBetween('detalle_prestamo.updated_at', [$fechaAi, $fechaAf])
                ->whereIn('detalle_prestamo.estado', ['P','A'])
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button = '<button type="button" name="edit" id="'.$datas->idd.'"
                    class = "editpay btn-float  bg-gradient-primary btn-sm tooltipsC"  title="Editar Pago"><i class="far fa-edit"></i></button>';
                   
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }
        return view('admin.pago_calender.index', compact('datas','clientes','datasp'));
    }
    
    
    public function indexPagonow(Request $request)
    {
        $prestamoc_id = $request->prestamoc_id;

        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";


        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->session()->get('usuario_id');
        
        $clientes = Cliente::where('usuario_id', '=', $usuario_id )->get();
        
        $datasp = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where([['prestamo.usuario_id', '=', $usuario_id], ['prestamo.estado', '!=', 'P']])
            ->pluck('cliente.nombres as nombres','prestamo.idp as idp')->toArray();
            
         if($request->ajax()){
            
           
                
                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '=', $fecha_Actual], ['prestamo.idp', '=', $prestamoc_id]])
                //->where([['prestamo.usuario_id', '=', $usuario_id],['prestamo.idp', '=', $prestamoc_id]])
                ->whereIn('detalle_prestamo.estado', ['C'])
                ->get();
                return  DataTables()->of($datas)
                    ->addColumn('action', function($datas){
                    $button ='&nbsp;<button type="button" name="prestamo" id="'.$datas->idd.'"
                    class = "pay btn-float  bg-gradient-warning btn-sm tooltipsC" title="Registar Pago"><i class="fa fa-fw fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>';
                    return $button;
    
                }) 
                ->rawColumns(['action'])
                ->make(true);


            }
        return view('admin.pago_calender.index', compact('datas','clientes','datasp'));
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

        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');


        $vc= DetallePrestamo::where([['prestamo_id', '=', $request->prestamo_id],
        ['d_numero_cuota', '=', $request->numero_cuota]])->first();

        $saldo = Prestamo::where('idp', '=', $request->prestamo_id)->first();
        $saldop = $saldo->monto_pendiente;


        $vcd = $vc->valor_cuota; // Valor cuota diaria


//Logica para adelanto de cuota

  if($request->fecha_pago > $fecha_Actual){


    if($request->valor_abono <  $vcd && $saldo->monto_atrasado == 0){
            
        Pago::create($request->all());

        DB::table('detalle_prestamo')
        ->where([['prestamo_id', '=', $request->prestamo_id],
                 ['d_numero_cuota', '=', $request->numero_cuota]])
        ->update([
        'valor_cuota' => ($vcd - $request->valor_abono),
        'valor_cuota_pagada'=>$request->valor_abono,
        'estado'=>'C',
        'updated_at'=> now() 
        ]);

        DB::table('prestamo')
        ->where([
            ['idp', '=', $request->prestamo_id]
            ])
        ->update([
        'monto_pendiente'=>($saldop - $request->valor_abono),
        'updated_at'=> now(),

        ]);
        
        return response()->json(['success' => 'abono']); 
    
    }else if($vcd == $request->valor_abono  && $saldo->monto_atrasado == 0){
        
        Pago::create($request->all());

        DB::table('detalle_prestamo')
        ->where([['prestamo_id', '=', $request->prestamo_id],
                 ['d_numero_cuota', '=', $request->numero_cuota]])
        ->update([
        'valor_cuota_pagada'=>$request->valor_abono,    
        'estado'=>'P',
        'updated_at'=> now() 
        ]);

        
        DB::table('prestamo')
        ->where([
            ['idp', '=', $request->prestamo_id]
            ])
        ->update([
        'monto_pendiente'=>($saldop - $request->valor_abono),
        'updated_at'=> now(),

        ]);
        return response()->json(['success' => 'okadelanto']); 
  
    }else if($request->valor_abono > $vcd){
        
        return response()->json(['success' => 'noadelanto']); 
}else if($request->valor_abono <= $vcd && $saldo->monto_atrasado > 0){
        
    return response()->json(['success' => 'error']); 
}else if($request->valor_abono <= $vcd && $saldo->monto_atrasado < 0){
        
    return response()->json(['success' => 'error']); 
}



  }
  //Logica para pagos normales
  else if($request->fecha_pago <= $fecha_Actual  && $request->estado_cuota == "C" ){
  
     //Pago de saldo total
        if($request->valor_abono == $saldop){
            
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
            
            return response()->json(['success' => 'total']);

            }
             //Pago de cuota 0 o < que cuota diaria
            else if($request->valor_abono == 0 || $request->valor_abono <  $vcd){
            
            Pago::create($request->all());

            DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['d_numero_cuota', '=', $request->numero_cuota]])
            ->update([
            'estado'=>'A',
            'valor_cuota_pagada'=>$request->valor_abono,
            'updated_at'=> now() 
            ]);

            $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
           
            $cuotasat = $saldoa->cuotas_atrasadas;
            $saldoat = $saldoa->monto_atrasado;    
            
            DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_atrasado'=>($saldoat + ($request->valor_cuota - $request->valor_abono)),
            'cuotas_atrasadas'=>($cuotasat + 1),
            'monto_pendiente'=>($saldop - $request->valor_abono),
            'updated_at'=> now()
            ]);
            

        }
     //Pago de cuota diaria + abono a atraso total
        else if($request->valor_abono == ($vcd + $saldo->monto_atrasado) && $saldo->monto_atrasado > 0 ){
            
            $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
           
            $cuotasat = $saldoa->cuotas_atrasadas; //Cuotas atrasadas totales
            $saldoat = $saldoa->monto_atrasado; // Saldo atrasado total
            $abonoat = $request->valor_abono - $vcd; // Abono a saldo atrasado
            //$cuotasatdesc = round($abonoat/$vcd,2); // Cuotas a descontar de las atrasadas

            
            
            if($cuotasat > 0 ){
            
            Pago::create($request->all());

            DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['d_numero_cuota', '=', $request->numero_cuota]])
            ->update([
            'estado'=>'P',
            'valor_cuota_pagada'=>$request->valor_abono,
            'updated_at'=> now() 
            ]);

            DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['estado', '=', 'A']])
            ->update([
            'estado'=>'P',
            'updated_at'=> now() 
            ]);


            
            DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_atrasado'=>($saldoat - $abonoat),
            'cuotas_atrasadas'=> 0,
            'monto_pendiente'=>($saldop - $request->valor_abono),
            'updated_at'=> now()
            ]);
            

                 
            }
            
            return response()->json(['success' => 'noat']); 
        }
     //Pago de cuota diaria normal
        else if($vcd == $request->valor_abono){
        
        Pago::create($request->all());

        DB::table('detalle_prestamo')
        ->where([['prestamo_id', '=', $request->prestamo_id],
                 ['d_numero_cuota', '=', $request->numero_cuota]])
        ->update([
        'estado'=>'P',
        'valor_cuota_pagada'=>$request->valor_abono,
        'updated_at'=> now() 
        ]);

        
        DB::table('prestamo')
        ->where([
            ['idp', '=', $request->prestamo_id]
            ])
        ->update([
        'monto_pendiente'=>($saldop - $request->valor_abono),
        'updated_at'=> now(),

        ]);
        
  }else if($request->valor_abono > $vcd  && $request->valor_abono < $saldop && $saldo->monto_atrasado == 0){

            
            return response()->json(['success' => 'adelantos']);

  }else if($request->valor_abono > $vcd && $request->valor_abono < ($vcd + $saldo->monto_atrasado) ){

            return response()->json(['success' => 'vcda']);

  }else if($request->valor_abono > ($vcd + $saldo->monto_atrasado) && $saldo->monto_atrasado > 0 ){

            return response()->json(['success' => 'adelantosa']);
  }
 
  return response()->json(['success' => 'ok']);

 }//Pago de cuota atrasada normal
        else if($request->fecha_pago <= $fecha_Actual  && $request->estado_cuota == "A" ){
            
        $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
        $saldop = $saldoa->monto_pendiente;
        $cuotasat = $saldoa->cuotas_atrasadas; //Cuotas atrasadas totales
        $saldoat = $saldoa->monto_atrasado; // Saldo atrasado total
        
        $pagoa =  Pago::where([['prestamo_id', '=', $request->prestamo_id], ['numero_cuota', '=', $request->numero_cuota]])->first();
        $pagoqa = $pagoa->valor_abono; //Valor abono de cuota ya pagada
        
        if($request->valor_abono == $request->vatraso){
        
        Pago::create($request->all());

        DB::table('detalle_prestamo')
        ->where([['prestamo_id', '=', $request->prestamo_id],
                 ['d_numero_cuota', '=', $request->numero_cuota]])
        ->update([
        'estado'=>'P',
        'valor_cuota_pagada'=>($pagoqa + $request->valor_abono),
        'updated_at'=> now() 
        ]);

        
        DB::table('prestamo')
        ->where([
            ['idp', '=', $request->prestamo_id]
            ])
        ->update([
        'monto_pendiente'=>($saldop - $request->valor_abono),
        'monto_atrasado' =>($saldoat - $request->valor_abono),
        'cuotas_atrasadas' =>($cuotasat - 1) ,
        'updated_at'=> now(),

        ]);
 
        
 
         return response()->json(['success' => 'okca']);
        }else if($request->valor_abono < $request->vatraso && $request->valor_abono > 0 ){
            
             Pago::create($request->all());
             
             DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['d_numero_cuota', '=', $request->numero_cuota]])
            ->update([
            'estado'=>'A',
            'valor_cuota_pagada'=>($pagoqa + $request->valor_abono),
            'updated_at'=> now() 
            ]);
            
               DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_pendiente'=>($saldop - $request->valor_abono),
            'monto_atrasado' =>($saldoat - $request->valor_abono),
            //'cuotas_atrasadas' =>($cuotasat - 1) ,
            'updated_at'=> now(),
    
            ]);
            
            return response()->json(['success' => 'abonoa']);
            
        }else if($request->valor_abono < $request->vatraso || $request->valor_abono > $request->vatraso){
            
            return response()->json(['success' => 'okcaerror']);
            
        }
        
 }else {
    return response()->json(['success' => 'error']);
       }

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
    public function editar(Request $request, $id)
    {
        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->session()->get('usuario_id');
         
       if(request()->ajax()){
           
        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->join('pago', function ($join) {
            $join->on('detalle_prestamo.d_numero_cuota', '=', 'pago.numero_cuota')
                 ->on('detalle_prestamo.prestamo_id', '=', 'pago.prestamo_id')
                 ->on('prestamo.idp', '=', 'pago.prestamo_id');
        })
        ->where([['prestamo.usuario_id', '=', $usuario_id],['detalle_prestamo.idd', '=', $id]])->first();
           
        if(!empty($data)){
            
         $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->join('pago', function ($join) {
            $join->on('detalle_prestamo.d_numero_cuota', '=', 'pago.numero_cuota')
                 ->on('detalle_prestamo.prestamo_id', '=', 'pago.prestamo_id')
                 ->on('prestamo.idp', '=', 'pago.prestamo_id');
        })
        ->where([['prestamo.usuario_id', '=', $usuario_id],['detalle_prestamo.idd', '=', $id]])->get();
        
        
            return response()->json(['result'=>$data]);            
            
       
        }else{
            
        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->where([['prestamo.usuario_id', '=', $usuario_id],['detalle_prestamo.idd', '=', $id]])->get();
            return response()->json(['result'=>$data]);
            
            
           }
        }
        return view('admin.pago_calender.index');

    }
    
public function editarp(Request $request, $id)
    {
        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->session()->get('usuario_id');
         
       if(request()->ajax()){
           
        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->join('pago', function ($join) {
            $join->on('detalle_prestamo.d_numero_cuota', '=', 'pago.numero_cuota')
                 ->on('detalle_prestamo.prestamo_id', '=', 'pago.prestamo_id')
                 ->on('prestamo.idp', '=', 'pago.prestamo_id');
        })
        ->where([['prestamo.usuario_id', '=', $usuario_id],['detalle_prestamo.prestamo_id', '=', $id],['detalle_prestamo.fecha_cuota', '=', $request->idf]])->first();
           
        if(!empty($data)){
            
         $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->join('pago', function ($join) {
            $join->on('detalle_prestamo.d_numero_cuota', '=', 'pago.numero_cuota')
                 ->on('detalle_prestamo.prestamo_id', '=', 'pago.prestamo_id')
                 ->on('prestamo.idp', '=', 'pago.prestamo_id');
        })
        ->where([['prestamo.usuario_id', '=', $usuario_id],['detalle_prestamo.prestamo_id', '=', $id],['detalle_prestamo.fecha_cuota', '=', $request->idf]])->get();
        
        
            return response()->json(['result'=>$data]);            
            
       
        }else{
            
        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->where([['prestamo.usuario_id', '=', $usuario_id],['detalle_prestamo.prestamo_id', '=', $id],['detalle_prestamo.fecha_cuota', '=', $request->idf]])->get();
            return response()->json(['result'=>$data]);
            
            
           }
        }
        return view('admin.pago_calender.index');

    }    
    
    public function editpay(Request $request, $id)
    {
        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->session()->get('usuario_id');
         
       if(request()->ajax()){
        $data = DB::table('prestamo')
        ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
        ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
        ->join('pago', function ($join) {
            $join->on('detalle_prestamo.d_numero_cuota', '=', 'pago.numero_cuota')
                 ->on('detalle_prestamo.prestamo_id', '=', 'pago.prestamo_id')
                 ->on('prestamo.idp', '=', 'pago.prestamo_id');
        })
        ->where([['prestamo.usuario_id', '=', $usuario_id],['detalle_prestamo.idd', '=', $id]])->latest('pago.updated_at')->first();
        
        
            return response()->json(['result'=>$data]);

        }
        return view('admin.pago_calender.index');

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
        
       
        $vc= DetallePrestamo::where([['prestamo_id', '=', $request->prestamo_id],
        ['d_numero_cuota', '=', $request->numero_cuota]])->first();

        $saldo = Prestamo::where('idp', '=', $request->prestamo_id)->first();
        $saldopv = $saldo->monto_pendiente;

        $vcd = $vc->valor_cuota;

        // Limpiar las cuotas pagadas actuales para la actualización.


        $pago =  Pago::where([['prestamo_id', '=', $request->prestamo_id], ['numero_cuota', '=', $request->numero_cuota]])->first();
        $pagoq = $pago->valor_abono; //Valor abono de cuota ya pagada

        $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
           
        $cuotasat = $saldoa->cuotas_atrasadas; //Cuotas atrasadas totales
        $saldoat = $saldoa->monto_atrasado;    // Saldo atrasado total
        $saldoad = $saldoa->longitud;    // Saldo adelantado total
        
        if($pagoq == $vcd){
         
            DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_pendiente'=>($saldopv + $pagoq),
            'updated_at'=> now()
             ]); 

        }else if($pagoq == 0 || $pagoq < $vcd){

        DB::table('prestamo')
        ->where([
            ['idp', '=', $request->prestamo_id]
            ])
        ->update([
        'monto_atrasado'=>($saldoat - ($request->valor_cuota - $pagoq)),
        'cuotas_atrasadas'=>($cuotasat - 1),
        'monto_pendiente'=>($saldopv + $pagoq),
        'updated_at'=> now()
        ]);

        }else if($pagoq > $vcd && $pagoq < $saldopv){

            $abonoat = $pagoq - $vcd;
            $cuotasatdesc = round($abonoat/$vcd,2);

            if($saldoat > 0){
            DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_atrasado'=>($saldoat + $abonoat),
            'cuotas_atrasadas'=>($cuotasat + $cuotasatdesc),
            'monto_pendiente'=>($saldopv + $pagoq),
            'updated_at'=> now()
            ]);
            }else{
                DB::table('prestamo')
                ->where([
                    ['idp', '=', $request->prestamo_id]
                    ])
                ->update([
                'monto_atrasado'=>0,
                'longitud'=>($saldoad - ($pagoq - $vcd)),
                'cuotas_atrasadas'=>0,
                'monto_pendiente'=>($saldopv + $pagoq),
                'updated_at'=> now()
                ]);


            }
        }

        $saldopa = Prestamo::where('idp', '=', $request->prestamo_id)->first();
        $saldop = $saldopa->monto_pendiente;

        
        if($request->valor_abono == 0 || $request->valor_abono <  $vcd){
            
            $pago =  Pago::where([['prestamo_id', '=', $request->prestamo_id], ['numero_cuota', '=', $request->numero_cuota]])->first();
            
            DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['d_numero_cuota', '=', $request->numero_cuota]])
            ->update([
            'estado'=>'A',
            'valor_cuota_pagada'=>$request->valor_abono,
            'updated_at'=> now() 
            ]);
                       
            $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
           
            $cuotasat = $saldoa->cuotas_atrasadas; //Cuotas atrasadas totales
            $saldoat = $saldoa->monto_atrasado;    // Saldo atrasado total
            
            
             //Actualiza de nuevo
            $pago->update($request->all());

            $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
           
            $cuotasat = $saldoa->cuotas_atrasadas;//Cuotas atrasadas totales
            $saldoat = $saldoa->monto_atrasado; // Saldo atrasado total
            
            DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_atrasado'=>($saldoat + ($request->valor_cuota - $request->valor_abono)),
            'cuotas_atrasadas'=>($cuotasat + 1),
            'monto_pendiente'=>($saldop - $request->valor_abono),
            'updated_at'=> now()
            ]);
            
           

 }else if($request->valor_abono > $vcd && $request->valor_abono < $saldop){
            
            $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
           
            $cuotasat = $saldoa->cuotas_atrasadas; //Cuotas atrasadas totales
            $saldoat = $saldoa->monto_atrasado; // Saldo atrasado total
            $abonoat = $request->valor_abono - $vcd; // Abono a saldo atrasado
            $cuotasatdesc = round($abonoat/$vcd,2); // Cuotas a descontar de las atrasadas
           
 if($cuotasatdesc <= $cuotasat && $abonoat <= $saldoat ){
            
            DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['d_numero_cuota', '=', $request->numero_cuota]])
            ->update([
            'estado'=>'P',
            'valor_cuota_pagada'=>$request->valor_abono,
            'updated_at'=> now() 
            ]);

            //Actualiza de nuevo
            $pago->update($request->all());
            
            DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_atrasado'=>($saldoat - $abonoat),
            'cuotas_atrasadas'=>($cuotasat - $cuotasatdesc),
            'monto_pendiente'=>($saldop - $request->valor_abono),
            'updated_at'=> now()
            ]);
 }else if($cuotasat == 0 ){

                DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['d_numero_cuota', '=', $request->numero_cuota]])
            ->update([
            'estado'=>'P',
            'valor_cuota_pagada'=>$request->valor_abono,
            'updated_at'=> now() 
            ]);

            //Actualiza de nuevo
            $pago->update($request->all());
            
            DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_pendiente'=>($saldop - $request->valor_abono),
            'longitud'=>($request->valor_abono - $vcd),
            'updated_at'=> now()
            ]);

 }else{

                return response()->json(['success' => 'noa']);  
      }
            

 }else if($vcd == $request->valor_abono){
        
        $pago =  Pago::where([['prestamo_id', '=', $request->prestamo_id], ['numero_cuota', '=', $request->numero_cuota]])->first();
        $pagoq = $pago->valor_abono; //Valor abono de cuota ya pagada

        DB::table('detalle_prestamo')
        ->where([['prestamo_id', '=', $request->prestamo_id],
                 ['d_numero_cuota', '=', $request->numero_cuota]])
        ->update([
        'estado'=>'P',
        'valor_cuota_pagada'=>$request->valor_abono,
        'updated_at'=> now() 
        ]);
        
         //Actualiza de nuevo
        $pago->update($request->all());


        DB::table('prestamo')
        ->where([
            ['idp', '=', $request->prestamo_id]
            ])
        ->update([
        'monto_pendiente'=>($saldop - $request->valor_abono),
        'updated_at'=> now(),

        ]);
        
        }

        

        return response()->json(['success' => 'oka']);
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

    public function detalle($id)
    {
        
        if(request()->ajax()){

              
            $dataPagos = DB::table('pago')
             ->where('prestamo_id', '=', $id)->get();
    
        
            return response()->json(['result1'=>$dataPagos ]);

        }
        



    }
    
    
     public function PagosPayApp(Request $request)
    {   
       
         $useractivo = Usuario::where([
            ['usuario', '=', $request->usuario],
            ['activo', '=', 1]
        
            ])->count();
            
            
            if($useractivo >= 1){  
                
       
        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');


        $vc= DetallePrestamo::where([['prestamo_id', '=', $request->prestamo_id],
        ['d_numero_cuota', '=', $request->numero_cuota]])->first();

        $saldo = Prestamo::where('idp', '=', $request->prestamo_id)->first();
        $saldop = $saldo->monto_pendiente;


        $vcd = $vc->valor_cuota; // Valor cuota diaria


//Logica para adelanto de cuota

  if($request->fecha_pago > $fecha_Actual){


    if($request->valor_abono <  $vcd && $saldo->monto_atrasado == 0){
            
        Pago::create($request->all());

        DB::table('detalle_prestamo')
        ->where([['prestamo_id', '=', $request->prestamo_id],
                 ['d_numero_cuota', '=', $request->numero_cuota]])
        ->update([
        'valor_cuota' => ($vcd - $request->valor_abono),
        'valor_cuota_pagada'=>$request->valor_abono,
        'estado'=>'C',
        'updated_at'=> now() 
        ]);

        DB::table('prestamo')
        ->where([
            ['idp', '=', $request->prestamo_id]
            ])
        ->update([
        'monto_pendiente'=>($saldop - $request->valor_abono),
        'updated_at'=> now(),

        ]);
        
        return response()->json(['success' => 'Abono exitoso de cuota'], 200); 
    
    }else if($vcd == $request->valor_abono  && $saldo->monto_atrasado == 0){
        
        Pago::create($request->all());

        DB::table('detalle_prestamo')
        ->where([['prestamo_id', '=', $request->prestamo_id],
                 ['d_numero_cuota', '=', $request->numero_cuota]])
        ->update([
        'valor_cuota_pagada'=>$request->valor_abono,    
        'estado'=>'P',
        'updated_at'=> now() 
        ]);

        
        DB::table('prestamo')
        ->where([
            ['idp', '=', $request->prestamo_id]
            ])
        ->update([
        'monto_pendiente'=>($saldop - $request->valor_abono),
        'updated_at'=> now(),

        ]);
        return response()->json(['success' => 'Pago exitoso de cuota'], 200); 
  
    }else if($request->valor_abono > $vcd){
        
        return response()->json(['success' => 'noadelanto'], 200); 
}else if($request->valor_abono <= $vcd && $saldo->monto_atrasado > 0){
        
    return response()->json(['success' => 'error'], 200); 
}else if($request->valor_abono <= $vcd && $saldo->monto_atrasado < 0){
        
    return response()->json(['success' => 'error'], 200); 
}



  }
  //Logica para pagos normales
  else if($request->fecha_pago <= $fecha_Actual  && $request->estado_cuota == "C" ){
  
     //Pago de saldo total
        if($request->valor_abono == $saldop){
            
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
            
            return response()->json(['success' => 'Pago total realizado'], 200);

            }
             //Pago de cuota 0 o < que cuota diaria
            else if($request->valor_abono == 0 || $request->valor_abono <  $vcd){
            
            Pago::create($request->all());

            DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['d_numero_cuota', '=', $request->numero_cuota]])
            ->update([
            'estado'=>'A',
            'valor_cuota_pagada'=>$request->valor_abono,
            'updated_at'=> now() 
            ]);

            $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
           
            $cuotasat = $saldoa->cuotas_atrasadas;
            $saldoat = $saldoa->monto_atrasado;    
            
            DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_atrasado'=>($saldoat + ($request->valor_cuota - $request->valor_abono)),
            'cuotas_atrasadas'=>($cuotasat + 1),
            'monto_pendiente'=>($saldop - $request->valor_abono),
            'updated_at'=> now()
            ]);
            

        }
     //Pago de cuota diaria + abono a atraso total
        else if($request->valor_abono == ($vcd + $saldo->monto_atrasado) && $saldo->monto_atrasado > 0 ){
            
            $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
           
            $cuotasat = $saldoa->cuotas_atrasadas; //Cuotas atrasadas totales
            $saldoat = $saldoa->monto_atrasado; // Saldo atrasado total
            $abonoat = $request->valor_abono - $vcd; // Abono a saldo atrasado
            //$cuotasatdesc = round($abonoat/$vcd,2); // Cuotas a descontar de las atrasadas

            
            
            if($cuotasat > 0 ){
            
            Pago::create($request->all());

            DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['d_numero_cuota', '=', $request->numero_cuota]])
            ->update([
            'estado'=>'P',
            'valor_cuota_pagada'=>$request->valor_abono,
            'updated_at'=> now() 
            ]);

            DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['estado', '=', 'A']])
            ->update([
            'estado'=>'P',
            'updated_at'=> now() 
            ]);


            
            DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_atrasado'=>($saldoat - $abonoat),
            'cuotas_atrasadas'=> 0,
            'monto_pendiente'=>($saldop - $request->valor_abono),
            'updated_at'=> now()
            ]);
            

                 
            }
            
            return response()->json(['success' => 'noat'], 200); 
        }
     //Pago de cuota diaria normal
        else if($vcd == $request->valor_abono){
        
        Pago::create($request->all());

        DB::table('detalle_prestamo')
        ->where([['prestamo_id', '=', $request->prestamo_id],
                 ['d_numero_cuota', '=', $request->numero_cuota]])
        ->update([
        'estado'=>'P',
        'valor_cuota_pagada'=>$request->valor_abono,
        'updated_at'=> now() 
        ]);

        
        DB::table('prestamo')
        ->where([
            ['idp', '=', $request->prestamo_id]
            ])
        ->update([
        'monto_pendiente'=>($saldop - $request->valor_abono),
        'updated_at'=> now(),

        ]);
        
  }else if($request->valor_abono > $vcd  && $request->valor_abono < $saldop && $saldo->monto_atrasado == 0){

            
            return response()->json(['success' => 'adelantos']);

  }else if($request->valor_abono > $vcd && $request->valor_abono < ($vcd + $saldo->monto_atrasado) ){

            return response()->json(['success' => 'vcda']);

  }else if($request->valor_abono > ($vcd + $saldo->monto_atrasado) && $saldo->monto_atrasado > 0 ){

            return response()->json(['success' => 'adelantosa']);
  }
 
  return response()->json(['success' => 'Pago realizado correctamente'], 200);

 }//Pago de cuota atrasada normal
        else if($request->fecha_pago <= $fecha_Actual  && $request->estado_cuota == "A" ){
            
        $saldoa = Prestamo::where('idp', '=', $request->prestamo_id)->first();    
        $saldop = $saldoa->monto_pendiente;
        $cuotasat = $saldoa->cuotas_atrasadas; //Cuotas atrasadas totales
        $saldoat = $saldoa->monto_atrasado; // Saldo atrasado total
        
        $pagoa =  Pago::where([['prestamo_id', '=', $request->prestamo_id], ['numero_cuota', '=', $request->numero_cuota]])->first();
        $pagoqa = $pagoa->valor_abono; //Valor abono de cuota ya pagada
        
        if($request->valor_abono == $request->vatraso){
        
        Pago::create($request->all());

        DB::table('detalle_prestamo')
        ->where([['prestamo_id', '=', $request->prestamo_id],
                 ['d_numero_cuota', '=', $request->numero_cuota]])
        ->update([
        'estado'=>'P',
        'valor_cuota_pagada'=>($pagoqa + $request->valor_abono),
        'updated_at'=> now() 
        ]);

        
        DB::table('prestamo')
        ->where([
            ['idp', '=', $request->prestamo_id]
            ])
        ->update([
        'monto_pendiente'=>($saldop - $request->valor_abono),
        'monto_atrasado' =>($saldoat - $request->valor_abono),
        'cuotas_atrasadas' =>($cuotasat - 1) ,
        'updated_at'=> now(),

        ]);
 
        
 
         return response()->json(['success' => 'okca'], 200);
        }else if($request->valor_abono < $request->vatraso && $request->valor_abono > 0 ){
            
             Pago::create($request->all());
             
             DB::table('detalle_prestamo')
            ->where([['prestamo_id', '=', $request->prestamo_id],
                     ['d_numero_cuota', '=', $request->numero_cuota]])
            ->update([
            'estado'=>'A',
            'valor_cuota_pagada'=>($pagoqa + $request->valor_abono),
            'updated_at'=> now() 
            ]);
            
               DB::table('prestamo')
            ->where([
                ['idp', '=', $request->prestamo_id]
                ])
            ->update([
            'monto_pendiente'=>($saldop - $request->valor_abono),
            'monto_atrasado' =>($saldoat - $request->valor_abono),
            //'cuotas_atrasadas' =>($cuotasat - 1) ,
            'updated_at'=> now(),
    
            ]);
            
            return response()->json(['success' => 'abonoa'], 200);
            
        }else if($request->valor_abono < $request->vatraso || $request->valor_abono > $request->vatraso){
            
            return response()->json(['success' => 'okcaerror'], 200);
            
        }
        
 }else {
    return response()->json(['success' => 'error'], 200);
       }
       
         }else{ return response()->json(['error'=> 'Unauthorised'], 400);
            }

}
    
    
    
    
    
     public function PagosPayAppXpApp(Request $request)
    {
        
        
         $useractivo = Usuario::where([
            ['usuario', '=', $request->usuario],
            ['activo', '=', 1]
        
            ])->count();
            
            
            if($useractivo >= 1){  
              
       
        $pagos_registrados = $request->estado_pago;
        
        $fechaAi= Carbon::now()->toDateString()." 00:00:01";
        $fechaAf= Carbon::now()->toDateString()." 23:59:59";


        $fecha_Actual = Carbon::now();
        $fecha_Actual = $fecha_Actual->Format('Y-m-d');
        
        $usuario_id = $request->usuario_id;
        
       
         
            
            // Pagos por cobrar del día
            if($pagos_registrados == 6 || $pagos_registrados == null){
               
            $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where([['prestamo.usuario_id', '=', $usuario_id], ['detalle_prestamo.fecha_cuota', '=', $fecha_Actual]])
           
            ->whereIn('detalle_prestamo.estado', ['C'])
            ->where('prestamo.delete_at', '=', null)
            ->where(function ($query) {
            $fechai= Carbon::now()->toDateString()." 00:00:01";
            $query->where('detalle_prestamo.updated_at', '<', $fechai)
             ->orWhere('detalle_prestamo.updated_at', '=', null);
            })
            ->get();
           
           return response()->json(['datas'=>  $datas], 200);
           // Pagos registrados del día
         } else if($pagos_registrados == 1 ){

                $datas = DB::table('prestamo')
                ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
                ->where('prestamo.usuario_id', '=', $usuario_id)
                ->whereBetween('detalle_prestamo.updated_at', [$fechaAi, $fechaAf])
                ->whereIn('detalle_prestamo.estado', ['P','A'])
                ->where('prestamo.delete_at', '=', null)
                ->get();
              
return response()->json(['datas'=>  $datas], 200);
// Pagos por cobrar por prestamo
            }else if($pagos_registrados == 4  ){
               
                
            $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where('prestamo.usuario_id', '=', $usuario_id)
            ->where('prestamo.monto_pendiente', '>', 0)
            ->where(function ($query) {
            $fechai= Carbon::now()->toDateString()." 00:00:01";
            $query->where('prestamo.updated_at', '<', $fechai)
            ->orWhere('prestamo.updated_at', '=', null);})
            ->whereIn('detalle_prestamo.estado', ['A','C'])
            ->where('detalle_prestamo.fecha_cuota', '<=', Carbon::now()->toDateString())
            ->where('prestamo.delete_at', '=', null)
            ->select('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas')
            ->groupBy('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas')
            ->get();
           

// Pagos registrados del día por prestamo
return response()->json(['datas'=>  $datas], 200);

            }else if($pagos_registrados == 5){
               
                
                 $datas = DB::table('prestamo')
            ->Join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->Join('detalle_prestamo', 'prestamo.idp', '=', 'detalle_prestamo.prestamo_id')
            ->where('prestamo.usuario_id', '=', $usuario_id)
            ->whereBetween('prestamo.updated_at', [$fechaAi, $fechaAf])
            ->whereIn('detalle_prestamo.estado', ['A','P'])
            ->where('prestamo.delete_at', '=', null)
            ->select('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado')
            ->groupBy('prestamo.idp','prestamo.estado', 'cliente.nombres', 'cliente.apellidos', 'cliente.direccion','cliente.consecutivo', 'prestamo.monto_atrasado')
            ->get();
       return response()->json(['datas'=>  $datas], 200);

            }

            


            
         
           
            }else{
                
                return response()->json(['error'=> 'Unauthorised'], 400);
            }
    }
    
    
    
}
