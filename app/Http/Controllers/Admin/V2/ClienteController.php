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
     * index — lista de clientes con DataTable AJAX.
     */
    public function index(Request $request)
    {
        $id_usuario = Session()->get('usuario_id');

        $usuarios = Usuario::orderBy('id')
            ->where('id', '=', $id_usuario)
            ->pluck('usuario', 'id')
            ->toArray();

        $datas = collect(); // evita variable indefinida en la vista

        if ($request->ajax()) {
            $datas = Cliente::where('usuario_id', '=', $id_usuario)
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

                    return $edit . $prestamo . $detalle;
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
        $error = Validator::make($request->all(), $this->rules());

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        Cliente::create($request->all());

        return response()->json(['success' => 'ok']);
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
            $data = Cliente::findOrFail($id);
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

        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());

        return response()->json(['success' => 'ok1']);
    }

    /**
     * detalle — devuelve préstamos del cliente (AJAX).
     */
    public function detalle(int $id): JsonResponse
    {
        $result = Prestamo::where('cliente_id', '=', $id)->get();

        return response()->json(['result' => $result]);
    }
}
