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

        if ($request->ajax() || $request->has('draw')) {
            $datas = DB::table('prestamo')
                ->join('cliente', 'prestamo.cliente_id', '=', 'cliente.id')
                ->where('prestamo.usuario_id', $usuario_id)
                ->where('prestamo.monto_pendiente', '>', 0)
                ->whereNull('prestamo.delete_at')
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

                    $btnAnular = '<button type="button"
                        class="btn-v2-action bg-gradient-danger btn-sm tooltipsC anularp"
                        data-id="' . $row->idp . '"
                        title="Anular préstamo"
                        aria-label="Anular préstamo ' . $row->idp . '">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>';

                    $btnRefi = '<button type="button"
                        class="btn-v2-action bg-gradient-info btn-sm tooltipsC refinanciar"
                        data-id="' . $row->idp . '"
                        title="Refinanciar"
                        aria-label="Refinanciar préstamo ' . $row->idp . '">
                        <i class="fas fa-sync-alt" aria-hidden="true"></i>
                    </button>';

                    return $btnPagos . '&nbsp;' . $btnDetalle . '&nbsp;' . $btnRefi . '&nbsp;' . $btnAnular;
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

        $incluirDomingo = (bool) $request->input('incluir_domingo', 0);
        $incluirFestivo = (bool) $request->input('incluir_festivo', 0);

        DB::transaction(function () use ($request, $incluirDomingo, $incluirFestivo) {
            Prestamo::create($request->all());

            $prestamoId = Prestamo::latest('idp')->value('idp');

            $this->generateInstallments(
                $prestamoId,
                $request->tipo_pago,
                (int) $request->cuotas,
                $request->fecha_inicial,
                (float) $request->valor_cuota,
                $incluirDomingo,
                $incluirFestivo
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

        $incluirDomingo = (bool) $request->input('incluir_domingo', 0);
        $incluirFestivo = (bool) $request->input('incluir_festivo', 0);

        DB::transaction(function () use ($request, $incluirDomingo, $incluirFestivo) {

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
                (float) $request->valor_cuota,
                $incluirDomingo,
                $incluirFestivo
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
        float $valorCuota,
        bool $incluirDomingo = true,
        bool $incluirFestivo = true
    ): void {
        $rows = [];

        // Ajustar la fecha inicial si cae en domingo o festivo excluido
        $fechaInicial = $this->skipToValidDate($fechaInicial, $incluirDomingo, $incluirFestivo);

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

            // Avanzar fecha y aplicar ajuste de domingo/festivo
            $fechaInicial = $this->advanceDate($fechaInicial, $tipoPago);
            $fechaInicial = $this->skipToValidDate($fechaInicial, $incluirDomingo, $incluirFestivo);
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

    /**
     * Avanza la fecha hasta que no caiga en domingo (si excluido) ni en
     * feriado argentino (si excluido). Si ambos están permitidos, no hace nada.
     */
    private function skipToValidDate(string $date, bool $incluirDomingo, bool $incluirFestivo): string
    {
        if ($incluirDomingo && $incluirFestivo) {
            return $date;
        }

        $d = Carbon::createFromFormat('Y-m-d', $date);

        $changed = true;
        while ($changed) {
            $changed = false;

            if (! $incluirDomingo && $d->dayOfWeek === Carbon::SUNDAY) {
                $d->addDay();
                $changed = true;
                continue;
            }

            if (! $incluirFestivo && $this->isFestivoArgentina($d)) {
                $d->addDay();
                $changed = true;
            }
        }

        return $d->toDateString();
    }

    /**
     * Indica si una fecha es feriado nacional en Argentina.
     *
     * Fuente: Ley 27.399 y Decreto 1584/2010.
     *  1. Feriados fijos (no se trasladan).
     *  2. Feriados trasladables: Mar/Mié → lunes anterior; Jue/Vie/Sáb → lunes siguiente.
     *  3. Carnaval (Lunes y Martes, 48 y 47 días antes de Pascua).
     *  4. Viernes Santo (2 días antes de Pascua).
     */
    private function isFestivoArgentina(Carbon $date): bool
    {
        $year  = $date->year;
        $month = $date->month;
        $day   = $date->day;

        // ── 1. Feriados fijos ────────────────────────────────────────────────
        $fijos = [
            [1,  1],   // Año Nuevo
            [3,  24],  // Día de la Memoria por la Verdad y la Justicia
            [4,  2],   // Día del Veterano y los Caídos en Malvinas
            [5,  1],   // Día del Trabajador
            [5,  25],  // Revolución de Mayo
            [7,  9],   // Día de la Independencia
            [12, 8],   // Inmaculada Concepción de María
            [12, 25],  // Navidad
        ];

        foreach ($fijos as [$m, $d]) {
            if ($month === $m && $day === $d) {
                return true;
            }
        }

        // ── 2. Feriados trasladables (Decreto 1584/2010) ─────────────────────
        //    Mar/Mié → lunes anterior | Jue/Vie/Sáb → lunes siguiente
        $trasladables = [
            [6,  20],  // Paso a la Inmortalidad del Gral. Manuel Belgrano
            [8,  17],  // Paso a la Inmortalidad del Gral. José de San Martín
            [10, 12],  // Día del Respeto a la Diversidad Cultural
            [11, 20],  // Día de la Soberanía Nacional
        ];

        foreach ($trasladables as [$m, $d]) {
            $base = Carbon::create($year, $m, $d);
            $dow  = $base->dayOfWeek;
            if ($dow === Carbon::TUESDAY || $dow === Carbon::WEDNESDAY) {
                $festivo = $base->copy()->previous(Carbon::MONDAY);
            } elseif ($dow === Carbon::THURSDAY || $dow === Carbon::FRIDAY || $dow === Carbon::SATURDAY) {
                $festivo = $base->copy()->next(Carbon::MONDAY);
            } else {
                $festivo = $base->copy();
            }
            if ($date->isSameDay($festivo)) {
                return true;
            }
        }

        // ── 3 & 4. Carnaval y Semana Santa (basados en Pascua) ──────────────
        $easter = Carbon::create($year, 3, 21)->addDays(easter_days($year));

        // Carnaval: Lunes y Martes previos (48 y 47 días antes de Pascua)
        if ($date->isSameDay($easter->copy()->subDays(48))) return true;
        if ($date->isSameDay($easter->copy()->subDays(47))) return true;

        // Viernes Santo
        if ($date->isSameDay($easter->copy()->subDays(2))) return true;

        return false;
    }
}
