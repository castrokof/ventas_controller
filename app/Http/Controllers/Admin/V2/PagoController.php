<?php

// app/Http/Controllers/Admin/V2/PagoController.php

namespace App\Http\Controllers\Admin\V2;

use App\Http\Controllers\PagoCalenderController;
use Illuminate\Http\Request;

/**
 * V2 PagoController
 *
 * Delega toda la lógica al PagoCalenderController existente y sólo
 * sobreescribe la vista devuelta por index, apuntando a v2/pago_card.
 * Los endpoints AJAX (guardar, editar, actualizar…) siguen funcionando
 * a través de la ruta heredada del controlador original.
 */
class PagoController extends PagoCalenderController
{
    /**
     * Muestra la vista V2 del módulo pago_card.
     * Ruta: GET /admin/v2/pago-card
     */
    public function index(Request $request)
    {
        $usuario_id  = $request->session()->get('usuario_id');

        $clientes    = \App\Models\Admin\Cliente::where('usuario_id', $usuario_id)->get();
        $usuarioscp  = \App\Models\Seguridad\Usuario::orderBy('id')
                            ->where('id', $usuario_id)
                            ->pluck('usuario', 'id')
                            ->toArray();

        // Aprovecha los mismos datos que el controlador original,
        // pero renderiza la vista V2.
        return view('admin.v2.pago_card.index', compact('clientes', 'usuarioscp'));
    }
}
