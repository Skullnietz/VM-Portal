<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OperadorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportesClienteController;
use Illuminate\Http\Request;


// REDIRECCIONAMIENTO BASICO
Route::redirect('/', '/inicio');
// REDIRECCIONAMIENTO SEGUN ROL
Route::get('/inicio', 'InicioController@HomeRol')->name('homerol');
Route::get('/home', 'InicioController@HomeRol')->name('homerol');
Route::get('/logout', 'LoginController@logout')->name('salir');


// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% INTERFAZ Y VISTAS %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% //
Route::prefix('{language}')->group(function () {
    //////////////////////////////////// LOGIN CONTROLLER //////////////////////////////////////
    // LOGIN
    Route::post('/validar-registro', 'LoginController@Login')->name('validar-registro');
    // LOGIN
    Route::post('/validar-admin', 'LoginController@ADMINLogin')->name('validar-admin');
    //LOGIN
    Route::post('/validar-operador', [LoginController::class, 'operadorLogin'])->name('login.operador');
    //////////////////////////////////////////////////////////////////////////////////////////// 
});




// VISTA DEL LOGIN
Route::get('/login', function () {
    return view('login');
});
Route::get('/adminlogin', function () {
    return view('adminlogin');
});
Route::get('/oplogin', function () {
    return view('operadorlogin');
});

// PROTECCION DE DATOS MIDDLEWARE
Route::group(['middleware' => 'checkSession'], function () {

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
        // OPERADORES
        Route::get('/operadores', 'AdminController@OpView')->name('operadores'); // Administracion Administradores
        // PLANTAS
        Route::get('/plantas', 'AdminController@Plantas')->name('plantas'); // Administracion PLANTAS
        Route::get('/plantas/PlantaView/{id}', [AdminController::class, 'PlantaView']);
        // ARTICULOS
        Route::get('/articulos', 'AdminController@Articulos')->name('articulos'); // Administracion ARTICULOS
        // ARTICULOS
        Route::get('/codigocte', 'AdminController@CodigoCteV')->name('codigoscte'); // Administracion Codigos CTE
        // VENDINGS
        Route::get('/vendings', 'AdminController@Vendings')->name('vendings'); // Administracion VENDINGS
        // DISPOSITIVOS
        Route::get('/dispositivos', 'AdminController@Dispositivos')->name('dispositivos'); // Administracion DISPOSITIVOS
        // PLANOGRAMA
        Route::get('/config/plano/{id}', 'AdminController@Planograma')->name('planograma'); // Administracion Planograma
        // RELLENAR
        Route::get('/Astock/rellenar/{id}', 'AdminController@Surtir')->name('Arellenar'); // Administracion Surtido
        // ALERTAS
        Route::get('/alertas', [AdminController::class, 'Alertas']);



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
        ///////////////////////////////////// REPORTE DE PERMISOS /////////////////////////////
        Route::get('/reporte/consultaconsumos', [ReportesClienteController::class, 'indexConsultaConsumos'])
            ->name('consultasconsumo.index');
        Route::post('/reporte/consultaconsumos/data', [ReportesClienteController::class, 'dataConsultaConsumos'])
            ->name('consultasconsumo.data'); // <- endpoint de datos (POST)

        //RELLENADO DE VENDINGS | OPERADOR
        Route::get('/stock/rellenar/{id}', 'OperadorController@Surtir')->name('rellenar'); // Administracion Surtido
        Route::get('/op-vendings', 'OperadorController@Vendings')->name('op-vendings'); // Administracion VENDINGS
    });


    // °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°° ADMINISTRADORES °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°° //
