<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsTrackingTable extends Migration
{
    public function up()
    {
        Schema::create('gps_tracking', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('usuario_id');
            $table->decimal('latitud',  10, 7);
            $table->decimal('longitud', 11, 7);
            $table->float('precision_m')->nullable(); // accuracy in meters
            $table->float('velocidad_kmh')->nullable();
            $table->date('fecha');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['usuario_id', 'fecha']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('gps_tracking');
    }
}
