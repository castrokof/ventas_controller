<?php

namespace App\Http\Controllers\Admin\V2;

use App\Http\Controllers\Controller;
use App\Models\Admin\Gasto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * V2 GastoController — standalone, sin extends de la versión anterior.
 *
 * Mejoras respecto al original:
 *  - Sin return view() muerto dentro del bloque ajax
 *  - Respuestas JSON estandarizadas
 *  - Renderiza vistas admin.v2.gasto.*
 */
class GastoController extends Controller
{
    private function rules(): array
    {
        return [
            'monto'       => 'numeric|required|min:1|max:9999999999',
            'descripcion' => 'required|max:150',
        ];
    }

    /**
     * index — lista de gastos con DataTable AJAX (filtrada por rol).
     */
    public function index(Request $request)
    {
        $rol_id     = $request->session()->get('rol_id');
        $usuario_id = $request->session()->get('usuario_id');

        if ($request->ajax()) {
            $query = DB::table('gasto')
                ->join('usuario', 'gasto.usuario_id', '=', 'usuario.id')
                ->select(
                    'gasto.idg as id',
                    'gasto.monto',
                    'gasto.descripcion',
                    'gasto.usuario_id',
                    'gasto.created_at',
                    'gasto.updated_at'
                );

            if ($rol_id != 1) {
                $query->where('gasto.usuario_id', $usuario_id);
            }

            $datas = $query->orderBy('gasto.idg')->get();

            return DataTables()->of($datas)
                ->addColumn('action', function ($row) {
                    return '<button type="button" name="edit" id="' . $row->id . '"
                        class="edit btn-v2-action btn btn-primary btn-sm tooltipsC"
                        title="Editar gasto">
                        <i class="far fa-edit mr-1" aria-hidden="true"></i>Editar
                    </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.v2.gasto.index', compact('usuario_id'));
    }

    /**
     * guardar — crea un nuevo gasto.
     */
    public function guardar(Request $request): JsonResponse
    {
        $error = Validator::make($request->all(), $this->rules());

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        Gasto::create($request->all());

        return response()->json(['success' => 'ok']);
    }

    /**
     * editar — devuelve datos del gasto para edición (AJAX).
     */
    public function editar(int $id): JsonResponse
    {
        $data = Gasto::where('idg', '=', $id)->firstOrFail();

        return response()->json(['result' => $data]);
    }

    /**
     * actualizar — actualiza un gasto existente.
     */
    public function actualizar(Request $request, int $id): JsonResponse
    {
        $error = Validator::make($request->all(), $this->rules());

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        Gasto::where('idg', '=', $id)->update([
            'monto'       => $request->monto,
            'descripcion' => $request->descripcion,
            'updated_at'  => now(),
        ]);

        return response()->json(['success' => 'ok1']);
    }
}
