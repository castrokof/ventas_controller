<?php

// app/Http/Controllers/Admin/V2/PagoController.php

namespace App\Http\Controllers\Admin\V2;

use App\Http\Controllers\Controller;
use App\Models\Admin\Cliente;
use App\Models\Admin\DetallePrestamo;
use App\Models\Admin\Pago;
use App\Models\Admin\Prestamo;
use App\Models\Seguridad\Usuario;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * V2 PagoController — standalone, sin extends.
 *
 * Implementa toda la lógica de PagoCalenderController usando los mismos
 * modelos, pero:
 *  - Renderiza vistas admin.v2.pago_card.*
 *  - Inicializa $datas antes del bloque ajax (fix de variable indefinida)
 *  - Usa Carbon::today() en lugar de repetir Carbon::now()->Format('Y-m-d')
 *  - Deduplica la cláusula where de "no actualizado hoy" en un closure
 */
class PagoController extends Controller
{
    // ─── Helper: "registros no actualizados hoy" ───────────────────────────
    private function whereNoActualizadoHoy(string $campo = 'detalle_prestamo.updated_at'): \Closure
    {
        return function ($query) use ($campo) {
            $inicio = Carbon::today()->toDateString() . ' 00:00:01';
            $query->where($campo, '<', $inicio)
                  ->orWhereNull($campo);
        };
    }

    // ─── Helper: fecha de hoy ──────────────────────────────────────────────
    private function hoy(): string
    {
        return Carbon::today()->toDateString();
    }

    // ─── Helper: rango completo del día ───────────────────────────────────
    private function rangoHoy(): array
    {
        $base = Carbon::today()->toDateString();
        return [$base . ' 00:00:01', $base . ' 23:59:59'];
    }

    // ──────────────────────────────────────────────────────────────────────
    // VISTAS PRINCIPALES
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Página principal pago_card v2.
     * GET /admin/v2/pago-card
     * (equivale a indexcp → ruta pagoccp)
     */
    public function indexcp(Request $request)
    {
        $uid = $request->session()->get('usuario_id');

        $clientes   = Cliente::where('usuario_id', $uid)->get();
        $usuarioscp = Usuario::where('id', $uid)->pluck('usuario', 'id')->toArray();
        $datas      = collect(); // fix: variable definida antes del bloque ajax

        if ($request->ajax()) {
            $datas = DB::table('prestamo')
                ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                ->where('prestamo.usuario_id',      $uid)
                ->where('prestamo.monto_pendiente', '>', 0)
                ->whereNull('prestamo.delete_at')
                ->select(
                    'prestamo.idp', 'prestamo.estado',
                    'cliente.nombres', 'cliente.apellidos', 'cliente.direccion',
                    'cliente.consecutivo', 'cliente.telefono', 'cliente.celular',
                    'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas',
                    'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas'
                )
                ->groupBy(
                    'prestamo.idp', 'prestamo.estado',
                    'cliente.nombres', 'cliente.apellidos', 'cliente.direccion',
                    'cliente.consecutivo', 'cliente.telefono', 'cliente.celular',
                    'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas',
                    'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas'
                )
                ->get();

            return DataTables()->of($datas)
                ->addColumn('action', fn($d) => $this->botonesCardPrestamo($d, 'payp'))
                ->addColumn('datos',  fn($d) => $this->cardClientePrestamo($d, 'bg-olive'))
                ->rawColumns(['action', 'datos'])
                ->make(true);
        }

        return view('admin.v2.pago_card.index', compact('datas', 'clientes', 'usuarioscp'));
    }

