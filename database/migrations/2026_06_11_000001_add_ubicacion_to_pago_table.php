<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUbicacionToPagoTable extends Migration
{
    public function up()
    {
        Schema::table('pago', function (Blueprint $table) {
            $table->decimal('latitud', 10, 7)->nullable()->after('observacion_pago');
            $table->decimal('longitud', 11, 7)->nullable()->after('latitud');
        });
    }

    public function down()
    {
        Schema::table('pago', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud']);
        });
    }
}
