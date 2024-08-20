<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\NotificationController;

// REDIRECCIONAMIENTO BASICO
Route::redirect('/', 'inicio');
// REDIRECCIONAMIENTO SEGUN ROL
Route::get('/inicio', 'InicioController@HomeRol')->name('homerol');
Route::get('/home', 'InicioController@HomeRol')->name('homerol');
Route::post('/logout', 'LoginController@logout');



Route::prefix('{language}')->group(function () {
    // ------------> DENTRO DEL SISTEMA ADMINISTRATIVO <------------------
    //////////////////////////////////// CLIENT CONTROLLER ///////////////////////////////////// 
    // DASHBOARD | INICIO
    Route::get('/home-cli', 'ClientController@Home')->name('dash-cli'); // Dashboard de Clientes
    // VISTA EMPLEADOS
    Route::get('/empleados-cli', 'ClientController@Empleados')->name('empleados-cli'); // Administracion Empleados
    //PERMISOS DE EMPLEADO
    Route::get('/permisos-cli', 'ClientController@PermisosArticulos')->name('permisos-cli'); // Asignacion de permiso
    ////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////// LOGIN CONTROLLER //////////////////////////////////////
    // LOGIN
    Route::post('/validar-registro', 'LoginController@Login')->name('validar-registro');
    //////////////////////////////////////////////////////////////////////////////////////////// 
    //////////////////////////////////// NOTIFICACIONES ///////////////////////////////////////
    Route::get('/notifications/list', [NotificationController::class, 'listNotifications'])->name('listNotifications');
    ///////////////////////////////////// AREAS ////////////////////////////////////////////
    Route::get('/areas-cli', [ClientController::class, 'Areas'])->name('areas-cli');
    
});




// VISTA DEL LOGIN
Route::get('/login', function () {
    return view('login');
});
/////////////////////////////////////// REGISTRO DE ESTATUS ////////////////////////////////////////////////////
Route::get('vm-dash', 'StatusController@getIndexDash')->name('getindexdash');
Route::get('vm-status', 'StatusController@GetStatus')->name('getstatus');
Route::get('vm-rconsum', 'StatusController@ConsumosGet')->name('getconsum');
Route::get('vm-graphs', 'StatusController@getConsumoGraph')->name('getgraph');
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