    /**
     * Tab Pagos (filtros 0-5 por estado_pago).
     * GET /admin/v2/pago-card/tab
     * (equivale a indexc → ruta pagocc)
     */
    public function indexc(Request $request)
    {
        $uid      = $request->session()->get('usuario_id');
        $filtro   = $request->estado_pago;
        $hoy      = $this->hoy();
        [$ai, $af]= $this->rangoHoy();

        $clientes   = Cliente::where('usuario_id', $uid)->get();
        $usuarios   = Usuario::where('id', $uid)->pluck('usuario', 'id')->toArray();
        $usuarioscp = $usuarios;
        $datasp     = $this->listaPrestamosPorCliente($uid);
        $datas      = collect();

        if ($request->ajax()) {
            if ($filtro == 0 || $filtro === null) {
                // Cuotas por cobrar del día
                $datas = DB::table('prestamo')
                    ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                    ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                    ->where('prestamo.usuario_id', $uid)
                    ->where('detalle_prestamo.fecha_cuota', $hoy)
                    ->whereNull('prestamo.delete_at')
                    ->whereIn('detalle_prestamo.estado', ['C'])
                    ->where($this->whereNoActualizadoHoy())
                    ->get();

                return DataTables()->of($datas)
                    ->addColumn('action', fn($d) => $this->botonesTab($d, 'pay'))
                    ->addColumn('datos',  fn($d) => $this->cardClientePrestamo($d, ''))
                    ->rawColumns(['action', 'datos'])
                    ->make(true);

            } elseif ($filtro == 1) {
                // Pagos registrados del día
                $datas = DB::table('prestamo')
                    ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                    ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                    ->where('prestamo.usuario_id', $uid)
                    ->whereBetween('detalle_prestamo.updated_at', [$ai, $af])
                    ->whereIn('detalle_prestamo.estado', ['P', 'A'])
                    ->get();

                return DataTables()->of($datas)
                    ->addColumn('action', fn($d) => $this->botonesTab($d, 'editpay'))
                    ->addColumn('datos',  fn($d) => $this->cardClientePrestamo($d, 'bg-lightblue'))
                    ->rawColumns(['action', 'datos'])
                    ->make(true);

            } elseif ($filtro == 2) {
                // Cuotas atrasadas
                $datas = DB::table('prestamo')
                    ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                    ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                    ->where('prestamo.usuario_id', $uid)
                    ->where('detalle_prestamo.fecha_cuota', '<', $hoy)
                    ->where($this->whereNoActualizadoHoy())
                    ->whereIn('detalle_prestamo.estado', ['A', 'C'])
                    ->get();

                return DataTables()->of($datas)
                    ->addColumn('action', fn($d) =>
                        '<button type="button" class="pay btn-float bg-gradient-warning btn-sm" '.
                        'data-id="'.$d->idd.'" id="'.$d->idd.'" title="Registrar Pago">'.
                        '<i class="fa fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>'
                    )
                    ->rawColumns(['action'])
                    ->make(true);

            } elseif ($filtro == 3) {
                // Cuotas futuras
                $datas = DB::table('prestamo')
                    ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                    ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                    ->where('prestamo.usuario_id', $uid)
                    ->where('detalle_prestamo.fecha_cuota', '>', $hoy)
                    ->whereIn('detalle_prestamo.estado', ['C'])
                    ->whereNull('prestamo.delete_at')
                    ->get();

                return DataTables()->of($datas)
                    ->addColumn('action', fn($d) =>
                        '<button type="button" class="pay btn-float bg-gradient-warning btn-sm" '.
                        'data-id="'.$d->idd.'" id="'.$d->idd.'" title="Registrar Pago">'.
                        '<i class="fa fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>'
                    )
                    ->rawColumns(['action'])
                    ->make(true);

            } elseif ($filtro == 4) {
                // Por cobrar por préstamo
                $datas = DB::table('prestamo')
                    ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                    ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                    ->where('prestamo.usuario_id', $uid)
                    ->where('prestamo.monto_pendiente', '>', 0)
                    ->whereNull('prestamo.delete_at')
                    ->whereIn('detalle_prestamo.estado', ['A', 'C'])
                    ->where('detalle_prestamo.fecha_cuota', '<=', $hoy)
                    ->where($this->whereNoActualizadoHoy('prestamo.updated_at'))
                    ->select(
                        'prestamo.idp', 'prestamo.estado',
                        'cliente.nombres', 'cliente.apellidos', 'cliente.direccion',
                        'cliente.consecutivo', 'cliente.telefono', 'cliente.celular',
                        'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas',
                        'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas'
                    )
                    ->groupBy(
                        'prestamo.idp', 'prestamo.estado',
                        'cliente.nombres', 'cliente.apellidos', 'cliente.direccion',
                        'cliente.consecutivo', 'cliente.telefono', 'cliente.celular',
                        'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas',
                        'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas'
                    )
                    ->get();

                return DataTables()->of($datas)
                    ->addColumn('action', fn($d) => $this->botonesCardPrestamo($d, 'payp'))
                    ->addColumn('datos',  fn($d) => $this->cardClientePrestamo($d, 'bg-info'))
                    ->rawColumns(['action', 'datos'])
                    ->make(true);

            } elseif ($filtro == 5) {
                // Registrados por préstamo
                $datas = DB::table('prestamo')
                    ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                    ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                    ->where('prestamo.usuario_id', $uid)
                    ->whereBetween('prestamo.updated_at', [$ai, $af])
                    ->whereIn('detalle_prestamo.estado', ['A', 'P'])
                    ->select(
                        'prestamo.idp', 'prestamo.estado',
                        'cliente.nombres', 'cliente.apellidos', 'cliente.direccion',
                        'cliente.consecutivo', 'cliente.telefono', 'cliente.celular',
                        'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas',
                        'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas'
                    )
                    ->groupBy(
                        'prestamo.idp', 'prestamo.estado',
                        'cliente.nombres', 'cliente.apellidos', 'cliente.direccion',
                        'cliente.consecutivo', 'cliente.telefono', 'cliente.celular',
                        'prestamo.monto_atrasado', 'prestamo.cuotas_atrasadas',
                        'prestamo.monto_pendiente', 'prestamo.valor_cuota', 'prestamo.cuotas'
                    )
                    ->get();

                return DataTables()->of($datas)
                    ->addColumn('action', fn($d) => $this->botonesCardPrestamo($d, 'pagosr'))
                    ->addColumn('datos',  fn($d) => $this->cardClientePrestamo($d, 'bg-warning'))
                    ->rawColumns(['action', 'datos'])
                    ->make(true);
            }
        }

        return view('admin.v2.pago_card.index', compact('datas', 'clientes', 'datasp', 'usuarios', 'usuarioscp'));
    }

