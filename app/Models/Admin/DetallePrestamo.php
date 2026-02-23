<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DetallePrestamo extends Model
{
    protected $table = 'detalle_prestamo';
    protected $primary_key = 'idd';
    protected $fillable = ['d_numero_cuota', 'valor_cuota', 'valor_cuota_pagada', 'fecha_cuota', 'estado', 'activo', 'prestamo_id', 'delete_at'];

    public function prestamo(){
        return $this->belongsTo(Prestamo::class, 'idp');
    }

}
