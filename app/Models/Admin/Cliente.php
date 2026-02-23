<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $fillable = ['nombres', 'apellidos', 'tipo_documento', 'documento', 'telefono', 'celular', 'direccion', 'estado', 'pais', 'ciudad', 'barrio', 'sector', 'consecutivo', 'activo', 'observacion_cli', 'usuario_id'];


    public function prestamo(){
        return $this->hasMany(Prestamo::class, 'cliente_id');
    }

}