    /**
     * Cuotas de adelanto de un préstamo.
     * GET /admin/v2/pago-card/adelanto
     * (equivale a indexAdelanto → ruta pagoa)
     */
    public function indexAdelanto(Request $request)
    {
        $uid          = $request->session()->get('usuario_id');
        $prestamoc_id = $request->prestamoc_id;
        $hoy          = $this->hoy();
        $datas        = collect();
        $clientes     = Cliente::where('usuario_id', $uid)->get();
        $datasp       = $this->listaPrestamosPorCliente($uid);

        if ($request->ajax()) {
            $datas = DB::table('prestamo')
                ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                ->where('prestamo.usuario_id', $uid)
                ->where('detalle_prestamo.fecha_cuota', '>', $hoy)
                ->where('prestamo.idp', $prestamoc_id)
                ->whereIn('detalle_prestamo.estado', ['C'])
                ->get();

            return DataTables()->of($datas)
                ->addColumn('action', fn($d) =>
                    '<button type="button" class="pay btn-float bg-gradient-success btn-sm" '.
                    'data-id="'.$d->idd.'" id="'.$d->idd.'" title="Registrar Pago">'.
                    '<i class="fa fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>'
                )
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.v2.pago_card.index', compact('datas', 'clientes', 'datasp'));
    }

    /**
     * Cuotas atrasadas de un préstamo.
     * GET /admin/v2/pago-card/atrasos
     * (equivale a indexAtrasosp → ruta atrasosp)
     */
    public function indexAtrasosp(Request $request)
    {
        $uid          = $request->session()->get('usuario_id');
        $prestamoc_id = $request->prestamoc_id;
        $hoy          = $this->hoy();
        $datas        = collect();
        $clientes     = Cliente::where('usuario_id', $uid)->get();
        $datasp       = $this->listaPrestamosPorCliente($uid);

        if ($request->ajax()) {
            $datas = DB::table('prestamo')
                ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                ->where('prestamo.usuario_id', $uid)
                ->where('detalle_prestamo.fecha_cuota', '<', $hoy)
                ->where('prestamo.idp', $prestamoc_id)
                ->where(function ($q) {
                    $q->where('detalle_prestamo.updated_at', '<=', Carbon::today()->toDateString() . ' 23:59:01')
                      ->orWhereNull('detalle_prestamo.updated_at');
                })
                ->whereIn('detalle_prestamo.estado', ['A', 'C'])
                ->get();

            return DataTables()->of($datas)
                ->addColumn('action', fn($d) =>
                    '<button type="button" class="pay btn-float bg-gradient-warning btn-sm" '.
                    'data-id="'.$d->idd.'" id="'.$d->idd.'" title="Registrar Pago">'.
                    '<i class="fa fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>'
                )
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.v2.pago_card.index', compact('datas', 'clientes', 'datasp'));
    }

    /**
     * Pagos registrados del día para un préstamo.
     * GET /admin/v2/pago-card/registrados
     * (equivale a indexRegistrados → ruta pagosrs)
     */
    public function indexRegistrados(Request $request)
    {
        $uid          = $request->session()->get('usuario_id');
        $prestamoc_id = $request->prestamoc_id;
        [$ai, $af]    = $this->rangoHoy();
        $datas        = collect();
        $clientes     = Cliente::where('usuario_id', $uid)->get();
        $datasp       = $this->listaPrestamosPorCliente($uid);

        if ($request->ajax()) {
            $datas = DB::table('prestamo')
                ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                ->where('prestamo.usuario_id', $uid)
                ->where('prestamo.idp', $prestamoc_id)
                ->whereBetween('detalle_prestamo.updated_at', [$ai, $af])
                ->whereIn('detalle_prestamo.estado', ['P', 'A'])
                ->get();

            return DataTables()->of($datas)
                ->addColumn('action', fn($d) =>
                    '<button type="button" class="editpay btn-float bg-gradient-primary btn-sm" '.
                    'data-id="'.$d->idd.'" id="'.$d->idd.'" title="Editar Pago">'.
                    '<i class="far fa-edit"></i></button>'
                )
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.v2.pago_card.index', compact('datas', 'clientes', 'datasp'));
    }

    /**
     * Cuotas de hoy de un préstamo específico.
     * GET /admin/v2/pago-card/pagonow
     * (equivale a indexPagonow → ruta pagonow)
     */
    public function indexPagonow(Request $request)
    {
        $uid          = $request->session()->get('usuario_id');
        $prestamoc_id = $request->prestamoc_id;
        $hoy          = $this->hoy();
        $datas        = collect();
        $clientes     = Cliente::where('usuario_id', $uid)->get();
        $datasp       = $this->listaPrestamosPorCliente($uid);

        if ($request->ajax()) {
            $datas = DB::table('prestamo')
                ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
                ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
                ->where('prestamo.usuario_id', $uid)
                ->where('detalle_prestamo.fecha_cuota', $hoy)
                ->where('prestamo.idp', $prestamoc_id)
                ->whereIn('detalle_prestamo.estado', ['C'])
                ->get();

            return DataTables()->of($datas)
                ->addColumn('action', fn($d) =>
                    '<button type="button" class="pay btn-float bg-gradient-warning btn-sm" '.
                    'data-id="'.$d->idd.'" id="'.$d->idd.'" title="Registrar Pago">'.
                    '<i class="fa fa-plus-circle"></i><i class="fas fa-money-bill-alt"></i></button>'
                )
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.v2.pago_card.index', compact('datas', 'clientes', 'datasp'));
    }

    // ──────────────────────────────────────────────────────────────────────
    // MUTACIONES
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Registra un pago.
     * POST /admin/v2/pago-card
     * (equivale a guardar → ruta guardar_pagoc)
     */
    public function guardar(Request $request): JsonResponse
    {
        $hoy    = $this->hoy();
        $vc     = DetallePrestamo::where([
                    ['prestamo_id',    $request->prestamo_id],
                    ['d_numero_cuota', $request->numero_cuota],
                  ])->first();
        $saldo  = Prestamo::where('idp', $request->prestamo_id)->first();
        $saldop = $saldo->monto_pendiente;
        $vcd    = $vc->valor_cuota;

        // ── Adelanto de cuota (fecha futura) ────────────────────────────
        if ($request->fecha_pago > $hoy) {

            if ($request->valor_abono < $vcd && $saldo->monto_atrasado == 0) {
                Pago::create($request->all());
                DB::table('detalle_prestamo')
                    ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                    ->update(['valor_cuota'=>($vcd-$request->valor_abono),'valor_cuota_pagada'=>$request->valor_abono,'estado'=>'C','updated_at'=>now()]);
                DB::table('prestamo')->where('idp',$request->prestamo_id)
                    ->update(['monto_pendiente'=>($saldop-$request->valor_abono),'updated_at'=>now()]);
                return response()->json(['success'=>'abono']);
            }

            if ($vcd == $request->valor_abono && $saldo->monto_atrasado == 0) {
                Pago::create($request->all());
                DB::table('detalle_prestamo')
                    ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                    ->update(['valor_cuota_pagada'=>$request->valor_abono,'estado'=>'P','updated_at'=>now()]);
                DB::table('prestamo')->where('idp',$request->prestamo_id)
                    ->update(['monto_pendiente'=>($saldop-$request->valor_abono),'updated_at'=>now()]);
                return response()->json(['success'=>'okadelanto']);
            }

            if ($request->valor_abono > $vcd) {
                return response()->json(['success'=>'noadelanto']);
            }

            if ($request->valor_abono <= $vcd && $saldo->monto_atrasado >= 0) {
                return response()->json(['success'=>'error']);
            }
        }

        // ── Pago normal o de cuota atrasada ─────────────────────────────
        if ($request->fecha_pago <= $hoy && $request->estado_cuota == 'C') {

            // Pago total del saldo
            if ($request->valor_abono == $saldop) {
                Pago::create($request->all());
                DB::table('detalle_prestamo')->where('prestamo_id',$request->prestamo_id)
                    ->update(['estado'=>'T','updated_at'=>now()]);
                DB::table('detalle_prestamo')
                    ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                    ->update(['valor_cuota_pagada'=>$request->valor_abono,'updated_at'=>now()]);
                $sa = Prestamo::where('idp',$request->prestamo_id)->first();
                DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                    'monto_atrasado'=>0,'cuotas_atrasadas'=>0,
                    'monto_pendiente'=>($saldop-$request->valor_abono),
                    'observacion_prestamo'=>'Cancelado','estado'=>'P','updated_at'=>now(),
                ]);
                return response()->json(['success'=>'total']);
            }

            // Abono parcial (genera atraso)
            if ($request->valor_abono == 0 || $request->valor_abono < $vcd) {
                Pago::create($request->all());
                DB::table('detalle_prestamo')
                    ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                    ->update(['estado'=>'A','valor_cuota_pagada'=>$request->valor_abono,'updated_at'=>now()]);
                $sa = Prestamo::where('idp',$request->prestamo_id)->first();
                DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                    'monto_atrasado'=>($sa->monto_atrasado+($request->valor_cuota-$request->valor_abono)),
                    'cuotas_atrasadas'=>($sa->cuotas_atrasadas+1),
                    'monto_pendiente'=>($saldop-$request->valor_abono),'updated_at'=>now(),
                ]);
                return response()->json(['success'=>'ok']);
            }

            // Pago cuota + saldo atrasado completo
            if ($request->valor_abono == ($vcd + $saldo->monto_atrasado) && $saldo->monto_atrasado > 0) {
                $sa       = Prestamo::where('idp',$request->prestamo_id)->first();
                $abonoat  = $request->valor_abono - $vcd;
                if ($sa->cuotas_atrasadas > 0) {
                    Pago::create($request->all());
                    DB::table('detalle_prestamo')
                        ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                        ->update(['estado'=>'P','valor_cuota_pagada'=>$request->valor_abono,'updated_at'=>now()]);
                    DB::table('detalle_prestamo')
                        ->where([['prestamo_id',$request->prestamo_id],['estado','A']])
                        ->update(['estado'=>'P','updated_at'=>now()]);
                    DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                        'monto_atrasado'=>($sa->monto_atrasado-$abonoat),
                        'cuotas_atrasadas'=>0,
                        'monto_pendiente'=>($saldop-$request->valor_abono),'updated_at'=>now(),
                    ]);
                }
                return response()->json(['success'=>'noat']);
            }

