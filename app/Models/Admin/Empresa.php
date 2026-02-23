<?php

namespace App\Models\Admin;
use App\Models\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';
    protected $fillable = ['nombre', 'tipo_documento', 'documento', 'activo'];

    public function empleado(){
        return $this->hasMany(Empleado::class, 'empresa_id');
    }

    public function usuarios(){
        return $this->hasManyThrough(
            Usuario::class,
            Empleado::class,
            'empresa_id',   
            'empleado_id',   
            'id',
            'ide'
           
        );
    }

    
}
