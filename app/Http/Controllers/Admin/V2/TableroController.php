<?php

namespace App\Http\Controllers\Admin\V2;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableroController extends Controller
{
    /**
     * Dashboard V2 — resumen operativo del usuario en sesión.
     * GET /admin/v2/tablero
     */
    public function index(Request $request)
    {
        $usuarioId = $request->session()->get('usuario_id');
        $hoy       = Carbon::today()->toDateString();

        // ── Clientes ────────────────────────────────────────────────────────
        $totalClientes = DB::table('cliente')
            ->where('usuario_id', $usuarioId)
            ->count();

        // ── Préstamos activos (con saldo pendiente, no anulados) ─────────
        $totalPrestamos = DB::table('prestamo')
            ->where('usuario_id', $usuarioId)
            ->where('monto_pendiente', '>', 0)
            ->whereNull('delete_at')
            ->count();

        // ── Monto total atrasado ─────────────────────────────────────────
        $montoAtrasado = (int) DB::table('prestamo')
            ->where('usuario_id', $usuarioId)
            ->whereNull('delete_at')
            ->sum('monto_atrasado');

        // ── Cuotas de hoy ────────────────────────────────────────────────
        $cuotasHoy = DB::table('detalle_prestamo')
            ->join('prestamo', 'detalle_prestamo.prestamo_id', '=', 'prestamo.idp')
            ->where('prestamo.usuario_id', $usuarioId)
            ->where('detalle_prestamo.fecha_cuota', $hoy)
            ->selectRaw('
                SUM(detalle_prestamo.valor_cuota) as total_cobrar,
                SUM(CASE WHEN detalle_prestamo.estado = "P" THEN 1 ELSE 0 END) as pagadas,
                SUM(CASE WHEN detalle_prestamo.estado = "C" THEN 1 ELSE 0 END) as pendientes
            ')
            ->first();

        // ── Gastos del mes en curso ──────────────────────────────────────
        $gastosMes = (int) DB::table('gasto')
            ->where('usuario_id', $usuarioId)
            ->whereNull('delete_at')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at',  Carbon::now()->year)
            ->sum('monto');

        return view('admin.v2.tablero.index', [
            'totalClientes'  => $totalClientes,
            'totalPrestamos' => $totalPrestamos,
            'montoAtrasado'  => $montoAtrasado,
            'totalCobrar'    => (int) ($cuotasHoy->total_cobrar ?? 0),
            'cuotasPagadas'  => (int) ($cuotasHoy->pagadas      ?? 0),
            'cuotasPendientes' => (int) ($cuotasHoy->pendientes ?? 0),
            'gastosMes'      => $gastosMes,
            'fechaHoy'       => Carbon::today()->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY'),
        ]);
    }
}
