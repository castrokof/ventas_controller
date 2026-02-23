<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
       
    protected $table = 'gasto';
    protected $fillable = ['monto', 'descripcion', 'usuario_id', 'delete_at'];

    public function usuarios(){
        return $this->hasMany(Usuario::class, 'id');
    }


}
