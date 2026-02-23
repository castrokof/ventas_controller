<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetallePrestamoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_prestamo', function (Blueprint $table) {
            $table->bigIncrements('idd');
            $table->integer('d_numero_cuota');
            $table->decimal('valor_cuota',15,2);
            $table->decimal('valor_cuota_pagada',15,2)->nullable();
            $table->date('fecha_cuota');
            $table->char('estado',1);
            $table->char('activo',1);
            $table->unsignedBigInteger('prestamo_id');
            $table->foreign('prestamo_id', 'fk_prestamoid_detalleprestamoid')->references('idp')->on('prestamo')->onDelete('restrict')->onUpdate('restrict');
            $table->dateTime('delete_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_prestamo');
    }
}