            // Pago exacto de cuota
            if ($vcd == $request->valor_abono) {
                Pago::create($request->all());
                DB::table('detalle_prestamo')
                    ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                    ->update(['estado'=>'P','valor_cuota_pagada'=>$request->valor_abono,'updated_at'=>now()]);
                DB::table('prestamo')->where('idp',$request->prestamo_id)
                    ->update(['monto_pendiente'=>($saldop-$request->valor_abono),'updated_at'=>now()]);
                return response()->json(['success'=>'ok']);
            }

            if ($request->valor_abono > $vcd && $request->valor_abono < $saldop && $saldo->monto_atrasado == 0) {
                return response()->json(['success'=>'adelantos']);
            }
            if ($request->valor_abono > $vcd && $request->valor_abono < ($vcd + $saldo->monto_atrasado)) {
                return response()->json(['success'=>'vcda']);
            }
            if ($request->valor_abono > ($vcd + $saldo->monto_atrasado) && $saldo->monto_atrasado > 0) {
                return response()->json(['success'=>'adelantosa']);
            }
        }

        // ── Cuota atrasada (estado A) ────────────────────────────────────
        if ($request->fecha_pago <= $hoy && $request->estado_cuota == 'A') {
            $sa      = Prestamo::where('idp',$request->prestamo_id)->first();
            $saldop  = $sa->monto_pendiente;
            $pagoa   = Pago::where([['prestamo_id',$request->prestamo_id],['numero_cuota',$request->numero_cuota]])->first();
            $pagoqa  = $pagoa->valor_abono;

            if ($request->valor_abono == $request->vatraso) {
                Pago::create($request->all());
                DB::table('detalle_prestamo')
                    ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                    ->update(['estado'=>'P','valor_cuota_pagada'=>($pagoqa+$request->valor_abono),'updated_at'=>now()]);
                DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                    'monto_pendiente'=>($saldop-$request->valor_abono),
                    'monto_atrasado'=>($sa->monto_atrasado-$request->valor_abono),
                    'cuotas_atrasadas'=>($sa->cuotas_atrasadas-1),'updated_at'=>now(),
                ]);
                return response()->json(['success'=>'okca']);
            }

            if ($request->valor_abono < $request->vatraso && $request->valor_abono > 0) {
                Pago::create($request->all());
                DB::table('detalle_prestamo')
                    ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                    ->update(['estado'=>'A','valor_cuota_pagada'=>($pagoqa+$request->valor_abono),'updated_at'=>now()]);
                DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                    'monto_pendiente'=>($saldop-$request->valor_abono),
                    'monto_atrasado'=>($sa->monto_atrasado-$request->valor_abono),'updated_at'=>now(),
                ]);
                return response()->json(['success'=>'abonoa']);
            }

            return response()->json(['success'=>'okcaerror']);
        }

        return response()->json(['success'=>'error']);
    }

    /**
     * Actualiza (edita) un pago existente.
     * PUT /admin/v2/pago-card/{id}
     * (equivale a actualizar → ruta actualizar_pagoc)
     */
    public function actualizar(Request $request, int $id): JsonResponse
    {
        $vc     = DetallePrestamo::where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])->first();
        $saldo  = Prestamo::where('idp',$request->prestamo_id)->first();
        $saldopv= $saldo->monto_pendiente;
        $vcd    = $vc->valor_cuota;
        $pago   = Pago::where([['prestamo_id',$request->prestamo_id],['numero_cuota',$request->numero_cuota]])->first();
        $pagoq  = $pago->valor_abono;
        $sa     = Prestamo::where('idp',$request->prestamo_id)->first();

        // 1. Revertir el saldo anterior
        if ($pagoq == $vcd) {
            DB::table('prestamo')->where('idp',$request->prestamo_id)
                ->update(['monto_pendiente'=>($saldopv+$pagoq),'updated_at'=>now()]);
        } elseif ($pagoq == 0 || $pagoq < $vcd) {
            DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                'monto_atrasado'=>($sa->monto_atrasado-($request->valor_cuota-$pagoq)),
                'cuotas_atrasadas'=>($sa->cuotas_atrasadas-1),
                'monto_pendiente'=>($saldopv+$pagoq),'updated_at'=>now(),
            ]);
        } elseif ($pagoq > $vcd && $pagoq < $saldopv) {
            $abonoat     = $pagoq - $vcd;
            $cuotasatdesc= round($abonoat/$vcd, 2);
            if ($sa->monto_atrasado > 0) {
                DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                    'monto_atrasado'=>($sa->monto_atrasado+$abonoat),
                    'cuotas_atrasadas'=>($sa->cuotas_atrasadas+$cuotasatdesc),
                    'monto_pendiente'=>($saldopv+$pagoq),'updated_at'=>now(),
                ]);
            } else {
                DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                    'monto_atrasado'=>0,'longitud'=>($sa->longitud-($pagoq-$vcd)),
                    'cuotas_atrasadas'=>0,'monto_pendiente'=>($saldopv+$pagoq),'updated_at'=>now(),
                ]);
            }
        }

        $saldop = Prestamo::where('idp',$request->prestamo_id)->value('monto_pendiente');
        $sa2    = Prestamo::where('idp',$request->prestamo_id)->first();

        // 2. Aplicar nuevo valor
        if ($request->valor_abono == 0 || $request->valor_abono < $vcd) {
            DB::table('detalle_prestamo')
                ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                ->update(['estado'=>'A','valor_cuota_pagada'=>$request->valor_abono,'updated_at'=>now()]);
            $pago->update($request->all());
            $sa3 = Prestamo::where('idp',$request->prestamo_id)->first();
            DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                'monto_atrasado'=>($sa3->monto_atrasado+($request->valor_cuota-$request->valor_abono)),
                'cuotas_atrasadas'=>($sa3->cuotas_atrasadas+1),
                'monto_pendiente'=>($saldop-$request->valor_abono),'updated_at'=>now(),
            ]);
        } elseif ($request->valor_abono > $vcd && $request->valor_abono < $saldop) {
            $abonoat     = $request->valor_abono - $vcd;
            $cuotasatdesc= round($abonoat/$vcd, 2);
            if ($cuotasatdesc <= $sa2->cuotas_atrasadas && $abonoat <= $sa2->monto_atrasado) {
                DB::table('detalle_prestamo')
                    ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                    ->update(['estado'=>'P','valor_cuota_pagada'=>$request->valor_abono,'updated_at'=>now()]);
                $pago->update($request->all());
                DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                    'monto_atrasado'=>($sa2->monto_atrasado-$abonoat),
                    'cuotas_atrasadas'=>($sa2->cuotas_atrasadas-$cuotasatdesc),
                    'monto_pendiente'=>($saldop-$request->valor_abono),'updated_at'=>now(),
                ]);
            } elseif ($sa2->cuotas_atrasadas == 0) {
                DB::table('detalle_prestamo')
                    ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                    ->update(['estado'=>'P','valor_cuota_pagada'=>$request->valor_abono,'updated_at'=>now()]);
                $pago->update($request->all());
                DB::table('prestamo')->where('idp',$request->prestamo_id)->update([
                    'monto_pendiente'=>($saldop-$request->valor_abono),
                    'longitud'=>($request->valor_abono-$vcd),'updated_at'=>now(),
                ]);
            } else {
                return response()->json(['success'=>'noa']);
            }
        } elseif ($vcd == $request->valor_abono) {
            DB::table('detalle_prestamo')
                ->where([['prestamo_id',$request->prestamo_id],['d_numero_cuota',$request->numero_cuota]])
                ->update(['estado'=>'P','valor_cuota_pagada'=>$request->valor_abono,'updated_at'=>now()]);
            $pago->update($request->all());
            DB::table('prestamo')->where('idp',$request->prestamo_id)
                ->update(['monto_pendiente'=>($saldop-$request->valor_abono),'updated_at'=>now()]);
        }

        return response()->json(['success'=>'oka']);
    }

    // ──────────────────────────────────────────────────────────────────────
    // CONSULTAS AJAX
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Datos de detalle_prestamo para el modal de pago (por idd).
     * GET /admin/v2/pago-card/{id}/editar
     */
    public function editar(Request $request, int $id): JsonResponse
    {
        if (!$request->ajax()) abort(403);
        $uid = $request->session()->get('usuario_id');

        $base = DB::table('prestamo')
            ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
            ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
            ->where('prestamo.usuario_id', $uid)
            ->where('detalle_prestamo.idd', $id);

        $withPago = (clone $base)
            ->join('pago', function ($j) {
                $j->on('detalle_prestamo.d_numero_cuota', '=', 'pago.numero_cuota')
                  ->on('detalle_prestamo.prestamo_id',    '=', 'pago.prestamo_id')
                  ->on('prestamo.idp',                    '=', 'pago.prestamo_id');
            })->first();

        $data = $withPago
            ? (clone $base)->join('pago', function ($j) {
                $j->on('detalle_prestamo.d_numero_cuota','=','pago.numero_cuota')
                  ->on('detalle_prestamo.prestamo_id','=','pago.prestamo_id')
                  ->on('prestamo.idp','=','pago.prestamo_id');
              })->get()
            : $base->get();

        return response()->json(['result' => $data]);
    }

    /**
     * Datos por prestamo_id + fecha (para botón payp).
     * GET /admin/v2/pago-card/{id}/editarp
     */
    public function editarp(Request $request, int $id): JsonResponse
    {
        if (!$request->ajax()) abort(403);
        $uid = $request->session()->get('usuario_id');
        $idf = $request->idf;

        $base = DB::table('prestamo')
            ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
            ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
            ->where('prestamo.usuario_id', $uid)
            ->where('detalle_prestamo.prestamo_id', $id)
            ->where('detalle_prestamo.fecha_cuota', $idf);

        $withPago = (clone $base)
            ->join('pago', function ($j) {
                $j->on('detalle_prestamo.d_numero_cuota','=','pago.numero_cuota')
                  ->on('detalle_prestamo.prestamo_id','=','pago.prestamo_id')
                  ->on('prestamo.idp','=','pago.prestamo_id');
            })->first();

        $data = $withPago
            ? (clone $base)->join('pago', function ($j) {
                $j->on('detalle_prestamo.d_numero_cuota','=','pago.numero_cuota')
                  ->on('detalle_prestamo.prestamo_id','=','pago.prestamo_id')
                  ->on('prestamo.idp','=','pago.prestamo_id');
              })->get()
            : $base->get();

        return response()->json(['result' => $data]);
    }

    /**
     * Último pago de una cuota (para editar pago registrado).
     * GET /admin/v2/pago-card/{id}/editpay
     */
    public function editpay(Request $request, int $id): JsonResponse
    {
        if (!$request->ajax()) abort(403);
        $uid = $request->session()->get('usuario_id');

        $data = DB::table('prestamo')
            ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
            ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
            ->join('pago', function ($j) {
                $j->on('detalle_prestamo.d_numero_cuota','=','pago.numero_cuota')
                  ->on('detalle_prestamo.prestamo_id','=','pago.prestamo_id')
                  ->on('prestamo.idp','=','pago.prestamo_id');
            })
            ->where('prestamo.usuario_id', $uid)
            ->where('detalle_prestamo.idd', $id)
            ->latest('pago.updated_at')
            ->first();

        return response()->json(['result' => $data]);
    }

    /**
     * Pagos realizados a un préstamo.
     * GET /admin/v2/pago-card/{id}
     * (equivale a detalle → ruta detalle_pagoc)
     */
    public function detalle(int $id): JsonResponse
    {
        if (!request()->ajax()) abort(403);

        $pagos = DB::table('pago')->where('prestamo_id', $id)->get();
        return response()->json(['result1' => $pagos]);
    }

    // ──────────────────────────────────────────────────────────────────────
    // HELPERS PRIVADOS DE HTML (botones y cards)
    // ──────────────────────────────────────────────────────────────────────

    /** Botones card para la vista por prestamo (payp / pagosr). */
    private function botonesCardPrestamo(object $d, string $claseAccion): string
    {
        $cuotasPdt = $d->valor_cuota > 0 ? round($d->monto_pendiente / $d->valor_cuota) : '?';
        return '<div class="row"><div class="col-md-6 col-xs-12">
          <div class="card"><div class="card-body bg-olive">
            <a class="'.$claseAccion.' btn btn-flotante btn-app bg-secondary" id="'.$d->idp.'" idf="'.now()->toDateString().'">
              <span class="badge bg-success">Crédito: '.$d->idp.'</span>
              <i class="fa fa-plus-circle fa-lg"></i> Pago</a>
            <a class="detallepay btn btn-flotante btn-app bg-info" id="'.$d->idp.'">
              <span class="badge bg-teal">Cuotas Pdt: '.$cuotasPdt.'/'.$d->cuotas.'</span>
              <i class="fas fa-book-reader"></i> Detalle Pay</a>
            <a class="adelantoc btn btn-flotante btn-app bg-warning" id="'.$d->idp.'">
              <span class="badge bg-info">Valor cuota $'.$d->valor_cuota.'</span>
              <i class="fa fa-credit-card"></i> Add Cuotas</a>
            <a class="atrasosp btn btn-flotante btn-app bg-danger" id="'.$d->idp.'">
              <span class="badge bg-warning">'.$d->cuotas_atrasadas.'</span>
              <i class="fa fa-credit-card"></i> Atrasos</a>
          </div></div></div></div>';
    }

    /** Botones para la vista tab (pay / editpay). */
    private function botonesTab(object $d, string $claseAccion): string
    {
        $cuotasPdt = $d->valor_cuota > 0 ? round($d->monto_pendiente / $d->valor_cuota) : '?';
        $idBtn = ($claseAccion === 'pay') ? $d->idd : $d->idd;
        return '<div class="row"><div class="col-md-6 col-xs-12">
          <div class="card"><div class="card-body">
            <a class="'.$claseAccion.' btn btn-flotante btn-app bg-secondary" id="'.$idBtn.'">
              <span class="badge bg-success">Cuota: '.$d->d_numero_cuota.'</span>
              <i class="fa fa-plus-circle fa-lg"></i> Pago cuota</a>
            <a class="detallepay btn btn-flotante btn-app bg-info" id="'.$d->idp.'">
              <span class="badge bg-teal">Cuotas Pdt: '.$cuotasPdt.'/'.$d->cuotas.'</span>
              <i class="fas fa-book-reader"></i> Detalle Pay</a>
            <a class="adelantoc btn btn-flotante btn-app bg-warning" id="'.$d->idp.'">
              <span class="badge bg-info">Valor cuota $'.$d->valor_cuota.'</span>
              <i class="fa fa-credit-card"></i> Add Cuotas</a>
            <a class="atrasosp btn btn-flotante btn-app bg-danger" id="'.$d->idp.'">
              <span class="badge bg-warning">'.$d->cuotas_atrasadas.'</span>
              <i class="fa fa-credit-card"></i> Atrasos</a>
          </div></div></div></div>';
    }

    /** Card de información del cliente. */
    private function cardClientePrestamo(object $d, string $bgClass): string
    {
        return '<div class="row"><div class="col-md-6 col-xs-12">
          <div class="small-box '.$bgClass.'">
            <div class="inner">
              <h4>'.$d->nombres.'</h4><h4>'.$d->apellidos.'</h4>
              <p><i class="fas fa-map-marked"></i>: '.$d->direccion.'</p>
              <p><i class="fas fa-phone-square-alt"></i>: '.$d->telefono.'</p>
              <p><a href="tel:+'.$d->celular.'"><i class="fas fa-mobile-alt"></i>: '.$d->celular.'</a></p>
              <p><i class="fas fa-sort-amount-down-alt"></i>: '.$d->consecutivo.'</p>
            </div>
            <button type="button" class="detalle btn btn-flotante1 btn-app bg-secondary small-box-footer" id="'.$d->idp.'">
              <span class="badge bg-teal">Saldo: '.$d->monto_pendiente.'</span>
              <i class="fa fa-atlas fa-lg"></i> #Crédito '.$d->idp.'
            </button>
          </div></div></div>';
    }

    /** Lista de préstamos activos para el select de préstamo en el formulario. */
    private function listaPrestamosPorCliente(int $uid): array
    {
        return DB::table('prestamo')
            ->join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where([['prestamo.usuario_id', $uid], ['prestamo.estado', '!=', 'P']])
            ->pluck('cliente.nombres', 'prestamo.idp')
            ->toArray();
    }
}
