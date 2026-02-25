<?php

// app/Http/Controllers/Admin/V2/PrestamoController.php

namespace App\Http\Controllers\Admin\V2;

use App\Http\Controllers\Controller;
use App\Models\Admin\Cliente;
use App\Models\Admin\DetallePrestamo;
use App\Models\Admin\Prestamo;
use App\Models\Admin\Pago;
use App\Models\Seguridad\Usuario;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * V2 PrestamoController
 *
 * Mejoras respecto al original:
 *  1. generateInstallments() — elimina ~350 líneas de código duplicado
 *     (guardar y refiguardar tenían el mismo loop de cuotas copiado).
 *  2. advanceDate()          — extrae la lógica de avance de fecha en un
 *     único método, sin cambiar el comportamiento (bisiesto, diciembre, sábados).
 *  3. Bug fix: $saldop no definida en refiguardar() → usa $saldoa->monto_pendiente.
 *  4. Inserts en batch (array de rows) en lugar de insert por cuota en el loop.
 *  5. Usa Prestamo::latest('idp')->value('idp') en vez del subquery manual.
 *  6. Validation messages en español.
 *  7. index() renderiza la vista admin.v2.prestamo.index (parallel path).
 */
class PrestamoController extends Controller
{
    // ─── Mensajes de validación en español ──────────────────────────────────
    private const MESSAGES = [
        'monto.required'        => 'El monto es obligatorio.',
        'tipo_pago.required'    => 'Seleccione el tipo de pago.',
        'cuotas.required'       => 'El número de cuotas es obligatorio.',
        'interes.required'      => 'El interés es obligatorio.',
        'valor_cuota.required'  => 'El valor de cuota es obligatorio.',
        'fecha_inicial.required'=> 'La fecha inicial es obligatoria.',
        'usuario_id.required'   => 'El usuario es obligatorio.',
        'activo.required'       => 'El estado es obligatorio.',
    ];

    // ─── Reglas de validación reutilizables ─────────────────────────────────
    private const RULES = [
        'monto'         => 'required|numeric|min:1',
        'tipo_pago'     => 'required|in:Diario,Semanal,Quincenal,Mensual',
        'cuotas'        => 'required|integer|min:1',
        'interes'       => 'required|numeric|min:0',
        'valor_cuota'   => 'required|numeric|min:1',
        'fecha_inicial' => 'required|date',
        'usuario_id'    => 'required|integer',
        'activo'        => 'required',
    ];

    // ────────────────────────────────────────────────────────────────────────
    // VISTAS
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Vista V2 — lista de préstamos activos.
     * GET /admin/v2/prestamo
     */
    public function index(Request $request)
    {
        $usuario_id = $request->session()->get('usuario_id');

        $clientes = Cliente::where('usuario_id', $usuario_id)->get();
        $usuarios = Usuario::orderBy('id')
            ->where('id', $usuario_id)
            ->pluck('usuario', 'id')
            ->toArray();

        if ($request->ajax()) {
            $datas = DB::table('prestamo')
                ->join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->where([
                    ['prestamo.usuario_id',      '=', $usuario_id],
                    ['prestamo.monto_pendiente', '>',  0],
                    ['prestamo.delete_at',       '=',  null],
                ])
                ->get();

            return DataTables()->of($datas)
                ->addColumn('action', function ($row) {
                    $btnPagos = '<button type="button"
                        class="btn-v2-action bg-gradient-warning btn-sm tooltipsC pagos"
                        data-id="' . $row->idp . '"
                        title="Detalle de Pagos"
                        aria-label="Ver detalle de pagos del préstamo ' . $row->idp . '">
                        <i class="fas fa-atlas" aria-hidden="true"></i>
                        <i class="fas fa-money-bill-alt" aria-hidden="true"></i>
                    </button>';

                    $btnDetalle = '<button type="button"
                        class="btn-v2-action bg-gradient-success btn-sm tooltipsC detalle"
                        data-id="' . $row->idp . '"
                        title="Detalle de Cuotas"
                        aria-label="Ver detalle de cuotas del préstamo ' . $row->idp . '">
                        <i class="fas fa-atlas" aria-hidden="true"></i>
                    </button>';

                    return $btnPagos . '&nbsp;' . $btnDetalle;
                })
                ->addColumn('estado_badge', function ($row) {
                    if ($row->monto_atrasado > 0) {
                        return '<span class="badge badge-danger">Atrasado</span>';
                    }
                    if ($row->monto_pendiente == 0) {
                        return '<span class="badge badge-success">Pagado</span>';
                    }
                    return '<span class="badge badge-warning">Activo</span>';
                })
                ->rawColumns(['action', 'estado_badge'])
                ->make(true);
        }

        return view('admin.v2.prestamo.index', compact('usuarios', 'clientes'));
    }

