<?php

namespace App\Http\Controllers\Admin\V2;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * GPS tracking del cobrador.
 *
 * GET  /admin/v2/gps               → vista principal (admin/empresa)
 * POST /admin/v2/gps/registrar     → guardar punto GPS (desde el dispositivo del cobrador)
 * GET  /admin/v2/gps/datos         → JSON puntos de un usuario/fecha
 * GET  /admin/v2/gps/usuarios      → JSON lista de usuarios visibles (para el selector)
 */
class GpsController extends Controller
{
    // ── scope de usuario_ids visibles según rol ───────────────────────────
    private function scopeUsuarioIds(): array
    {
        $uid    = (int) session('usuario_id');
        $rol_id = (int) session('rol_id');

        if ($rol_id === 1) {
            // Administrador: todos
            return DB::table('usuario')->pluck('id')->toArray();
        }

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
     * Vista principal — mapa + resumen.
     * GET /admin/v2/gps
     */
    public function index(Request $request)
    {
        $uids   = $this->scopeUsuarioIds();
        $usuarios = DB::table('usuario')
            ->whereIn('id', $uids)
            ->select('id', 'usuario')
            ->orderBy('usuario')
            ->get();

        return view('admin.v2.gps.index', compact('usuarios'));
    }

    /**
     * Guardar un punto GPS enviado desde el dispositivo del cobrador.
     * POST /admin/v2/gps/registrar
     */
    public function registrar(Request $request): JsonResponse
    {
        $uid = (int) session('usuario_id');
        if (!$uid) {
            return response()->json(['ok' => false, 'msg' => 'Sin sesión'], 401);
        }

        $lat = (float) ($request->latitud  ?? 0);
        $lng = (float) ($request->longitud ?? 0);

        if ($lat === 0.0 && $lng === 0.0) {
            return response()->json(['ok' => false, 'msg' => 'Coordenadas inválidas'], 422);
        }

        // Zona horaria Argentina
        $ahora = Carbon::now('America/Argentina/Buenos_Aires');
        $fecha = $ahora->toDateString();

        DB::table('gps_tracking')->insert([
            'usuario_id'   => $uid,
            'latitud'      => $lat,
            'longitud'     => $lng,
            'precision_m'  => $request->precision  ? (float) $request->precision  : null,
            'velocidad_kmh'=> $request->velocidad   ? (float) $request->velocidad  : null,
            'fecha'        => $fecha,
            'created_at'   => $ahora->toDateTimeString(),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Devuelve los puntos GPS de un usuario en una fecha dada.
     * GET /admin/v2/gps/datos?usuario_id=X&fecha=YYYY-MM-DD
     */
    public function datos(Request $request): JsonResponse
    {
        $uids       = $this->scopeUsuarioIds();
        $usuarioId  = (int) ($request->usuario_id ?? session('usuario_id'));
        $fecha      = $request->fecha ?? Carbon::now('America/Argentina/Buenos_Aires')->toDateString();

        if (!in_array($usuarioId, $uids)) {
            return response()->json(['ok' => false, 'msg' => 'Sin permiso'], 403);
        }

        $puntos = DB::table('gps_tracking')
            ->where('usuario_id', $usuarioId)
            ->where('fecha', $fecha)
            ->orderBy('created_at')
            ->select('latitud', 'longitud', 'precision_m', 'velocidad_kmh', 'created_at')
            ->get();

        // Calcular distancia total con fórmula Haversine
        $distanciaKm = 0.0;
        $prev = null;
        foreach ($puntos as $p) {
            if ($prev !== null) {
                $distanciaKm += $this->haversine(
                    (float) $prev->latitud,  (float) $prev->longitud,
                    (float) $p->latitud,     (float) $p->longitud
                );
            }
            $prev = $p;
        }

        return response()->json([
            'ok'           => true,
            'puntos'       => $puntos,
            'distancia_km' => round($distanciaKm, 2),
        ]);
    }

    /**
     * Lista de usuarios visibles para el selector del formulario.
     * GET /admin/v2/gps/usuarios
     */
    public function usuarios(): JsonResponse
    {
        $uids = $this->scopeUsuarioIds();
        $lista = DB::table('usuario')
            ->whereIn('id', $uids)
            ->select('id', 'usuario')
            ->orderBy('usuario')
            ->get();

        return response()->json(['result' => $lista]);
    }

    // ── Haversine: distancia en km entre dos coordenadas ─────────────────
    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $r    = 6371.0; // radio tierra km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $r * 2 * asin(sqrt($a));
    }
}
