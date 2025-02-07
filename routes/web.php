<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportesClienteController;


// REDIRECCIONAMIENTO BASICO
Route::redirect('/', 'inicio');
// REDIRECCIONAMIENTO SEGUN ROL
Route::get('/inicio', 'InicioController@HomeRol')->name('homerol');
Route::get('/home', 'InicioController@HomeRol')->name('homerol');
Route::get('/logout', 'LoginController@logout')->name('salir');


// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% INTERFAZ Y VISTAS %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% //
Route::prefix('{language}')->group(function () {
    // ------------> DENTRO DEL SISTEMA ADMINISTRATIVO <------------------
    //////////////////////////////////// CLIENT CONTROLLER ///////////////////////////////////// 
    // DASHBOARD | INICIO
    Route::get('/home-cli', 'ClientController@Home')->name('dash-cli'); // Dashboard de Clientes
    // VISTA EMPLEADOS
    Route::get('/empleados-cli', 'ClientController@Empleados')->name('empleados-cli'); // Administracion Empleados
    //PERMISOS DE EMPLEADO
    Route::get('/permisos-cli', 'ClientController@PermisosArticulos')->name('permisos-cli'); // Asignacion de permiso
    // PERMISOS FILTRADO POR AREA
    Route::get('areas/permissions/{areaId}', 'ClientController@PermisosArticulosFilter')->name('permisos-cli'); // Asignacion de permiso
    ////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////// ADMIN CONTROLLER /////////////////////////////////////
    // DASHBOARD | INICIO
    Route::get('home-admin', 'AdminController@Home')->name('dash-admin'); // Dashboard de Administradores
    // USUARIOS
    Route::get('/usuarios', 'AdminController@Usuarios')->name('usuarios'); // Administracion Empleados
    // ADMINISTRADORES
    Route::get('/administradores', 'AdminController@AdminView')->name('administradores'); // Administracion Administradores
    // PLANTAS
    Route::get('/plantas', 'AdminController@Plantas')->name('plantas'); // Administracion Empleados
    Route::get('/plantas/PlantaView/{id}', [AdminController::class, 'PlantaView']);
    // ARTICULOS
    Route::get('/articulos', 'AdminController@Articulos')->name('articulos'); // Administracion Empleados
    // VENDINGS
    Route::get('/vendings', 'AdminController@Vendings')->name('vendings'); // Administracion Empleados
    // DISPOSITIVOS
    Route::get('/dispositivos', 'AdminController@Dispositivos')->name('dispositivos'); // Administracion Empleados
    // PLANOGRAMA
    Route::get('/config/plano/{id}', 'AdminController@Planograma')->name('planograma'); // Administracion Planograma
    // RELLENAR
    Route::get('/stock/rellenar/{id}', 'AdminController@Surtir')->name('rellenar'); // Administracion Surtido
    
    

    ////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////// LOGIN CONTROLLER //////////////////////////////////////
    // LOGIN
    Route::post('/validar-registro', 'LoginController@Login')->name('validar-registro');
    // LOGIN
    Route::post('/validar-admin', 'LoginController@ADMINLogin')->name('validar-admin');
    //////////////////////////////////////////////////////////////////////////////////////////// 
    //////////////////////////////////// NOTIFICACIONES ///////////////////////////////////////
    Route::get('/notifications/list', [NotificationController::class, 'listNotifications'])->name('listNotifications');
    ///////////////////////////////////// AREAS ////////////////////////////////////////////
    Route::get('/areas-cli', [ClientController::class, 'Areas'])->name('areas-cli');

    ///////////////////////////////////// REPORTES DE CONSUMO //////////////////////////////
    Route::get('/reporte/consumoxempleado', [ReportesClienteController::class, 'indexConsumoxEmpleado'])->name('consumosxempleado.index');
    Route::get('/reporte/consumoxarea', [ReportesClienteController::class, 'indexConsumoxArea'])->name('consumosxarea.index');
    Route::get('/reporte/consumoxvending', [ReportesClienteController::class, 'indexConsumoxVending'])->name('consumosxvending.index');
    ///////////////////////////////////// REPORTE DE VENDINGS /////////////////////////////
    Route::get('/reporte/inventariovm', [ReportesClienteController::class, 'indexInventarioVM'])->name('inventariovm.index');
});




