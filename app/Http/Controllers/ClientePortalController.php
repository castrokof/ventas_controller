<?php

namespace App\Http\Controllers;

use App\Models\Admin\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientePortalController extends Controller
{
    private function authedCliente(): ?Cliente
    {
        $id = session('portal_cliente_id');
        return $id ? Cliente::find($id) : null;
    }

    private function defaultPassword(Cliente $cliente): string
    {
        return substr((string) $cliente->documento, -6);
    }

    /* ── Login form ────────────────────────────────────────── */
    public function index()
    {
        if (session('portal_cliente_id')) {
            return redirect()->route('cliente.portal.dashboard');
        }
        return view('cliente.login');
    }

    /* ── Autenticar ────────────────────────────────────────── */
    public function login(Request $request)
    {
        $documento = trim($request->input('documento', ''));
        $password  = $request->input('password', '');

        if (!$documento || !$password) {
            return back()->withErrors(['Ingresa tu número de documento y contraseña.'])
                         ->withInput(['documento' => $documento]);
        }

        $cliente = Cliente::where('documento', $documento)->where('activo', 1)->first();

        if (!$cliente) {
            return back()->withErrors(['No se encontró ningún cliente activo con ese documento.'])
                         ->withInput(['documento' => $documento]);
        }

        if (is_null($cliente->portal_password)) {
            $valid = ($password === $this->defaultPassword($cliente));
        } else {
            $valid = Hash::check($password, $cliente->portal_password);
        }

        if (!$valid) {
            return back()->withErrors(['Contraseña incorrecta. Por defecto son los últimos 6 dígitos de tu documento.'])
                         ->withInput(['documento' => $documento]);
        }

        $request->session()->put('portal_cliente_id', $cliente->id);
        return redirect()->route('cliente.portal.dashboard');
    }

    /* ── Dashboard — mis préstamos ─────────────────────────── */
    public function dashboard()
    {
        $cliente = $this->authedCliente();
        if (!$cliente) return redirect()->route('cliente.portal.index');

        $prestamos = DB::table('prestamo')
            ->where('prestamo.cliente_id', $cliente->id)
            ->whereNull('prestamo.delete_at')
            ->selectRaw("
                prestamo.idp, prestamo.monto, prestamo.monto_pendiente,
                prestamo.cuotas, prestamo.tipo_pago, prestamo.fecha_inicial,
                prestamo.fecha_final, prestamo.estado, prestamo.valor_cuota,
                SUM(CASE WHEN dp.estado IN ('P','T') THEN 1 ELSE 0 END) as cuotas_pagadas,
                SUM(CASE WHEN dp.estado = 'A'        THEN 1 ELSE 0 END) as cuotas_atrasadas,
                SUM(CASE WHEN dp.estado = 'C'        THEN 1 ELSE 0 END) as cuotas_pendientes_cnt
            ")
            ->leftJoin('detalle_prestamo as dp', 'prestamo.idp', '=', 'dp.prestamo_id')
            ->groupBy(
                'prestamo.idp', 'prestamo.monto', 'prestamo.monto_pendiente',
                'prestamo.cuotas', 'prestamo.tipo_pago', 'prestamo.fecha_inicial',
                'prestamo.fecha_final', 'prestamo.estado', 'prestamo.valor_cuota'
            )
            ->orderByDesc('prestamo.idp')
            ->get();

        $prestamoIds = $prestamos->pluck('idp')->toArray();
        $cuotas = collect();
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

        return view('cliente.portal', compact('cliente', 'prestamos', 'cuotas'));
    }

    /* ── Cerrar sesión ─────────────────────────────────────── */
    public function logout(Request $request)
    {
        $request->session()->forget('portal_cliente_id');
        return redirect()->route('cliente.portal.index');
    }

    /* ── Cambiar contraseña — form ─────────────────────────── */
    public function showChangePassword()
    {
        $cliente = $this->authedCliente();
        if (!$cliente) return redirect()->route('cliente.portal.index');
        return view('cliente.cambiar-password', compact('cliente'));
    }

    /* ── Cambiar contraseña — guardar ──────────────────────── */
    public function changePassword(Request $request)
    {
        $cliente = $this->authedCliente();
        if (!$cliente) return redirect()->route('cliente.portal.index');

        $nueva    = $request->input('nueva_password', '');
        $confirma = $request->input('confirmar_password', '');

        if (strlen($nueva) < 6) {
            return back()->withErrors(['La contraseña debe tener al menos 6 caracteres.']);
        }
        if ($nueva !== $confirma) {
            return back()->withErrors(['Las contraseñas no coinciden.']);
        }

        $cliente->portal_password = Hash::make($nueva);
        $cliente->save();

        return back()->with('success', '¡Contraseña actualizada correctamente!');
    }
}
