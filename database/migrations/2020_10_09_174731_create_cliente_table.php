<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cliente', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombres',100);
            $table->string('apellidos',100);
            $table->string('tipo_documento',50);
            $table->integer('documento');
            $table->string('telefono',50)->nullable();
            $table->string('celular',50);
            $table->string('direccion',150);
            $table->string('estado',50);
            $table->string('pais',50)->nullable();
            $table->string('ciudad',50)->nullable();
            $table->string('barrio',50)->nullable();
            $table->string('sector',50)->nullable();
            $table->integer('consecutivo');
            $table->char('activo',1);
            $table->string('observacion_cli',200)->nullable();
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id', 'fk_clienteid_usuarioid')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('cliente');
    }
}
