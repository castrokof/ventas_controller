<?php

namespace App\Http\Controllers\Admin\V2;

use App\Http\Controllers\Controller;
use App\Models\Admin\Cliente;
use App\Models\Admin\Prestamo;
use App\Models\Seguridad\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * V2 ClienteController — standalone, sin extends.
 *
 * Implementa la lógica de ClienteController usando los mismos modelos, pero:
 *  - Renderiza vistas admin.v2.cliente.*
 *  - Inicializa $datas antes del bloque ajax
 *  - Validación centralizada y respuestas JSON estandarizadas
 */
class ClienteController extends Controller
{
    // ── Reglas de validación compartidas ──────────────────────────────────
    private function rules(): array
    {
        return [
            'nombres'        => 'required|max:100',
            'apellidos'      => 'required|max:100',
            'documento'      => 'numeric|required|min:10000|max:999999999999',
            'celular'        => 'numeric|required|min:10000|max:9999999999999999',
            'tipo_documento' => 'required',
            'usuario_id'     => 'required',
            'ciudad'         => 'required',
            'pais'           => 'required',
            'estado'         => 'required',
            'direccion'      => 'required',
            'consecutivo'    => 'numeric|required|min:1|max:9999999999',
            'activo'         => 'required',
        ];
    }

    /**
     * Devuelve la lista de usuario_ids visibles para el usuario en sesión.
     * - rol 2 (empresa): todos los usuarios de la empresa
     * - resto: solo el propio usuario
     */
    private function scopeUsuarioIds(): array
    {
        $uid    = (int) session('usuario_id');
        $rol_id = (int) session('rol_id');

        if ($rol_id === 2) {
            $emp_id     = session('empleado_id');
            $empresa_id = DB::table('empleado')->where('ide', $emp_id)->value('empresa_id');
            if ($empresa_id) {
                $ids = DB::table('usuario')
                    ->join('empleado', 'usuario.empleado_id', '=', 'empleado.ide')
                    ->where('empleado.empresa_id', $empresa_id)
                    ->pluck('usuario.id')
                    ->toArray();
                return $ids ?: [$uid];
            }
        }

        return [$uid];
    }

