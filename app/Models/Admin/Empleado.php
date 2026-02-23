<?php

namespace App\Models\Admin;

use App\Models\Seguridad\Usuario;
use App\Models\Admin\Empresa;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleado';
    protected $primary_key = 'ide';
    
    protected $fillable = ['nombres', 'apellidos', 'tipo_documento', 'documento','pais', 'ciudad', 'barrio', 'direccion', 'celular', 'telefono', 'activo', 'empresa_id'];

    public function usuarios(){
        return $this->hasOne(Usuario::class, 'empleado_id');
    }

    public function empresas(){
        return $this->belongsTo(Empresa::class, 'id');
    }


}