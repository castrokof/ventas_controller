<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';
    protected $fillable = ['valor_cuota', 'abono', 'valor_abono', 'numero_cuota', 'fecha_pago','observacion_pago','sync', 'prestamo_id', 'usuario_id'];
}