    // ────────────────────────────────────────────────────────────────────────
    // MUTACIONES
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Crea un nuevo préstamo y genera sus cuotas.
     * POST /admin/v2/prestamo
     */
    public function guardar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), self::RULES, self::MESSAGES);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        DB::transaction(function () use ($request) {
            Prestamo::create($request->all());

            $prestamoId = Prestamo::latest('idp')->value('idp');

            $this->generateInstallments(
                $prestamoId,
                $request->tipo_pago,
                (int) $request->cuotas,
                $request->fecha_inicial,
                (float) $request->valor_cuota
            );
        });

        return response()->json(['success' => 'ok']);
    }

    /**
     * Anula un préstamo (soft-delete + estado='A').
     * PUT /admin/v2/prestamo/{id}/anular
     */
    public function anularp(int $id): JsonResponse
    {
        if (! request()->ajax()) {
            abort(403);
        }

        DB::table('prestamo')
            ->where('idp', $id)
            ->update([
                'estado'    => 'A',
                'delete_at' => now(),
            ]);

        return response()->json(['success' => 'ok']);
    }

    /**
     * Devuelve datos del préstamo para el formulario de refinanciamiento.
     * GET /admin/v2/prestamo/{id}/refinanciar
     */
    public function refinanciar(int $id): JsonResponse
    {
        if (! request()->ajax()) {
            abort(403);
        }

        $data = DB::table('prestamo')
            ->join('cliente',          'prestamo.cliente_id', '=', 'cliente.id')
            ->join('detalle_prestamo', 'prestamo.idp',        '=', 'detalle_prestamo.prestamo_id')
            ->where('prestamo.idp', $id)
            ->select(
                'cliente.nombres',
                'cliente.apellidos',
                'cliente.id as cliente_id',
                'prestamo.idp',
                'prestamo.monto',
                'prestamo.monto_total',
                'prestamo.monto_pendiente',
                'prestamo.cuotas',
                'prestamo.interes',
                'prestamo.valor_cuota',
                'prestamo.tipo_pago',
                'prestamo.usuario_id',
                'detalle_prestamo.d_numero_cuota',
                'detalle_prestamo.fecha_cuota'
            )
            ->get();

        return response()->json(['result' => $data]);
    }

    /**
     * Refinancia un préstamo: cierra el actual y crea uno nuevo.
     * POST /admin/v2/prestamo/refinanciar
     *
     * Bug fix: la versión original usaba $saldop (variable indefinida).
     *          Aquí se usa correctamente $saldoa->monto_pendiente.
     */
    public function refiguardar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), self::RULES, self::MESSAGES);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        DB::transaction(function () use ($request) {

            // 1. Registrar el pago de cierre
            Pago::create($request->all());

            // 2. Marcar todas las cuotas del préstamo como 'T' (transferido)
            DB::table('detalle_prestamo')
                ->where('prestamo_id', $request->prestamo_id)
                ->update(['estado' => 'T', 'updated_at' => now()]);

            // 3. Registrar el valor abonado en la cuota específica
            DB::table('detalle_prestamo')
                ->where([
                    ['prestamo_id',   '=', $request->prestamo_id],
                    ['d_numero_cuota','=', $request->numero_cuota],
                ])
                ->update(['valor_cuota_pagada' => $request->valor_abono, 'updated_at' => now()]);

            // 4. Cerrar el préstamo original
            //    Bug fix: la versión original usaba $saldop (indefinida).
            //    Obtenemos monto_pendiente del modelo.
            $prestamo = Prestamo::where('idp', $request->prestamo_id)->firstOrFail();

            DB::table('prestamo')
                ->where('idp', $request->prestamo_id)
                ->update([
                    'monto_atrasado'       => 0,
                    'cuotas_atrasadas'     => 0,
                    'monto_pendiente'      => $prestamo->monto_pendiente - $request->valor_abono,
                    'observacion_prestamo' => 'Refinanciado',
                    'estado'               => 'P',
                    'updated_at'           => now(),
                ]);

            // 5. Crear el nuevo préstamo
            Prestamo::create($request->all());

            $nuevoId = Prestamo::latest('idp')->value('idp');

            $this->generateInstallments(
                $nuevoId,
                $request->tipo_pago,
                (int) $request->cuotas,
                $request->fecha_inicial,
                (float) $request->valor_cuota
            );
        });

        return response()->json(['success' => 'ok']);
    }

    // ────────────────────────────────────────────────────────────────────────
    // CONSULTAS AJAX
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Cuotas del préstamo (por prestamo_id=idp).
     * GET /admin/v2/prestamo/{id}/detalle
     */
    public function detallep(int $id): JsonResponse
    {
        if (! request()->ajax()) {
            abort(403);
        }

        $data = DB::table('prestamo')
            ->join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->where('prestamo.idp', $id)
            ->get();

        return response()->json(['result' => $data]);
    }

    /**
     * Datos completos del préstamo para panel de detalle.
     * GET /admin/v2/prestamo/{id}/detalle-completo
     */
    public function detallepn(int $id): JsonResponse
    {
        if (! request()->ajax()) {
            abort(403);
        }

        $data = DB::table('prestamo')
            ->join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
            ->select(
                'cliente.nombres', 'cliente.apellidos',
                'cliente.direccion', 'cliente.celular', 'cliente.id',
                'prestamo.monto', 'prestamo.interes', 'prestamo.monto_total',
                'prestamo.monto_pendiente', 'prestamo.valor_cuota',
                'prestamo.cuotas', 'prestamo.tipo_pago', 'prestamo.idp',
                'prestamo.cuotas_atrasadas', 'prestamo.monto_atrasado',
                'prestamo.fecha_inicial', 'prestamo.created_at'
            )
            ->where('prestamo.idp', $id)
            ->get();

        return response()->json(['result' => $data]);
    }

    /**
     * Cuotas detalladas de un préstamo.
     * GET /admin/v2/prestamo/{id}/cuotas
     */
    public function detalle(int $id): JsonResponse
    {
        if (! request()->ajax()) {
            abort(403);
        }

        $cuotas = DB::table('detalle_prestamo')
            ->where('prestamo_id', $id)
            ->select('d_numero_cuota', 'valor_cuota', 'fecha_cuota', 'valor_cuota_pagada', 'estado')
            ->orderBy('d_numero_cuota')
            ->get();

        return response()->json(['result' => $cuotas]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Genera las cuotas (detalle_prestamo) para un préstamo.
     *
     * Reemplaza los 4 bloques if/elseif idénticos en guardar() y refiguardar()
     * del controlador original (~350 líneas → este método).
     * El batch insert reduce N round-trips a la BD → 1.
     */
    private function generateInstallments(
        int $prestamoId,
        string $tipoPago,
        int $cuotas,
        string $fechaInicial,
        float $valorCuota
    ): void {
        $rows = [];

        for ($i = 0; $i < $cuotas; $i++) {
            $rows[] = [
                'd_numero_cuota' => $i + 1,
                'valor_cuota'    => $valorCuota,
                'fecha_cuota'    => $fechaInicial,
                'estado'         => 'C',
                'activo'         => 1,
                'prestamo_id'    => $prestamoId,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            // Avanzar fecha para la siguiente cuota
            $fechaInicial = $this->advanceDate($fechaInicial, $tipoPago);
        }

        DB::table('detalle_prestamo')->insert($rows);
    }

    /**
     * Calcula la siguiente fecha de cuota según el tipo de pago.
     *
     * Consolida la lógica del controlador original, preservando
     * el comportamiento exacto: bisiesto, diciembre, sábados.
     *
     * Quincenal → +15 días (sin condiciones especiales)
     * Mensual   → +30 días (sin condiciones especiales)
     * Semanal   → +7 días base; +8 o +9 cuando cruza Nav./Año Nuevo
     * Diario    → +1 día base; +2 sábados; +3 viernes/sábado fin de año
     */
    private function advanceDate(string $date, string $tipoPago): string
    {
        $d    = Carbon::createFromFormat('Y-m-d', $date);
        $ts   = strtotime($date);
        $dow  = (int) date('N', $ts); // 1=Lun … 7=Dom; 6=Sáb
        $month= (int) date('m', $ts);
        $doy  = (int) date('z', $ts); // 0-indexed (0=1-Ene)
        $leap = (int) date('L', $ts); // 1 si año bisiesto

        // Quincenal y Mensual: avance simple, sin excepciones
        if ($tipoPago === 'Quincenal') {
            return $d->addDays(15)->toDateString();
        }
        if ($tipoPago === 'Mensual') {
            return $d->addDays(30)->toDateString();
        }

        // ── Semanal ─────────────────────────────────────────────────────────
        if ($tipoPago === 'Semanal') {
            if ($month === 12) {
                // Días de corte de fin de año según si es bisiesto
                // (preserva la lógica original con los mismos índices doy)
                [$c1, $c2] = $leap ? [352, 359] : [351, 358];
                [$b1, $b2] = $leap ? [351, 358] : [350, 357];

                if ($dow === 6 && in_array($doy, [$c1, $c2])) {
                    return $d->addDays(7)->toDateString();
                }
                if (in_array($dow, [5, 1]) && in_array($doy, [$c1, $c2])) {
                    return $d->addDays(8)->toDateString();
                }
                if ($dow === 6 && in_array($doy, [$b1, $b2])) {
                    return $d->addDays(9)->toDateString();
                }
                if (! in_array($dow, [6, 5, 1]) && in_array($doy, [$c1, $c2])) {
                    return $d->addDays(7)->toDateString();
                }
            }

            return $d->addDays(7)->toDateString();
        }

        // ── Diario ──────────────────────────────────────────────────────────
        // (tipoPago === 'Diario' o cualquier otro valor desconocido)
        if ($month === 12) {
            [$c1, $c2] = $leap ? [358, 365] : [357, 364];
            [$b1, $b2] = $leap ? [357, 364] : [356, 363];

            if ($dow === 6 && in_array($doy, [$c1, $c2])) {
                return $d->addDays(2)->toDateString();
            }
            if ($dow === 5 && in_array($doy, [$c1, $c2])) {
                return $d->addDays(3)->toDateString();
            }
            if ($dow === 6 && in_array($doy, [$b1, $b2])) {
                return $d->addDays(3)->toDateString();
            }
            if (! in_array($dow, [6, 5]) && in_array($doy, [$c1, $c2])) {
                return $d->addDays(2)->toDateString();
            }
            if ($dow === 6) {
                return $d->addDays(2)->toDateString();
            }

            return $d->addDays(1)->toDateString();
        }

        // Mes normal: sábado salta al lunes, resto +1
        return $dow === 6
            ? $d->addDays(2)->toDateString()
            : $d->addDays(1)->toDateString();
    }
}
