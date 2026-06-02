<?php

namespace App\Http\Controllers;

use App\Models\Admin\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientePortalController extends Controller
{
    public function index(Request $request)
    {
        $documento = trim($request->get('documento', ''));
        $cliente   = null;
        $prestamos = collect();
        $cuotas    = collect();
        $error     = null;

        if ($documento !== '') {
            $cliente = Cliente::where('documento', $documento)
                ->where('activo', 1)
                ->first();

            if (!$cliente) {
                $error = 'No se encontró ningún cliente activo con ese documento.';
            } else {
                $prestamos = DB::table('prestamo')
                    ->where('prestamo.cliente_id', $cliente->id)
                    ->whereNull('prestamo.delete_at')
                    ->selectRaw('
                        prestamo.idp, prestamo.monto, prestamo.monto_pendiente,
                        prestamo.cuotas, prestamo.tipo_pago, prestamo.fecha_inicial,
                        prestamo.fecha_final, prestamo.estado, prestamo.valor_cuota,
                        SUM(CASE WHEN dp.estado IN (\'P\',\'T\') THEN 1 ELSE 0 END) as cuotas_pagadas,
                        SUM(CASE WHEN dp.estado = \'A\'           THEN 1 ELSE 0 END) as cuotas_atrasadas,
                        SUM(CASE WHEN dp.estado = \'C\'           THEN 1 ELSE 0 END) as cuotas_pendientes_cnt
                    ')
                    ->leftJoin('detalle_prestamo as dp', 'prestamo.idp', '=', 'dp.prestamo_id')
                    ->groupBy(
                        'prestamo.idp', 'prestamo.monto', 'prestamo.monto_pendiente',
                        'prestamo.cuotas', 'prestamo.tipo_pago', 'prestamo.fecha_inicial',
                        'prestamo.fecha_final', 'prestamo.estado', 'prestamo.valor_cuota'
                    )
                    ->orderByDesc('prestamo.idp')
                    ->get();

                // Cuotas pendientes y atrasadas de todos los préstamos del cliente
                $prestamoIds = $prestamos->pluck('idp')->toArray();
                if ($prestamoIds) {
                    $cuotas = DB::table('detalle_prestamo')
                        ->whereIn('prestamo_id', $prestamoIds)
                        ->whereIn('estado', ['A', 'C'])
                        ->select('idd', 'prestamo_id', 'd_numero_cuota', 'valor_cuota', 'valor_cuota_pagada', 'fecha_cuota', 'estado')
                        ->orderBy('prestamo_id')
                        ->orderBy('d_numero_cuota')
                        ->get()
                        ->groupBy('prestamo_id');
                }
            }
        }

        return view('cliente.portal', compact('cliente', 'prestamos', 'cuotas', 'error', 'documento'));
    }
}
