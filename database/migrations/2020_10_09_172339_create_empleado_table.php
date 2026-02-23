<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpleadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empleado', function (Blueprint $table) {
            $table->bigIncrements('ide');   
            $table->string('nombres',100);
            $table->string('apellidos',100)->nullable();
            $table->string('tipo_documento',50)->nullable();
            $table->integer('documento')->nullable();
            $table->string('pais',50)->nullable();
            $table->string('ciudad',50)->nullable();
            $table->string('barrio',50)->nullable();
            $table->string('direccion',50)->nullable();
            $table->string('celular',50)->nullable();
            $table->string('telefono',50);
            $table->char('activo',1);
            $table->unsignedBigInteger('empresa_id');
            $table->foreign('empresa_id', 'fk_empresaid_empleadoid')->references('id')->on('empresa')->onDelete('restrict')->onUpdate('restrict');
            $table->integer('base')->nullable();
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
        Schema::dropIfExists('empleado');
    }
}
