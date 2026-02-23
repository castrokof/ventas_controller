<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
return $request->user();
});

Route::post('medidoresout','Admin\OrdenesmtlasignarController@medidorall');
Route::post('medidores','Admin\OrdenEjecutadaController@medidorejecutado');
Route::post('marcas','Admin\MarcasController@marcasall');
Route::post('loginMovil1','Seguridad\LoginController@loginMovil');
Route::post('cliente_list','ClienteController@index_cli_app');
Route::post('cliente_update','ClienteController@actualizar_cli_app');
Route::post('cliente_create','ClienteController@guardar_cli_app');


Route::post('pagos_list','PagoController@indexPagosApp');
Route::post('prestamos_list','PrestamoController@indexPrestamoApp');
Route::post('prestamos_detalle','PrestamoController@DetallePrestamoApp');


Route::post('pagos_pay','PagoCalenderController@PagosPayApp');
Route::post('pagos_payxp','PagoCalenderController@PagosPayAppXpApp');








