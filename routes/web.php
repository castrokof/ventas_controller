<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if(version_compare(PHP_VERSION, '7.2.0', '>=')) { error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING); }




/* RUTAS IMAGENES TEXTO */

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
//Route::get('/', 'Admin\InicioController@index')->name('inicio');
Route::get('/', 'Seguridad\LoginController@index')->name('inicio');
Route::get('seguridad/login', 'Seguridad\LoginController@index')->name('login');
Route::post('seguridad/login', 'Seguridad\LoginController@login')->name('login_post');
Route::get('seguridad/logout', 'Seguridad\LoginController@logout')->name('logout');
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'superadmin']], function () {
     
     
     /* RUTAS DEL MENU */
     Route::get('menu', 'MenuController@index')->name('menu');
     Route::get('menu/crear', 'MenuController@crear')->name('crear_menu');
     Route::get('menu/{id}/editar', 'MenuController@editar')->name('editar_menu');
     Route::put('menu/{id}', 'MenuController@actualizar')->name('actualizar_menu');
     Route::post('menu', 'MenuController@guardar')->name('guardar_menu');
     Route::post('menu/guardar-orden', 'MenuController@guardarOrden')->name('guardar_orden');
     Route::get('rol/{id}/elimniar', 'MenuController@eliminar')->name('eliminar_menu');
    
     /* RUTAS DEL ROL */
     Route::get('rol', 'RolController@index')->name('rol');
     Route::get('rol/crear', 'RolController@crear')->name('crear_rol');
     Route::post('rol', 'RolController@guardar')->name('guardar_rol');
     Route::get('rol/{id}/editar', 'RolController@editar')->name('editar_rol');
     Route::put('rol/{id}', 'RolController@actualizar')->name('actualizar_rol');
     Route::delete('rol/{id}', 'RolController@eliminar')->name('eliminar_rol');
    
     /* RUTAS DEL MENUROL */
     Route::get('menu-rol', 'MenuRolController@index')->name('menu_rol');
     Route::post('menu-rol', 'MenuRolController@guardar')->name('guardar_menu_rol');
     
     /* RUTAS DE LA EMPRESA */
     Route::get('empresa', 'EmpresaController@index')->name('empresa');
     Route::get('empresa/crear', 'EmpresaController@crear')->name('crear_empresa');
     Route::post('empresa', 'EmpresaController@guardar')->name('guardar_empresa');
     Route::get('empresa/{id}/editar', 'EmpresaController@editar')->name('editar_empresa');
     Route::put('empresa/{id}', 'EmpresaController@actualizar')->name('actualizar_empresa');

     /* RUTAS DEL PERMISO */
     Route::get('permiso', 'PermisoController@index')->name('permiso');
     Route::get('permiso/crear', 'PermisoController@crear')->name('crear_permiso');
     Route::post('permiso', 'PermisoController@guardar')->name('guardar_permiso');
     Route::get('permiso/{id}/editar', 'PermisoController@editar')->name('editar_permiso');
     Route::put('permiso/{id}', 'PermisoController@actualizar')->name('actualizar_permiso');
     Route::delete('permiso/{id}', 'PermisoController@eliminar')->name('eliminar_permiso');
     
     /* RUTAS DEL PERMISOROL */
     Route::get('permiso-rol', 'PermisoRolController@index')->name('permiso_rol');
     Route::post('permiso-rol', 'PermisoRolController@guardar')->name('guardar_permiso_rol');


   
});


