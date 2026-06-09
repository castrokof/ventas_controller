<?php

namespace App\Http\Controllers\Admin\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FestivoController extends Controller
{
    public function index()
    {
        $festivos = DB::table('festivos_extra')
            ->orderBy('fecha')
            ->get();

        return view('admin.v2.festivos.index', compact('festivos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha'       => 'required|date|unique:festivos_extra,fecha',
            'descripcion' => 'nullable|string|max:120',
        ], [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date'     => 'Formato de fecha inválido.',
            'fecha.unique'   => 'Esa fecha ya está registrada como festivo.',
        ]);

        $id = DB::table('festivos_extra')->insertGetId([
            'fecha'       => $request->fecha,
            'descripcion' => $request->descripcion,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['ok' => true, 'id' => $id]);
    }

    public function destroy($id)
    {
        DB::table('festivos_extra')->where('id', $id)->delete();
        return response()->json(['ok' => true]);
    }
}