//OPERADORES 
    Route::post('get-operadores', [AdminController::class, 'getOperadores'])->name('get-operadores');
    Route::post('/operadores/add', [AdminController::class, 'agregarOperador'])->name('add.operador');
    Route::post('/operadores/estatus', [AdminController::class, 'updateOpEstatus'])->name('update.operador.estatus');
    Route::delete('/operadores/{id}', [AdminController::class, 'destroyOperador'])->name('operador.destroy');
    Route::post('/admin/editar-operador', [AdminController::class, 'editarOperador']);
    //ADMINISTRADORES
    Route::post('get-administradores', [AdminController::class, 'getAdministradores'])->name('get-administradores');
    Route::post('/administrador/estatus', [AdminController::class, 'updateEstatus'])->name('update.administrador.estatus');
    Route::post('/administrador/add', [AdminController::class, 'agregarAdministrador'])->name('add.administrador');
    Route::post('/administrador/{id}', [AdminController::class, 'destroyAdmin'])->name('administrador.destroy');
    //USUARIOS
    Route::post('get-usuarios', [AdminController::class, 'getUsuarios'])->name('get-usuarios');
    Route::post('/usuario/estatus', [AdminController::class, 'updateEstatusUser'])->name('update.user.estatus');
    Route::post('/guardar-usuario', [AdminController::class, 'guardarUsuario']);
    Route::post('/usuario/{id}', [AdminController::class, 'eliminarUsuario'])->name('user.destroy');
    //PLANTAS
    Route::post('/getPlantas', [AdminController::class, 'getPlantas'])->name('get-plantas');
    Route::post('/getPlantasInfo', [AdminController::class, 'getPlantasInfo'])->name('getPlantasInfo');
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
    Route::post('/planta/get-permisos-articulos/{idPlanta}', 'AdminController@getPermisosArticulos')->name('admin-get.permisos.articulos'); // Asignacion de permisos
    Route::post('/planta/check-permission', [AdminController::class, 'checkPermission']);
    Route::post('/planta/add-permission', [AdminController::class, 'addPermission']);
    Route::get('/planta/export-excel-permissions', [AdminController::class, 'exportPermisos'])->name('admin-exportar.permisos');
    Route::post('/admin/plantas/PlantaView/{idPlanta}/permisos/{idArea}', [AdminController::class, 'filtrarPermisosPorArea']);
    //PLANTA EMPLEADOS
    Route::post('/planta/empleados/data/{idPlanta}', 'AdminController@getDataEmpleados')->name('admin-empleados.data');
    Route::get('/planta/export-csv-employees', 'AdminController@exportCSV');
    Route::post('/planta/import-csv-employees', 'AdminController@importCSV');
    Route::get('/planta/export-excel-employees', 'AdminController@exportExcel');
    Route::post('/planta/empleado/add', 'AdminController@storeemployee');
    //ARTICULOS
    Route::post('/articulos-datatable', [AdminController::class, 'getArticulosDataTable']);
    Route::post('/cambiar-estatus-articulo', [AdminController::class, 'cambiarEstatus'])->name('cambiar.estatus.articulo');
    Route::post('/articulos/store', [AdminController::class, 'storeArticulo']);
    Route::delete('/articulos/{id}/delete', [AdminController::class, 'deleteArticulo']);
    Route::get('/articulos/{id}/edit', [AdminController::class, 'editArticulo']);
    Route::post('/articulos/{id}/update', [AdminController::class, 'updateArticulo']);
    // CODIGOS CLIENTE
// Ruta para listar los registros (con parámetro opcional)
    Route::get('/codigocte/{id?}', 'AdminController@CodigoCteID')->name('codigocte');
    // Ruta para crear un nuevo registro
    Route::post('/codigocte/store', 'AdminController@storeCodigoCte')->name('codigocte.store');
    // Ruta para actualizar un registro existente
    Route::put('/codigocte/update', 'AdminController@updateCodigoCte')->name('codigocte.update');
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
    Route::post('/dispositivos/get', [AdminController::class, 'getDispositivos'])->name('dispositivos.get');
    Route::post('/dispositivos/store', [AdminController::class, 'storeDispositivo'])->name('dispositivos.store');
    Route::get('/dispositivos/{id}', [AdminController::class, 'showDispositivo'])->name('dispositivos.show'); // Para obtener datos del dispositivo

    // ALERTAS
    Route::post('/allalertas', [AdminController::class, 'getConfiguracionesReportes'])->name('alertas.get');
    Route::post('/admin/alertas/users', [AdminController::class, 'getUsersForAlerts'])->name('alertas.users');
    Route::post('/admin/alertas/store', [AdminController::class, 'StoreAlertaAdmin'])->name('alertas.store');
    Route::get('/admin/alertas/{id}', [AdminController::class, 'getAlerta'])->name('alertas.show');
    Route::post('/admin/alertas/update/{id}', [AdminController::class, 'UpdateAlertaAdmin'])->name('alertas.update');
    Route::delete('/admin/alertas/{id}', [AdminController::class, 'destroyAlerta'])->name('alertas.destroy');
    Route::post('/reportes/guardar-configuracion', [ReportesClienteController::class, 'guardarConfiguracion'])->name('reportes.guardar_configuracion');

    // REPORTES ADMINISTRADOR
    Route::get('/admin/reporte/consumoxempleado', [AdminController::class, 'ReporteConsumoEmpleado'])->name('admin.consumoxempleado.index');
    Route::get('/admin/getconsumoxempleado/data', [AdminController::class, 'getReporteConsumoEmpleadoData'])->name('admin.getconsumoxempleado.data');

    // SINCRONIZACION
    Route::post('/sync-data', [AdminController::class, 'getSyncData'])->name('sync.data');




    //##################################### REGISTRO DE ESTATUS ######################################################//
    Route::get('vm-admin', 'StatusController@getAdminDash')->name('getadmindash');
    Route::get('vm-dash', 'StatusController@getIndexDash')->name('getindexdash');
    Route::get('vm-allstatus', 'StatusController@GetAllStatus')->name('getallstatus');
    Route::get('vm-status', 'StatusController@GetStatus')->name('getstatus');
    Route::get('vm-rconsum', 'StatusController@ConsumosGet')->name('getconsum');
    Route::get('vm-rallconsum', 'StatusController@ConsumosGetAdmin')->name('getallconsum');
    Route::get('vm-graphs', 'StatusController@getConsumoGraph')->name('getgraph');
    Route::get('vm-admingraphs', 'StatusController@getAdminDashboardStats')->name('getadmingraph');


    // °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°° CLIENTES °°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°°° //
