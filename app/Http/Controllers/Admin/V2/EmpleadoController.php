<?php

namespace App\Http\Controllers\Admin\V2;

use App\Http\Controllers\Controller;
use App\Models\Admin\Empleado;
use App\Models\Admin\Empresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * V2 EmpleadoController — standalone, sin extends de la versión anterior.
 *
 * Mejoras respecto al original:
 *  - Inicializa $datas antes del bloque ajax (evita "variable undefined" en vistas)
 *  - Usa Eloquent / DB uniforme según el método
 *  - Respuestas JSON estandarizadas con clave 'success' / 'errors'
 *  - Renderiza vistas admin.v2.empleado.*
 */
class EmpleadoController extends Controller
{
    // ── Reglas de validación compartidas ──────────────────────────────────
    private function rules(): array
    {
        return [
            'nombres'        => 'required|max:100',
            'apellidos'      => 'required|max:100',
            'documento'      => 'numeric|required|min:10000|max:9999999999',
            'tipo_documento' => 'required',
            'empresa_id'     => 'required',
            'ciudad'         => 'required',
            'pais'           => 'required',
            'barrio'         => 'required',
            'direccion'      => 'required',
            'activo'         => 'required',
        ];
    }

    /**
     * index — lista de empleados con DataTable AJAX (filtrada por rol).
     */
    public function index(Request $request)
    {
        $rol_id = $request->session()->get('rol_id');

        // Empresas disponibles para el select del formulario
        $empresa = Empresa::orderBy('id')->pluck('id', 'nombre')->toArray();

        if ($request->ajax()) {

            if ($rol_id == 1) {
                // Superadmin: ve todos los empleados
                $datas = DB::table('empleado')
                    ->join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
                    ->select('empleado.*', 'empresa.nombre as empresa_nombre')
                    ->orderBy('empleado.ide')
                    ->get();
            } else {
                // Empleado: sólo ve los de su misma empresa
                $empleado_id = $request->session()->get('empleado_id');

                $emp = DB::table('empleado')
                    ->join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
                    ->where('empleado.ide', '=', $empleado_id)
                    ->select('empleado.empresa_id')
                    ->first();

                $empresa_id = $emp->empresa_id ?? 0;

                $datas = DB::table('empleado')
                    ->join('empresa', 'empleado.empresa_id', '=', 'empresa.id')
                    ->where('empleado.empresa_id', '=', $empresa_id)
                    ->select('empleado.*', 'empresa.nombre as empresa_nombre')
                    ->orderBy('empleado.ide')
                    ->get();
            }

            return DataTables()->of($datas)
                ->addColumn('action', function ($row) {
                    $edit = '<button type="button" name="edit" id="' . $row->ide . '"
                        class="edit btn-v2-action btn btn-primary btn-sm tooltipsC"
                        title="Editar empleado">
                        <i class="far fa-edit mr-1" aria-hidden="true"></i>Editar
                    </button>';

                    $clientes = '&nbsp;<button type="button" id="' . $row->ide . '"
                        class="clientes btn-v2-action btn bg-gradient-warning btn-sm tooltipsC"
                        title="Ver clientes de este empleado">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </button>';

                    return $edit . $clientes;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.v2.empleado.index', compact('empresa'));
    }

    /**
     * guardar — crea un nuevo empleado.
     */
    public function guardar(Request $request): JsonResponse
    {
        $error = Validator::make($request->all(), $this->rules());

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        Empleado::create($request->all());

        return response()->json(['success' => 'ok']);
    }

    /**
     * editar — devuelve datos del empleado para edición (AJAX).
     */
    public function editar(int $id)
    {
        $empresa = Empresa::orderBy('id')->pluck('id', 'nombre')->toArray();

        if (request()->ajax()) {
            $data = Empleado::where('ide', '=', $id)->firstOrFail();
            return response()->json(['result' => $data]);
        }

        return view('admin.v2.empleado.index', compact('empresa'));
    }

    /**
     * actualizar — actualiza un empleado existente.
     */
    public function actualizar(Request $request, int $id): JsonResponse
    {
        $error = Validator::make($request->all(), $this->rules());

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        Empleado::where('ide', '=', $id)->update([
            'nombres'        => $request->nombres,
            'apellidos'      => $request->apellidos,
            'tipo_documento' => $request->tipo_documento,
            'documento'      => $request->documento,
            'pais'           => $request->pais,
            'ciudad'         => $request->ciudad,
            'barrio'         => $request->barrio,
            'direccion'      => $request->direccion,
            'celular'        => $request->celular,
            'telefono'       => $request->telefono,
            'activo'         => $request->activo,
            'empresa_id'     => $request->empresa_id,
            'updated_at'     => now(),
        ]);

        return response()->json(['success' => 'ok1']);
    }
}
