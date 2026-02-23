<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('usuario',50)->unique();
            $table->string('password',100);
            $table->string('remenber_token',100);
            $table->string('tipo_de_usuario',50);
            $table->string('email',100)->unique();
            $table->char('activo',1);
            $table->unsignedBigInteger('empleado_id');
            $table->foreign('empleado_id', 'fk_empleadoid_usuarioid')->references('ide')->on('empleado')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('usuario');
    }
}