///////////////////////////////////////// EMPLEADOS TOOLS ///////////////////////////////////////////////////



    Route::get('/test-upload', function () {
        return response('
        <form method="POST" action="/test-upload" enctype="multipart/form-data">
            ' . csrf_field() . '
            <input type="file" name="csv_file" required>
            <button type="submit">Enviar</button>
        </form>
    ', 200)->header('Content-Type', 'text/html');
    });

    Route::post('/test-upload', function (Request $request) {
        // Verificamos si PHP puede escribir en su directorio temporal
        $tmpDir = sys_get_temp_dir(); // Por lo general /tmp en Linux o C:\Windows\Temp en Windows
        $testFile = $tmpDir . '/test-upload.txt';

        // Intentamos escribir un archivo de prueba
        file_put_contents($testFile, 'Prueba de escritura desde Laravel');

        return response()->json([
            'tmp_dir' => $tmpDir,
            'file_exists' => file_exists($testFile),
            'writable' => is_writable($tmpDir),
            'php_user' => get_current_user(),
            'php_ini_upload_tmp_dir' => ini_get('upload_tmp_dir'),
        ]);
    });


    Route::post('empleado/toggle-status/{id}', 'ClientController@toggleStatus');
    Route::post('empleados/data', 'ClientController@getDataEmpleados')->name('empleados.data');
    Route::get('export-csv-employees', 'ClientController@exportCSV');
    Route::post('import-csv-employees', 'ClientController@importCSV');
    Route::get('empleado/delete/{Id_Empleado}', 'ClientController@destroyEmployee')->name('empleado.delete');
    Route::get('export-excel-employees', 'ClientController@exportExcel');
    Route::post('empleado/add', 'ClientController@storeemployee');
    Route::get('areas/data', 'ClientController@getAreas')->name('areas.data');
    Route::post('empleado/update/{id}', 'ClientController@updateemployee')->name('empleados.update');
    ///////////////////////////////////////// PERMISOS TOOLS ///////////////////////////////////////////////////
    Route::get('get-permisos-articulos', 'ClientController@getPermisosArticulos')->name('get.permisos.articulos'); // Asignacion de permisos
    Route::post('/delete-permiso-articulo/{id}', 'ClientController@deletePermisoArticulo')->name('delete.permiso.articulo');
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
    Route::post('/asignar-permisos-area', [ClientController::class, 'asignarPermisosArea']);
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

    ////////////////////////////////////     OPERADORES    ///////////////////////////////
    Route::get('op/vendings/data', [OperadorController::class, 'getVendingsData'])->name('vendings.data');



    ////////////////////////////////////      PRUEBAS     ///////////////////////////////

    Route::get('/layout-vm', function () {
        session_start();
        return view('administracion.layout');
    });

    Route::get('/online-vm', function () {

        return view('monitoreo.online-vending');
    });
});

