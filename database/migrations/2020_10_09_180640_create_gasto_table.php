<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGastoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gasto', function (Blueprint $table) {
            $table->bigIncrements('idg');
            $table->integer('monto');
            $table->string('descripcion', 150);
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id', 'fk_gastoid_usuarioid')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('gasto');
    }
}
