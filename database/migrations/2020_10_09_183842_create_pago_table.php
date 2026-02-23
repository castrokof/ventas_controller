<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('valor_cuota',15,2);
            $table->char('abono',1);
            $table->decimal('valor_abono',15,2);
            $table->integer('numero_cuota');
            $table->date('fecha_pago');
            $table->string('observacion_pago',100)->nullable();
            $table->char('sync',1);
            $table->unsignedBigInteger('prestamo_id');
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id', 'fk_pagoid_usuarioid')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('prestamo_id', 'fk_pagoid_prestamoid')->references('idp')->on('prestamo')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('pago');
    }
}