Route::group(['middleware' => ['auth']], function () {

Route::get('/tablero', 'AdminController@index')->name('tablero');

Route::get('informes', 'AdminController@informes')->name('informes')->middleware('superEditor');
Route::get('pagod', 'AdminController@informesp')->name('informesp')->middleware('superEditor');
Route::get('prestamod', 'AdminController@informespo')->name('informespo')->middleware('superConsultor');
Route::get('gastod', 'AdminController@informesg')->name('informesg')->middleware('superConsultor');


/* RUTAS DEL USUARIO */
Route::get('usuario', 'UsuarioController@index')->name('usuario')->middleware('superEditor');
Route::get('usuario/crear', 'UsuarioController@crear')->name('crear_usuario')->middleware('superEditor');
Route::post('usuario', 'UsuarioController@guardar')->name('guardar_usuario')->middleware('superEditor');
Route::get('usuario/{id}/editar', 'UsuarioController@editar')->name('editar_usuario')->middleware('superEditor');
Route::get('usuario/{id}/password', 'UsuarioController@editarpassword')->name('editar_password')->middleware('superEditor');
Route::put('usuario/{id}', 'UsuarioController@actualizar')->name('actualizar_usuario')->middleware('superEditor');
Route::put('password/{id}', 'UsuarioController@actualizarpassword')->name('actualizar_password')->middleware('superEditor');


/* RUTAS DEL EMPLEADO */
Route::get('empleado', 'EmpleadoController@index')->name('empleado')->middleware('superEditor');
Route::get('empleado/crear', 'EmpleadoController@crear')->name('crear_empleado')->middleware('superEditor');
Route::post('empleado', 'EmpleadoController@guardar')->name('guardar_empleado')->middleware('superEditor');
Route::get('empleado/{id}/editar', 'EmpleadoController@editar')->name('editar_empleado')->middleware('superEditor');
Route::put('empleado/{id}', 'EmpleadoController@actualizar')->name('actualizar_empleado')->middleware('superEditor');

/* RUTAS DEL CLIENTE */
Route::get('clientes', 'ClienteController@index')->name('cliente')->middleware('superConsultor');
Route::get('cliente', 'ClienteController@indexcli')->name('clientecli')->middleware('superConsultor');
Route::get('clientes/{id}', 'ClienteController@indexCliente')->name('cliente_usuario')->middleware('superConsultor');

Route::get('cliente/crear', 'ClienteController@crear')->name('crear_cliente')->middleware('superConsultor');
Route::post('cliente', 'ClienteController@guardar')->name('guardar_cliente')->middleware('superConsultor');
Route::get('cliente/{id}/editar', 'ClienteController@editar')->name('editar_cliente')->middleware('superConsultor');
Route::put('cliente/{id}', 'ClienteController@actualizar')->name('actualizar_cliente')->middleware('superConsultor');


Route::get('clientes_card', 'ClienteController@index_card')->name('cliente_card')->middleware('superConsultor');


/* RUTAS DEL ORDEN CLIENTE */
Route::get('cliente/ruta', 'ClienteController@ruta')->name('cliente-ruta')->middleware('superConsultor');
Route::post('cliente/ruta', 'ClienteController@rutaGuardar')->name('guardar-ruta')->middleware('superConsultor');

/* RUTAS DEL PRESTAMO */
Route::get('prestamo', 'PrestamoController@index')->name('prestamo')->middleware('superConsultor');
Route::get('prestamo/crear', 'PrestamoController@crear')->name('crear_prestamo')->middleware('superConsultor');
Route::post('prestamo', 'PrestamoController@guardar')->name('guardar_prestamo')->middleware('superConsultor');
Route::get('prestamo/{id}/editar', 'PrestamoController@editar')->name('editar_prestamo')->middleware('superConsultor');
Route::get('prestamo/{id}', 'PrestamoController@detalle')->name('detalle_prestamo')->middleware('superConsultor');
Route::get('prestamop/{id}', 'PrestamoController@detallep')->name('detalle_prestamop')->middleware('superConsultor');
Route::put('prestamo/{id}', 'PrestamoController@actualizar')->name('actualizar_prestamo')->middleware('superConsultor');
Route::put('anularp/{id}', 'PrestamoController@anularp')->name('anular_prestamo')->middleware('superConsultor');
Route::post('prestamorefi', 'PrestamoController@refiguardar')->name('guardar_prestamorefi')->middleware('superConsultor');



Route::post('detalle_prestamo', 'DetallePrestamoController@update')->name('actualizar_cuota_fecha')->middleware('superConsultor');
Route::get('prestamopn/{id}', 'PrestamoController@detallepn')->name('detalle_prestamopn')->middleware('superConsultor');

Route::get('refinanciar/{id}/prestamo', 'PrestamoController@refinanciar')->name('refinanciar_prestamo')->middleware('superConsultor');

/* RUTAS DEL PAGO */
Route::get('pago/{id}', 'PagoController@detalle')->name('detalle_pago')->middleware('superConsultor');
Route::get('pago', 'PagoController@index')->name('pago')->middleware('superConsultor');
Route::get('pago/crear', 'PagoController@crear')->name('crear_pago')->middleware('superConsultor');
Route::post('pago', 'PagoController@guardar')->name('guardar_pago')->middleware('superConsultor');
Route::get('pago/{id}/editar', 'PagoController@editar')->name('editar_pago')->middleware('superConsultor');
Route::get('pago/{id}/editpay', 'PagoController@editpay')->name('editpay_pago')->middleware('superConsultor');
Route::put('pago/{id}', 'PagoController@actualizar')->name('actualizar_pago')->middleware('superConsultor');



/* RUTAS DEL PAGO */
Route::get('pagoc/{id}', 'PagoCalenderController@detalle')->name('detalle_pagoc')->middleware('superConsultor');
Route::get('pagoc', 'PagoCalenderController@index')->name('pagoc')->middleware('superConsultor');
Route::get('pagoc/crear', 'PagoCalenderControllerr@crear')->name('crear_pagoc')->middleware('superConsultor');
Route::post('pagoc', 'PagoCalenderController@guardar')->name('guardar_pagoc')->middleware('superConsultor');
Route::get('pagoc/{id}/editar', 'PagoCalenderController@editar')->name('editar_pagoc')->middleware('superConsultor');
Route::get('pagoc/{id}/editpay', 'PagoCalenderController@editpay')->name('editpay_pagoc')->middleware('superConsultor');
Route::put('pagoc/{id}', 'PagoCalenderController@actualizar')->name('actualizar_pagoc')->middleware('superConsultor');
Route::get('pagoca', 'PagoCalenderController@indexAdelanto')->name('pagoa')->middleware('superConsultor');
Route::get('pagocap', 'PagoCalenderController@indexAtrasosp')->name('atrasosp')->middleware('superConsultor');
Route::get('pagocr', 'PagoCalenderController@indexRegistrados')->name('pagosrs')->middleware('superConsultor');
Route::get('pagocnow', 'PagoCalenderController@indexPagonow')->name('pagonow')->middleware('superConsultor');

Route::get('pagocp/{id}/editarp', 'PagoCalenderController@editarp')->name('editar_pagocp')->middleware('superConsultor');

Route::get('pagocc', 'PagoCalenderController@indexc')->name('pagocc')->middleware('superConsultor');

Route::get('pagoccp', 'PagoCalenderController@indexcp')->name('pagoccp')->middleware('superConsultor');

/* RUTAS DEL GASTO */
Route::get('gasto', 'GastoController@index')->name('gasto')->middleware('superConsultor');
Route::get('gasto/crear', 'GastoController@crear')->name('crear_gasto')->middleware('superConsultor');
Route::post('gasto', 'GastoController@guardar')->name('guardar_gasto')->middleware('superConsultor');
Route::get('gasto/{id}/editar', 'GastoController@editar')->name('editar_gasto')->middleware('superConsultor');
Route::put('gasto/{id}', 'GastoController@actualizar')->name('actualizar_gasto')->middleware('superConsultor');



/* RUTAS DEL USUARIO NO ADMIN PARA CONTRASEÑA */
Route::put('password1/{id}', 'UsuarioController@actualizarpassword1')->name('actualizar_password1');

/* RUTAS DEL ARCHIVO y ENTRADA */
Route::get('archivo', 'ArchivoController@index')->name('archivo')->middleware('superConsultor');
Route::post('guardar', 'EntradaController@guardar')->name('subir_archivo')->middleware('superEditor');

/* RUTAS DE ASIGNACION */
Route::get('asignacion', 'OrdenesmtlasignarController@index')->name('asignacion')->middleware('superEditor');
Route::post('asignacion_orden', 'OrdenesmtlasignarController@actualizar')->name('actualizar_asignacion')->middleware('superEditor');
Route::post('desasignacion_orden', 'OrdenesmtlasignarController@desasignar')->name('desasignar_asignacion')->middleware('superEditor');
Route::get('idDivision', 'OrdenesmtlasignarController@idDivisionss')->name('idDivisionsss')->middleware('superEditor');
/* DETALLE DE ORDENES */
Route::get('seguimiento', 'OrdenesmtlasignarController@seguimiento')->name('seguimiento')->middleware('superConsultor');
Route::get('seguimiento/{id}', 'OrdenesmtlasignarController@fotos')->name('fotos')->middleware('superConsultor');
Route::get('seguimientodetalle/{id}', 'OrdenesmtlasignarController@detalle')->name('detalle_de_orden')->middleware('superConsultor');
Route::get('posicionamiento', 'OrdenesmtlasignarController@posicionamiento')->name('posicionamiento')->middleware('superConsultor');
//Route::get('seguimientoExportar', 'OrdenesmtlasignarController@exportarExcel')->name('exportarxlsx');


/* RUTAS DE MARCA */
Route::get('marca', 'MarcasController@index')->name('marca')->middleware('superConsultor');
Route::get('marca/crear', 'MarcasController@crear')->name('crear_marca')->middleware('superEditor');
Route::post('marca', 'MarcasController@guardar')->name('guardar_marca')->middleware('superEditor');
Route::get('marca/{id}/editar', 'MarcasController@editar')->name('editar_marca')->middleware('superEditor');
Route::put('marca/{id}', 'MarcasController@actualizar')->name('actualizar_marca')->middleware('superEditor');
    
   
});

