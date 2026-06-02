<?php

namespace App\Http\Middleware;

use Closure;

class SoloEmpresaAdmin
{
    public function handle($request, Closure $next)
    {
        $rol = session('rol_nombre');
        if ($rol === 'administrador' || $rol === 'empresa') {
            return $next($request);
        }
        return redirect('/tablero')->with('mensaje', 'No tiene permiso para acceder a esta sección.');
    }
}