    /**
     * index — lista de clientes con DataTable AJAX.
     */
    public function index(Request $request)
    {
        $id_usuario = (int) Session()->get('usuario_id');
        $uids       = $this->scopeUsuarioIds();

        $usuarios = Usuario::orderBy('id')
            ->where('id', '=', $id_usuario)
            ->pluck('usuario', 'id')
            ->toArray();

        $datas = collect(); // evita variable indefinida en la vista

        if ($request->ajax() || $request->has('draw')) {
            $datas = Cliente::whereIn('usuario_id', $uids)
                ->orderBy('usuario_id')
                ->orderBy('consecutivo')
                ->get();

            return DataTables()->of($datas)
                ->addColumn('action', function (Cliente $cliente) {
                    $edit = '<button type="button" name="edit" id="' . $cliente->id . '"
                        class="edit btn-float bg-gradient-primary btn-sm tooltipsC"
                        title="Editar Cliente"><i class="far fa-edit"></i></button>';

                    $prestamo = '&nbsp;<button type="button" name="prestamo" id="' . $cliente->id . '"
                        class="prestamo btn-float bg-gradient-warning btn-sm tooltipsC"
                        title="Agregar Préstamo">
                        <i class="fa fa-fw fa-plus-circle"></i>
                        <i class="fas fa-money-bill-alt"></i></button>';

                    $detalle = '&nbsp;<button type="button" name="detalle" id="' . $cliente->id . '"
                        class="detalle btn-float bg-gradient-success btn-sm tooltipsC"
                        title="Detalle de Préstamos"><i class="fas fa-atlas"></i></button>';

                    $calificacion = '&nbsp;<button type="button" name="calificacion" id="' . $cliente->id . '"
                        class="calificacion btn-float bg-gradient-dark btn-sm tooltipsC"
                        title="Calificación del cliente"><i class="fas fa-star"></i></button>';

                    $resetpwd = '&nbsp;<button type="button" name="resetpwd" id="' . $cliente->id . '"
                        class="resetpwd btn-float bg-gradient-info btn-sm tooltipsC"
                        title="Restablecer contraseña portal"><i class="fas fa-key"></i></button>';

                    return $edit . $prestamo . $detalle . $calificacion . $resetpwd;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.v2.cliente.index', compact('usuarios', 'datas'));
    }

    /**
     * guardar — crea un nuevo cliente.
     */
    public function guardar(Request $request): JsonResponse
    {
        $data = $request->all();

        if (empty($data['usuario_id'])) {
            $data['usuario_id'] = (int) session('usuario_id');
        }
        if (!isset($data['activo']) || $data['activo'] === '') {
            $data['activo'] = 1;
        }
        if (empty($data['consecutivo'])) {
            $data['consecutivo'] = (int) Cliente::where('usuario_id', $data['usuario_id'])->max('consecutivo') + 1;
        }

        $error = Validator::make($data, $this->rules());

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $cliente = Cliente::create($data);

        return response()->json([
            'success'   => 'ok',
            'id'        => $cliente->id,
            'documento' => $cliente->documento,
            'nombres'   => $cliente->nombres,
            'apellidos' => $cliente->apellidos,
        ]);
    }

    /**
     * editar — devuelve datos del cliente para edición (AJAX) o vista.
     */
    public function editar(int $id)
    {
        $id_usuario = Session()->get('usuario_id');

        $usuarios = Usuario::orderBy('id')
            ->where('id', '=', $id_usuario)
            ->pluck('usuario', 'id')
            ->toArray();

        if (request()->ajax()) {
            $data = Cliente::where('id', $id)
                ->whereIn('usuario_id', $this->scopeUsuarioIds())
                ->firstOrFail();
            return response()->json(['result' => $data]);
        }

        return view('admin.v2.cliente.index', compact('usuarios'));
    }

    /**
     * actualizar — actualiza un cliente existente.
     */
    public function actualizar(Request $request, int $id): JsonResponse
    {
        $error = Validator::make($request->all(), $this->rules());

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $cliente = Cliente::where('id', $id)
            ->whereIn('usuario_id', $this->scopeUsuarioIds())
            ->firstOrFail();
        $cliente->update($request->all());

        return response()->json(['success' => 'ok1']);
    }

    /**
     * detalle — devuelve préstamos del cliente (AJAX).
     */
    public function detalle(int $id): JsonResponse
    {
        $result = Prestamo::where('cliente_id', '=', $id)
            ->whereIn('usuario_id', $this->scopeUsuarioIds())
            ->get();

        return response()->json(['result' => $result]);
    }

    /**
     * calificacion — historial de pago y score del cliente (AJAX).
     * GET /admin/v2/cliente/{id}/calificacion
     */
    public function calificacion(int $id): JsonResponse
    {
        $cliente = Cliente::where('id', $id)
            ->whereIn('usuario_id', $this->scopeUsuarioIds())
            ->firstOrFail();

        // Préstamos del cliente (visibles por scope)
        $prestamoIds = Prestamo::where('cliente_id', $id)
            ->whereIn('usuario_id', $this->scopeUsuarioIds())
            ->whereNull('delete_at')
            ->pluck('idp')
            ->toArray();

        $totalPrestamos = count($prestamoIds);

        if ($totalPrestamos === 0) {
            return response()->json([
                'cliente'        => $cliente->nombres . ' ' . $cliente->apellidos,
                'documento'      => $cliente->documento,
                'total_prestamos'=> 0,
                'total_cuotas'   => 0,
                'pagadas'        => 0,
                'atrasadas'      => 0,
                'pendientes'     => 0,
                'monto_total'    => 0,
                'monto_pagado'   => 0,
                'score'          => 100,
                'calificacion'   => 'Sin historial',
                'nivel'          => 'nuevo',
            ]);
        }

        // Estadísticas de cuotas
        $stats = DB::table('detalle_prestamo')
            ->whereIn('prestamo_id', $prestamoIds)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN estado IN (\'P\',\'T\') THEN 1 ELSE 0 END) as pagadas,
                SUM(CASE WHEN estado = \'A\'           THEN 1 ELSE 0 END) as atrasadas,
                SUM(CASE WHEN estado = \'C\'           THEN 1 ELSE 0 END) as pendientes,
                SUM(valor_cuota)        as monto_total,
                SUM(valor_cuota_pagada) as monto_pagado
            ')
            ->first();

        $total     = (int)   ($stats->total      ?? 0);
        $pagadas   = (int)   ($stats->pagadas    ?? 0);
        $atrasadas = (int)   ($stats->atrasadas  ?? 0);
        $pendientes= (int)   ($stats->pendientes ?? 0);
        $historial = $pagadas + $atrasadas; // cuotas ya vencidas (base del score)

        // Score: % de cuotas vencidas que se pagaron; si sin historial 100
        $score = $historial > 0
            ? round($pagadas / $historial * 100)
            : 100;

        // Penalización por atrasadas actuales
        $score = max(0, $score - min($atrasadas * 2, 20));

        if ($score >= 90 && $atrasadas === 0) {
            $nivel = 'A'; $calificacion = 'Excelente';
        } elseif ($score >= 75) {
            $nivel = 'B'; $calificacion = 'Bueno';
        } elseif ($score >= 55) {
            $nivel = 'C'; $calificacion = 'Regular';
        } else {
            $nivel = 'D'; $calificacion = 'Alto riesgo';
        }

        // Historial por préstamo
        $historialPrestamos = DB::table('prestamo')
            ->whereIn('prestamo.idp', $prestamoIds)
            ->selectRaw('
                prestamo.idp, prestamo.monto, prestamo.monto_pendiente,
                prestamo.cuotas, prestamo.tipo_pago, prestamo.fecha_inicial,
                prestamo.estado as estado_prestamo,
                SUM(CASE WHEN dp.estado IN (\'P\',\'T\') THEN 1 ELSE 0 END) as dp_pagadas,
                SUM(CASE WHEN dp.estado = \'A\'          THEN 1 ELSE 0 END) as dp_atrasadas
            ')
            ->leftJoin('detalle_prestamo as dp', 'prestamo.idp', '=', 'dp.prestamo_id')
            ->groupBy('prestamo.idp','prestamo.monto','prestamo.monto_pendiente',
                      'prestamo.cuotas','prestamo.tipo_pago','prestamo.fecha_inicial',
                      'prestamo.estado')
            ->orderByDesc('prestamo.idp')
            ->get();

        return response()->json([
            'cliente'          => $cliente->nombres . ' ' . $cliente->apellidos,
            'documento'        => $cliente->documento,
            'total_prestamos'  => $totalPrestamos,
            'total_cuotas'     => $total,
            'pagadas'          => $pagadas,
            'atrasadas'        => $atrasadas,
            'pendientes'       => $pendientes,
            'monto_total'      => round((float)($stats->monto_total  ?? 0), 2),
            'monto_pagado'     => round((float)($stats->monto_pagado ?? 0), 2),
            'score'            => $score,
            'calificacion'     => $calificacion,
            'nivel'            => $nivel,
            'prestamos'        => $historialPrestamos,
        ]);
    }

    /**
     * reordenar — actualiza el consecutivo de varios clientes en una sola llamada.
     * POST /admin/v2/cliente/reordenar
     * Body: cambios[] = [{id, consecutivo}, ...]
     */
    public function reordenar(Request $request): JsonResponse
    {
        $cambios = $request->input('cambios', []);
        if (empty($cambios)) {
            return response()->json(['error' => 'Sin cambios.'], 422);
        }

        $uids = $this->scopeUsuarioIds();

        DB::transaction(function () use ($cambios, $uids) {
            foreach ($cambios as $c) {
                $id   = (int) ($c['id']          ?? 0);
                $cons = (int) ($c['consecutivo'] ?? 0);
                if ($id > 0 && $cons > 0) {
                    Cliente::where('id', $id)
                        ->whereIn('usuario_id', $uids)
                        ->update(['consecutivo' => $cons]);
                }
            }
        });

        return response()->json(['success' => true]);
    }

    /**
     * resetPassword — restablece la contraseña del portal al valor por defecto
     * (últimos 6 dígitos del documento). Solo accesible vía AJAX.
     * POST /admin/v2/cliente/{id}/reset-password
     */
    public function resetPassword(int $id): JsonResponse
    {
        $cliente = Cliente::where('id', $id)
            ->whereIn('usuario_id', $this->scopeUsuarioIds())
            ->firstOrFail();

        $cliente->portal_password = null;
        $cliente->save();

        $default = substr((string) $cliente->documento, -6);

        return response()->json([
            'success' => 'ok',
            'default' => $default,
            'nombre'  => $cliente->nombres . ' ' . $cliente->apellidos,
        ]);
    }
}