// VISTA DEL LOGIN
Route::get('/login', function () {
    return view('login');
});
Route::get('/adminlogin', function () {
    return view('adminlogin');
});

// PROTECCION DE DATOS MIDDLEWARE
Route::group(['middleware' => 'checkSession'], function() {
// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°° ADMINISTRADORES °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°° //
//ADMINISTRADORES
Route::get('get-administradores', [AdminController::class, 'getAdministradores'])->name('get-administradores');
Route::post('/administrador/estatus', [AdminController::class, 'updateEstatus'])->name('update.administrador.estatus');
Route::post('/administrador/add', [AdminController::class, 'agregarAdministrador'])->name('add.administrador');
Route::delete('/administrador/{id}', [AdminController::class, 'destroyAdmin'])->name('administrador.destroy');
//USUARIOS
Route::get('get-usuarios', [AdminController::class, 'getUsuarios'])->name('get-usuarios');
Route::post('/usuario/estatus', [AdminController::class, 'updateEstatusUser'])->name('update.user.estatus');
Route::post('/guardar-usuario', [AdminController::class, 'guardarUsuario']);
Route::delete('/usuario/{id}', [AdminController::class, 'destroyUser'])->name('user.destroy');
//PLANTAS
Route::get('/getPlantas', [AdminController::class, 'getPlantas']);
Route::get('/getPlantasInfo', [AdminController::class, 'getPlantasInfo'])->name('getPlantasInfo');
Route::post('/guardar-planta', [AdminController::class, 'guardarPlanta'])->name('guardarPlanta');
Route::delete('/planta/{id}', [AdminController::class, 'destroyPlanta'])->name('planta.destroy');
Route::post('/planta/estatus', [AdminController::class, 'updateEstatusPlanta'])->name('update.planta.estatus');
Route::post('/planta/update', [AdminController::class, 'updatePlanta'])->name('updatePlanta');
Route::delete('/planta/release/{id}', [AdminController::class, 'releaseRelatedRecords']);
//PLANTA AREA
Route::post('/plantable/{id}', [AdminController::class, 'TablasPlant']);
Route::post('/planta/areas/update-status', [AdminController::class, 'updateStatusArea'])->name('admin-areas.update-status');
Route::post('/planta/areas/generate-permissions', [AdminController::class, 'generateMissingPermissions']);
Route::post('/admin/generar-permisos', [AdminController::class, 'generateAllMissingPermissions'])->name('generate.all.permissions');
Route::post('/planta/areas/add', [AdminController::class, 'addArea']);
Route::get('/planta/export-excel-areas', [AdminController::class, 'exportExcelAreas']);
//PLANTA PERMISOS
Route::get('/planta/get-permisos-articulos/{idPlanta}', 'AdminController@getPermisosArticulos')->name('admin-get.permisos.articulos'); // Asignacion de permisos
Route::post('/planta/check-permission', [AdminController::class, 'checkPermission']);
Route::post('/planta/add-permission', [AdminController::class, 'addPermission']);
Route::get('/planta/export-excel-permissions', [AdminController::class, 'exportPermisos'])->name('admin-exportar.permisos');
Route::get('/admin/plantas/PlantaView/{idPlanta}/permisos/{idArea}', [AdminController::class, 'filtrarPermisosPorArea']);
//PLANTA EMPLEADOS
Route::get('/planta/empleados/data/{idPlanta}', 'AdminController@getDataEmpleados')->name('admin-empleados.data');
Route::get('/planta/export-csv-employees', 'AdminController@exportCSV');
Route::post('/planta/import-csv-employees', 'AdminController@importCSV');
Route::get('/planta/export-excel-employees', 'AdminController@exportExcel');
Route::post('/planta/empleado/add', 'AdminController@storeemployee');
//ARTICULOS
Route::get('/articulos-datatable', [AdminController::class, 'getArticulosDataTable']);
Route::post('/cambiar-estatus-articulo', [AdminController::class, 'cambiarEstatus'])->name('cambiar.estatus.articulo');
Route::post('/articulos/store', [AdminController::class, 'storeArticulo']);
Route::delete('/articulos/{id}/delete', [AdminController::class, 'deleteArticulo']);
Route::get('/articulos/{id}/edit', [AdminController::class, 'editArticulo']);
Route::post('/articulos/{id}/update', [AdminController::class, 'updateArticulo']);
//VENDINGS
Route::get('/vendings/data', [AdminController::class, 'getVendingsData'])->name('vendings.data');
Route::post('/vending/changeStatus', 'AdminController@changeStatusvm');
Route::post('/vending/delete', 'AdminController@deletevm');
Route::get('/vending/getDetails/{id}', [AdminController::class, 'getDetailsvm']);
Route::get('/vending/devices/{currentDeviceId?}', [AdminController::class, 'getAvailableDevices']);
Route::get('/vending/edit/{id}', [AdminController::class, 'getVendingMachine']);
Route::post('/vending/update', [AdminController::class, 'updateVendingMachine']);
Route::post('/vending/create', [AdminController::class, 'storeVM'])->name('vending.store');
// PLANOGRAMA
Route::post('/admin/config/plano/save', [AdminController::class, 'guardarCambiosPlano']);
Route::post('/admin/config/plano/remove', [AdminController::class, 'eliminarArticuloPlano']);
//RELLENAR
Route::post('/update-stock', [AdminController::class, 'updateStock'])->name('update.stock');

//DISPOSITIVOS
Route::get('/dispositivos/get', [AdminController::class, 'getDispositivos'])->name('dispositivos.get');
Route::post('/dispositivos/store', [AdminController::class, 'storeDispositivo'])->name('dispositivos.store');
Route::get('/dispositivos/{id}', [AdminController::class, 'showDispositivo'])->name('dispositivos.show'); // Para obtener datos del dispositivo
Route::post('/dispositivos/update/{id}', [AdminController::class, 'updateDispositivo'])->name('dispositivos.update');
Route::delete('/dispositivos/destroy/{id}', [AdminController::class, 'destroyDispositivo'])->name('dispositivos.destroy');
Route::get('/maquinas/list', [AdminController::class, 'listMquinas'])->name('maquinas.list');



//AREAS
Route::get('/getAreas', [AdminController::class, 'getAreas']);


//##################################### REGISTRO DE ESTATUS ######################################################//
Route::get('vm-admin', 'StatusController@getAdminDash')->name('getadmindash');
Route::get('vm-dash', 'StatusController@getIndexDash')->name('getindexdash');
Route::get('vm-status', 'StatusController@GetStatus')->name('getstatus');
Route::get('vm-rconsum', 'StatusController@ConsumosGet')->name('getconsum');
Route::get('vm-graphs', 'StatusController@getConsumoGraph')->name('getgraph');


// °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°° CLIENTES °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°° //
///////////////////////////////////////// EMPLEADOS TOOLS ///////////////////////////////////////////////////
Route::post('empleado/toggle-status/{id}', 'ClientController@toggleStatus');
Route::get('empleados/data', 'ClientController@getDataEmpleados')->name('empleados.data');
Route::get('export-csv-employees', 'ClientController@exportCSV');
Route::post('import-csv-employees', 'ClientController@importCSV');
Route::get('empleado/delete/{Id_Empleado}','ClientController@destroyEmployee')->name('empleado.delete');
Route::get('export-excel-employees', 'ClientController@exportExcel');
Route::post('empleado/add', 'ClientController@storeemployee');
Route::get('areas/data', 'ClientController@getAreas')->name('areas.data');
Route::put('empleado/update/{id}', 'ClientController@updateemployee')->name('empleados.update');
///////////////////////////////////////// PERMISOS TOOLS ///////////////////////////////////////////////////
Route::get('get-permisos-articulos', 'ClientController@getPermisosArticulos')->name('get.permisos.articulos'); // Asignacion de permisos
Route::delete('/delete-permiso-articulo/{id}', 'ClientController@deletePermisoArticulo')->name('delete.permiso.articulo');
Route::post('/update-permiso-articulo/{id}', 'ClientController@updatePermisoArticulo')->name('update.permiso.articulo');
Route::post('/toggle-status-permiso-articulo/{id}', 'ClientController@toggleStatusPermiso')->name('toggle.status.permiso.articulo');
Route::post('check-permission', [ClientController::class, 'checkPermission']);
Route::post('add-permission', [ClientController::class, 'addPermission']);
Route::get('export-excel-permissions', [ClientController::class, 'exportPermisos'])->name('exportar.permisos');
Route::get('/get-permisos-articulos/{Id}', [ClientController::class, 'getPermisosPorArea'])->name('getPermisosPorArea');
Route::post('/areas/generate-permissions', [ClientController::class, 'generateMissingPermissions']);
///////////////////////////////////////// AREAS TOOLS ///////////////////////////////////////////////////////
Route::get('/get-areas/data', 'ClientController@getDataAreas')->name('get-areas.data');
Route::post('/areas/update-name', [ClientController::class, 'updateNameArea'])->name('areas.update-name');
Route::post('/areas/update-status', [ClientController::class, 'updateStatusArea'])->name('areas.update-status');
Route::post('areas/add', [ClientController::class, 'addArea']);
Route::post('/areas/delete', [ClientController::class, 'deleteArea']);
Route::get('export-excel-areas', [ClientController::class, 'exportExcelAreas']);
/////////////////////////////////////////// REPORTES DE CONSUMO ///////////////////////////////////////////
Route::get('/getconsumoxempleado/data', [ReportesClienteController::class, 'getConsumoxEmpleado'])->name('consumosxempleado.data');
Route::get('/export/consumoxempleado', [ReportesClienteController::class, 'exportConsumoxEmpleado'])->name('export.consumoxempleado');
Route::get('/getconsumoxarea/data', [ReportesClienteController::class, 'getConsumoxArea'])->name('consumosxarea.data');
Route::get('/export/consumoxarea', [ReportesClienteController::class, 'exportConsumoxArea'])->name('export.consumoxarea');
Route::get('/getconsumoxvending/data', [ReportesClienteController::class, 'getConsumoxVending'])->name('consumosxvending.data');
Route::get('/export/consumoxvending', [ReportesClienteController::class, 'exportConsumoxVending'])->name('export.consumoxvending');
/////////////////////////////////////////// REPORTES DE VENDINGS ///////////////////////////////////////////
Route::get('/getinventariovm/data', [ReportesClienteController::class, 'getInventarioVM'])->name('inventariovm.data');
Route::get('/getstockvm/data/{idMaquina}', [ReportesClienteController::class, 'getInvStock'])->name('stockvm.data');
Route::get('/export-inventariovm', [ReportesClienteController::class, 'exportInventarioVM'])->name('export.inventariovm');
///////////////////////////////////////// NOTIFICACIONES ///////////////////////////////////////////////////

Route::get('/notifications/unread', [NotificationController::class, 'showNotifications'])->name('getUnreadNotifications');
Route::get('/mark-notification-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('markNotificationAsRead');


////////////////////////////////////      PRUEBAS     ///////////////////////////////

Route::get('/layout-vm', function () {
    session_start();
    return view('administracion.layout');
});

Route::get('/online-vm', function () {
    
    return view('monitoreo.online-vending');
});
});