/* ══════════════════════════════════════════════════════════════
 * RUTAS V2 — Modernización progresiva (parallel path)
 * NO modificar ni eliminar las rutas originales de arriba.
 * ══════════════════════════════════════════════════════════════ */
Route::prefix('admin/v2')
    ->name('admin.v2.')
    ->middleware(['auth', 'superConsultor'])
    ->namespace('Admin\V2')
    ->group(function () {

    /* Pago card V2 — vista modernizada */
    Route::get('pago-card', 'PagoController@index')->name('pago_card.index');

    /* Los endpoints AJAX de pago siguen usando el controlador original.
     * Se agregan aquí para cuando se cree la lógica V2 propia.
     * Por ahora apuntan al mismo PagoCalenderController base. */
    Route::post('pago-card/guardar',   'PagoController@guardar')->name('pago_card.guardar');
    Route::get('pago-card/{id}/edit',  'PagoController@editar')->name('pago_card.editar');
    Route::put('pago-card/{id}',       'PagoController@actualizar')->name('pago_card.actualizar');
    Route::get('pago-card/{id}/editpay', 'PagoController@editpay')->name('pago_card.editpay');

    /* ── Préstamos V2 ────────────────────────────────────────────── */
    /* Vista principal (lista + DataTable AJAX) */
    Route::get( 'prestamo',                 'PrestamoController@index')      ->name('prestamo.index');
    /* Crear préstamo + cuotas */
    Route::post('prestamo',                 'PrestamoController@guardar')    ->name('prestamo.guardar');
    /* Anular (soft-delete) */
    Route::put( 'prestamo/{id}/anular',     'PrestamoController@anularp')    ->name('prestamo.anular');
    /* Datos para formulario de refinanciamiento */
    Route::get( 'prestamo/{id}/refinanciar','PrestamoController@refinanciar')->name('prestamo.refinanciar');
    /* Guardar refinanciamiento */
    Route::post('prestamo/refinanciar',     'PrestamoController@refiguardar')->name('prestamo.refiguardar');
    /* AJAX: cuotas detalladas del préstamo (por idp) */
    Route::get( 'prestamo/{id}/cuotas',     'PrestamoController@detalle')    ->name('prestamo.cuotas');
    /* AJAX: info del préstamo por idp */
    Route::get( 'prestamo/{id}/detalle',    'PrestamoController@detallep')   ->name('prestamo.detalle');
    /* AJAX: datos completos de préstamo + cliente */
    Route::get( 'prestamo/{id}/detalle-completo', 'PrestamoController@detallepn')->name('prestamo.detalle_completo');

    /* ── Clientes V2 ─────────────────────────────────────────────── */
    /* Vista principal (lista DataTable AJAX) + crear */
    Route::get( 'cliente',              'ClienteController@index')    ->name('cliente.index');
    Route::post('cliente',              'ClienteController@guardar')  ->name('cliente.guardar');
    /* Editar / actualizar */
    Route::get( 'cliente/{id}/editar',  'ClienteController@editar')   ->name('cliente.editar');
    Route::put( 'cliente/{id}',         'ClienteController@actualizar')->name('cliente.actualizar');
    /* AJAX: detalle de préstamos del cliente */
    Route::get( 'cliente/{id}/detalle', 'ClienteController@detalle')  ->name('cliente.detalle');

});

Route::group(['middleware' => ['auth','superEditor']], function () {

/* ORDENES CRITICA */
Route::get('critica', 'OrdenesmtlasignarController@critica')->name('critica');
Route::get('criticaadd', 'OrdenesmtlasignarController@criticaadd')->name('criticaadd');
Route::get('generar_critica', 'OrdenesmtlasignarController@generarcritica')->name('generar_critica');
Route::post('adicionar_critica', 'OrdenesmtlasignarController@adicionarcritica')->name('adicionar_critica');
Route::post('eliminar_critica', 'OrdenesmtlasignarController@eliminarcritica')->name('eliminar_critica');
});





