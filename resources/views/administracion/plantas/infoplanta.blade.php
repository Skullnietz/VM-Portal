@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Ajustes Planta'))



@section('content')
<!-- Modal para agregar área -->
<div class="modal fade" id="addAreaModal" tabindex="-1" role="dialog" aria-labelledby="addAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAreaModalLabel">Agregar Área</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addAreaForm">
                    <div class="form-group">
                        <label for="areaName">Nombre del Área</label>
                        <input type="text" class="form-control" id="areaName" name="areaName" required>
                        <input type="hidden" value="{{$planta->Id_Planta}}" class="form-control" id="idPlanta" name="idPlanta" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal para Agregar Permiso -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" role="dialog" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addPermissionForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addPermissionModalLabel">Agregar Permiso</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" value="{{$planta->Id_Planta}}" name="idPlanta">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="area">Área</label>
                        <select name="Id_Area" class="form-control select3" required>
                            @foreach($areas as $area)
                                <option value="{{ $area->Id_Area }}">{{ $area->Txt_Nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="articulo">Artículo</label>
                        <select name="Id_Articulo" class="form-control select3" required>
                            @foreach($articulos as $articulo)
                                <option value="{{ $articulo->Id_Articulo }}">{{ $articulo->Txt_Descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="frecuencia">Frecuencia (días)</label>
                        <input type="number" name="Frecuencia" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Cantidad (pzas)</label>
                        <input type="number" name="Cantidad" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="submitBtn" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Empleado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Campo oculto para el ID -->
                    <input type="hidden" id="editId" name="id">
                    <form id="editForm">
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed"><i class="fas fa-key"></i>&nbsp;&nbsp;| NIP</span>
                                </div>
                                <input type="number" class="form-control" id="nip" name="nip" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed"><i class="fas fa-address-card"></i>&nbsp;&nbsp;| ID Tarjeta</span>
                                </div>
                                <input type="number" class="form-control" id="notarjeta" name="notarjeta" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Nombre</span>
                                </div>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Paterno</span>
                                </div>
                                <input type="text" class="form-control" id="apaterno" name="apaterno" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Materno</span>
                                </div>
                                <input type="text" class="form-control" id="amaterno" name="amaterno">
                            </div>
                        </div>
                        <div class="form-group"> 
                            <label for="area"><i class="fas fa-warehouse"></i>&nbsp;&nbsp;&nbsp;Área &nbsp;| &nbsp;&nbsp;Permisos de Producto</label>
                            <select class="form-control" id="area" name="area" required>
                                <!-- Opciones se llenarán con JavaScript -->
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload CSV Modal -->
    <div class="modal fade" id="uploadCsvModal" tabindex="-1" role="dialog" aria-labelledby="uploadCsvModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadCsvModalLabel">Subir archivo CSV</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ url('/planta/import-csv-employees?idPlanta=') }}{{$planta->Id_Planta}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="csvFile">Selecciona el archivo CSV</label>
                            <input type="file" class="form-control" id="csvFile" name="csv_file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Subir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para agregar empleado -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">Agregar Empleado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addEmployeeForm">
                    <input type="hidden" value="{{$planta->Id_Planta}}" name="idPlanta">
                <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-id-card-alt"></i>&nbsp;&nbsp;| N° Empleado</span>
                            </div>
                            <input type="number" class="form-control" id="no_empleado" name="no_empleado" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-key"></i>&nbsp;&nbsp;| NIP</span>
                            </div>
                            <input type="number" class="form-control" id="nip" name="nip" >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-address-card"></i>&nbsp;&nbsp;| ID Tarjeta</span>
                            </div>
                            <input type="number" class="form-control" id="no_tarjeta" name="no_tarjeta">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Nombre</span>
                            </div>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Paterno</span>
                            </div>
                            <input type="text" class="form-control" id="apaterno" name="apaterno" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-fixed"><i class="fas fa-user"></i>&nbsp;&nbsp;| Apellido Materno</span>
                            </div>
                            <input type="text" class="form-control" id="amaterno" name="amaterno">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="area"><i class="fas fa-warehouse"></i>&nbsp;&nbsp;&nbsp;Área &nbsp;| &nbsp;&nbsp;Permisos de Producto</label>
                        <select class="form-control" id="area" name="area" required>
                            @foreach ($areas as $area)
                                <option value="{{ $area->Id_Area }}">{{ $area->Txt_Nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" src="{{$planta->Ruta_Imagen}}" alt="Plant profile picture">
                </div>

                <h3 class="profile-username text-center">{{$planta->Txt_Nombre_Planta}}</h3>

                <p class="text-muted text-center">{{$planta->Txt_Sitio}}</p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Fecha Alta:</b> <a class="float-right">{{$planta->Fecha_Alta}}</a>
                  </li>
                  <li class="list-group-item">
                    <b>Fecha Modif:</b> <a class="float-right">{{$planta->Fecha_Modificacion}}</a>
                  </li>
                  <li class="list-group-item">
                    <b>Fecha Baja:</b> <a class="float-right">{{$planta->Fecha_Baja}}</a>
                  </li>
                </ul>
                @if($planta->Txt_Estatus == 'Alta')
                <a href="#" class="btn btn-success btn-block"><b>Estatus: Alta</b></a>
                @else
                <a href="#" class="btn btn-warning btn-block"><b>Estatus: Baja</b></a>
                @endif
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Vendings Registradas</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">

              @if($vendings->isEmpty())
                  <p class="text-muted">No hay máquinas expendedoras vinculadas a esta planta.</p>
                  <a href="#" class="btn btn-secondary">
                      Registrar Máquina Expendedora
                  </a>
              @else
                  @foreach($vendings as $vending)
                      <strong><i class="fas fa-vending-machine mr-1"></i> {{ $vending->Txt_Nombre }}</strong>

                      <p class="text-{{ $vending->Txt_Estatus === 'Alta' ? 'success' : 'danger' }}">
                          <span class="dot-status" style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: {{ $vending->Txt_Estatus === 'Alta' ? '#28a745' : '#dc3545' }}; margin-right: 5px;"></span> 
                          {{ $vending->Txt_Estatus === 'Alta' ? 'Habilitada' : 'Deshabilitada' }}
                      </p>
                      <hr>
                  @endforeach
              @endif

                

                

                
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-9">
            <div class="card">
              <div class="card-header p-2">
              <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active" id="linkarea" href="#areas" data-toggle="tab">Áreas</a></li>
                <li class="nav-item"><a class="nav-link" id="linkpermiso" href="#permisos" data-toggle="tab">Permisos por Área</a></li>
                <li class="nav-item"><a class="nav-link" id="linkempleado" href="#empleados" data-toggle="tab">Empleados</a></li>
              </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
            <div class="tab-content">
            
                <div class="active tab-pane" id="areas">
                <div class="col text-right">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" id="enableEditBtn" class="btn btn-secondary">
                    Habilitar Edición &nbsp;&nbsp;&nbsp;<i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAreaModal">
                    Agregar Área &nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-plus-circle fa-xs"></i> <i class="fas fa-square"></i>
                </button>
                <button type="button" class="btn btn-warning" id="generar-permisos">
                    Actualizar Permisos &nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-lock "></i> <i class="fas fa-sync-alt fa-xs"></i>
                </button>
                <a href="{{ url('/planta/export-excel-areas?idPlanta=') }}{{$planta->Id_Planta}}" type="button" class="btn btn-success">
                    Reporte de Áreas &nbsp;&nbsp;&nbsp;<i class="fas fa-file-excel"></i>
                </a>
            </div>
        </div>
        <br>
                    <table id="areasTable" class="table table-bordered table-striped">
                    <thead>
                                <tr>
                                    <th>ID Área</th>
                                    <th>Nombre</th>
                                    <th>Estatus</th> 
                                    <th>Fecha de Alta</th>
                                    <th>Fecha de Modificación</th>
                                    <th>Acciones</th>
                                    <th>Permisos</th>
                                </tr>
                      </thead>
                    </table>
                </div>

                <div class="tab-pane" id="permisos">
                    
                <div class="col text-right">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPermissionModal">
                            Agregar Permiso &nbsp;&nbsp;&nbsp;<i class="fas fa-user-lock"></i>
                        </button>
                        <a href="{{ url('/planta/export-excel-permissions?idPlanta=') }}{{$planta->Id_Planta}}" type="button" class="btn btn-success">
                            Reporte <i class="fas fa-file-excel"></i>
                        </a>
                    </div>
                </div><br>
                    <table id="permisos-articulos-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                            <th>Nombre</th>
                            <th>Artículo</th>
                            <th style="width:100px">Frecuencia</th>
                            <th style="width:100px">Cantidad</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="tab-pane" id="empleados">
                    <!-- Columna de la derecha con alineación a la derecha -->
            <div class="col text-right">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a type="button" href="{{ url('/planta/export-csv-employees?idPlanta=') }}{{$planta->Id_Planta}}" class="btn btn-secondary">
                        Descarga .CSV &nbsp;&nbsp;&nbsp;<i class="fas fa-file-csv"></i> <i class="fas fa-download"></i>
                    </a>
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#uploadCsvModal">
                        Subida .CSV &nbsp;&nbsp;&nbsp;<i class="fas fa-file-csv"></i> <i class="fas fa-cloud-upload-alt"></i>
                    </button>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addEmployeeModal">
                        Agregar Empleado &nbsp;&nbsp;&nbsp;<i class="fas fa-user-plus"></i>
                    </button>
                    <a href="{{ url('/planta/export-excel-employees?idPlanta=') }}{{$planta->Id_Planta}}" type="button" class="btn btn-success">
                        Reporte &nbsp;&nbsp;&nbsp;<i class="fas fa-file-excel"></i>
                    </a>
                </div><br><br>
                    <table id="empleados-table" class="table table-bordered table-striped">
                        <thead>
                                <tr>
                                    <th>No. empleado</th>
                                    <th>NIP</th>
                                    <th>Tarjeta</th>
                                    <th>Nombre</th>
                                    <th>Apellido Paterno</th>
                                    <th>Apellido Materno</th>
                                    <th>Área</th>
                                    <th>Permisos producto</th>
                                    <th>Editar</th>
                                    <th>Borrar</th>
                                    <th>Estatus</th>
                                    <th>Última modificación</th>
                                </tr>
                        </thead>
                    </table>
                </div>
            </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
      </section>
@stop

@section('right-sidebar')
@stop

@section('css')
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <!-- Incluir CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@stop

@section('js')
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/i18n/datepicker-es.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Incluir JavaScript de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $("#generar-permisos").click(function() {
        let plantaId = {{ $planta->Id_Planta }}; // Asegúrate de tener el ID de la planta disponible en Blade

        $.ajax({
            url: "{{ route('generate.all.permissions') }}",
            type: "POST",
            data: {
                idPlanta: plantaId,
                _token: "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $("#generar-permisos").prop("disabled", true).text("Generando...");
            },
            success: function(response) {
                alert(response.message); // Mostrar mensaje de éxito
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseJSON.message);
            },
            complete: function() {
                $("#generar-permisos").prop("disabled", false).text("Permisos Actualizados");
            }
        });
    });
});
</script>
<script>
  $(document).ready(function() {
    $.ajaxSetup({
    headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });



    // Cargar DataTable solo cuando se selecciona el tab correspondiente
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});
</script>
<script>

    $(document).ready(function() {
    // SCRIPT DE AREAS
    var areasTable = $('#areasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ url("/plantable/".$planta->Id_Planta) }}',
            type: 'POST',
            data: { table: 'areas' }
        },
        columns: [
            { data: 'Id_Area' },
            {
                data: 'Txt_Nombre',
                render: function(data, type, row) {
                    return '<input type="text" style="width:250px" class="form-control editable-name" data-id="' + row.Id_Area + '" value="' + data + '" disabled>';
                }
            },
            {
                data: 'Txt_Estatus',
                render: function(data, type, row) {
                    var buttonClass = data === 'Alta' ? 'btn-success' : 'btn-danger';
                    var newStatus = data === 'Alta' ? 'Baja' : 'Alta';
                    return '<button class="btn ' + buttonClass + ' toggle-status" data-id="' + row.Id_Area + '" data-new-status="' + newStatus + '" data-idplanta="{{$planta->Id_Planta}}">' + data + '</button>';
                },
                orderable: false,
                searchable: false
            },
            { data: 'AFecha' },
            { data: 'MFecha' },
            {
                data: null,
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-danger delete-area" data-id="' + row.Id_Area + '"><i class="fas fa-trash"></i></button> ' + ' <button class="btn btn-sm btn-warning refresh-permissions" data-idplanta="{{$planta->Id_Planta}}" data-id="' + row.Id_Area + '"><i class="fas fa-sync-alt"></i></button>';
                },
                orderable: false,
                searchable: false
            },
            {
                data: null,
                render: function(data, type, row) {
                    return '<a href="#' + row.Id_Area + '" class="btn btn-info"><i class="fas fa-lock"></i> Permisos</a>';
                },
                orderable: false,
                searchable: false
            }
        ],
        responsive: true,
        scrollX: true,
        language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna de manera ascendente",
                sortDescending: ": activar para ordenar la columna de manera descendente"
            }
        }
    });
    // Manejar el envío del formulario para agregar un área
    $('#addAreaForm').on('submit', function(e) {
        e.preventDefault(); // Evitar el comportamiento por defecto del formulario

        var areaName = $('#areaName').val();
        var idPlanta = $('#idPlanta').val();

        $.ajax({
            url: '{{ url("/planta/areas/add") }}', // Ruta para agregar el área
            method: 'POST',
            data: {
                new_name: areaName,
                idPlanta: idPlanta,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: 'Área agregada exitosamente.',
                        icon: 'success'
                    }).then(() => {
                        $('#addAreaModal').modal('hide');
                        areasTable.ajax.reload(); // Recargar la tabla
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al agregar el área.',
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Error en la solicitud AJAX.',
                    icon: 'error'
                });
            }
        });
    });

    $('#areasTable').on('click', '.refresh-permissions', function() {
    var idArea = $(this).data('id');
    var idPlanta = $(this).data('idplanta');

    Swal.fire({
        title: 'Confirmar actualización',
        text: "¿Estás seguro de que deseas generar los permisos de artículos faltantes?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url("planta/areas/generate-permissions") }}', // Ajusta la URL según la ruta de tu controlador
                method: 'POST',
                data: {
                    id_area: idArea,
                    idPlanta : idPlanta,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Actualizado',
                            'Los permisos de artículos faltantes se han generado correctamente.',
                            'success'
                        );
                        areasTable.ajax.reload(); // Recargar la tabla si es necesario
                    } else {
                        Swal.fire(
                            'Error',
                            'No se pudieron generar los permisos de artículos faltantes.',
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error',
                        'Error en la solicitud AJAX.',
                        'error'
                    );
                }
            });
        }
    });
});

    // Manejar la eliminación de un área
     $('#areasTable').on('click', '.delete-area', function() {
        var idArea = $(this).data('id');

        Swal.fire({
            title: 'Confirmar eliminación',
            text: "¿Estás seguro de que deseas eliminar esta área?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("areas/delete") }}',
                    method: 'POST',
                    data: {
                        id_area: idArea,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Eliminado',
                                    'El área ha sido eliminada correctamente.',
                                    'success'
                                );
                                areasTable.ajax.reload(); // Recargar la tabla
                            } else {
                                Swal.fire(
                                    'Error',
                                    response.message,
                                    'error'
                                );
                            }
                    },
                    error: function() {
                        Swal.fire(
                            'Error',
                            'Error en la solicitud AJAX.',
                            'error'
                        );
                    }
                });
            }
        });
    });
    

    // Manejar el cambio entre Habilitar y Deshabilitar
    $('#enableEditBtn').on('click', function() {
        var isDisabled = $('.editable-name').first().prop('disabled');

        // Alternar el estado de los campos
        $('.editable-name').prop('disabled', !isDisabled);

        // Cambiar el texto y el ícono del botón
        if (isDisabled) {
            $(this).text('Deshabilitar Edición ');
            $(this).append('<i class="fas fa-edit"></i>');
            $(this).removeClass('btn-secondary').addClass('btn-danger');
        } else {
            $(this).text('Habilitar Edición ');
            $(this).append('<i class="fas fa-edit"></i>');
            $(this).removeClass('btn-danger').addClass('btn-secondary');
        }
    });

    // Manejar el cambio de estatus
    $('#areasTable').on('click', '.toggle-status', function() {
        var idArea = $(this).data('id');
        var newStatus = $(this).data('new-status');
        var idPlanta = $(this).data('idplanta');
       
        var button = $(this);

        Swal.fire({
            title: 'Confirmar cambio de estatus',
            text: "¿Estás seguro de que deseas cambiar el estatus a " + newStatus + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("/planta/areas/update-status") }}',
                    method: 'POST',
                    data: {
                        id_area: idArea,
                        new_status: newStatus,
                        idPlanta: idPlanta,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            button.text(newStatus);
                            button.toggleClass('btn-success btn-danger');
                            button.data('new-status', response.new_status === 'Alta' ? 'Baja' : 'Alta');
                            Swal.fire(
                                'Cambio realizado',
                                'El estatus se ha cambiado correctamente.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error',
                                'Error al cambiar el estatus.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error',
                            'Error en la solicitud AJAX.',
                            'error'
                        );
                    }
                });
            }
        });
    });

     
    // Manejar la edición de nombre
    $('#areasTable').on('change', '.editable-name', function() {
        var idArea = $(this).data('id');
        var newName = $(this).val();

        Swal.fire({
            title: 'Confirmar edición',
            text: "¿Estás seguro de que deseas cambiar el nombre a " + newName + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("areas/update-name") }}',
                    method: 'POST',
                    data: {
                        id_area: idArea,
                        new_name: newName,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (!response.success) {
                            Swal.fire(
                                'Error',
                                'Error al actualizar el nombre.',
                                'error'
                            );
                        } else {
                            Swal.fire(
                                'Actualizado',
                                'El nombre se ha actualizado correctamente.',
                                'success'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error',
                            'Error en la solicitud AJAX.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select3').select2({
            width: '100%' // Ajusta el ancho según sea necesario
        });

        $('#submitBtn').click(function(e) {
            e.preventDefault();

            let form = $('#addPermissionForm');
            let actionUrl = "{{ url('/planta/add-permission') }}";

            // Verificar si ya existe el permiso antes de enviar
            $.ajax({
                url: "{{ url('/planta/check-permission') }}", // Ruta que verifica duplicados
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if(response.exists) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Este artículo ya está registrado para esta área.',
                        });
                    } else {
                        // Si no existe el permiso, enviamos el formulario por AJAX
                        $.ajax({
                            url: actionUrl,
                            method: 'POST',
                            data: form.serialize(),
                            success: function(result) {
                                if(result.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: 'Permiso agregado exitosamente.',
                                    }).then(() => {
                                        // Actualizar la tabla de permisos sin recargar la página
                                        $('#permisos-articulos-table').DataTable().ajax.reload();
                                        $('#addPermissionModal').modal('hide'); // Cerrar el modal
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Hubo un problema al agregar el permiso.',
                                    });
                                }
                            }
                        });
                    }
                }
            });
        });
   
        // SCRIPT DE PERMISOS

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var Permisotable = $('#permisos-articulos-table').DataTable({
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        pageLength: 50, // Número predeterminado de filas a mostrar
        processing: true,
        serverSide: true,
        ajax: {
            url: '/planta/get-permisos-articulos/{{$planta->Id_Planta}}',
            type: 'POST',
            data: function (d) {
                d._token = '{{ csrf_token() }}';
            },
            dataSrc: function(json) {
                // Asegúrate de que el formato de datos es el esperado
                console.log(json);
                return json.data;
            }
        },
        columns: [
            { data: 'Nombre' },
            { data: 'Articulo' },
            { 
                data: 'Frecuencia',
                render: function(data, type, row) {
                    return `
                        <div  class="input-group">
                            <input type="number" max="360" min="0"  class="form-control update-frecuencia" data-id="${row.Clave}" value="${data}">
                            <div class="input-group-append">
                                <span class="input-group-text">Días</span>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'Cantidad',
                render: function(data, type, row) {
                    return `
                        <div class="input-group">
                            <input type="number" max="360" min="0"  class="form-control update-cantidad" data-id="${row.Clave}" value="${data}">
                            <div class="input-group-append">
                                <span class="input-group-text">Cant.</span>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'Estatus',
                render: function(data, type, row) {
                    var btnClass = data === 'Alta' ? 'btn-danger' : 'btn-success';
                    var btnText = data === 'Alta' ? 'Desactivar <i class="fas fa-lock"></i>' : '&nbsp;&nbsp;Activar&nbsp;&nbsp; <i class="fas fa-lock-open"></i>';
                    return `
                        <button class="btn btn-xs ${btnClass} toggle-status" data-id="${row.Clave}" data-status="${data}">
                            ${btnText}
                        </button>
                    `;
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false, 
                render: function(data, type, row) {
                    return `<button class="btn btn-danger btn-xs delete-btn" data-id="${row.Clave}">&nbsp;&nbsp;&nbsp; Eliminar &nbsp;&nbsp;&nbsp;<i class="fas fa-trash"></i></button>`;
                }
            }
        ],
            responsive: true,
            scrollX: true,
        language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            infoPostFix: "",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna de manera ascendente",
                sortDescending: ": activar para ordenar la columna de manera descendente"
            }
        }
    });

 // Función para eliminar un permiso
$('#permisos-articulos-table').on('click', '.delete-btn', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Una vez eliminado, no podrás recuperar este permiso.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminarlo',
        cancelButtonText: 'Cancelar',
        dangerMode: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/delete-permiso-articulo/${id}`,
                type: 'DELETE',
                success: function(result) {
                    $('#permisos-articulos-table').DataTable().ajax.reload(); // Actualiza la tabla
                    Swal.fire(
                        'Eliminado',
                        'Permiso eliminado con éxito',
                        'success'
                    );
                },
                error: function(xhr, status, error) {
                    Swal.fire(
                        'Error',
                        `Error eliminando el permiso: ${xhr.responseJSON.error || xhr.responseText}`,
                        'error'
                    );
                }
            });
        }
    });
});

// Función para cambiar el estado
$('#permisos-articulos-table').on('click', '.toggle-status', function() {
    var id = $(this).data('id');
    var status = $(this).data('status');
    var newStatus = status === 'Alta' ? 'Baja' : 'Alta';
    $.ajax({
        url: `/toggle-status-permiso-articulo/${id}`,
        type: 'POST',
        data: {
            status: newStatus
        },
        success: function(result) {
            $('#permisos-articulos-table').DataTable().ajax.reload(); // Actualiza la tabla
            Swal.fire(
                'Actualizado',
                'Estado actualizado con éxito',
                'success'
            );
        },
        error: function(xhr, status, error) {
            Swal.fire(
                'Error',
                `Error actualizando el estado: ${xhr.responseJSON.error || xhr.responseText}`,
                'error'
            );
        }
    });
});

// Función para actualizar la cantidad en tiempo real
$('#permisos-articulos-table').on('change', '.update-cantidad', function() {
    var id = $(this).data('id');
    var value = $(this).val();
    $.ajax({
        url: `/update-permiso-articulo/${id}`,
        type: 'POST',
        data: {
            field: 'Cantidad',
            value: value
        },
        success: function(result) {
            $('#permisos-articulos-table').DataTable().ajax.reload(); // Actualiza la tabla
            toastr.success('Cantidad actualizada con éxito');
        },
        error: function(xhr, status, error) {
            toastr.error(`Error actualizando la cantidad: ${xhr.responseJSON?.error || xhr.responseText}`);
        }
    });
});

// Función para actualizar la frecuencia en tiempo real
$('#permisos-articulos-table').on('change', '.update-frecuencia', function() {
    var id = $(this).data('id');
    var value = $(this).val();
    $.ajax({
        url: `/update-permiso-articulo/${id}`,
        type: 'POST',
        data: {
            field: 'Frecuencia',
            value: value
        },
        success: function(result) {
            $('#permisos-articulos-table').DataTable().ajax.reload(); // Actualiza la tabla
            toastr.success('Frecuencia actualizada con éxito');
        },
        error: function(xhr, status, error) {
            toastr.error(`Error actualizando la frecuencia: ${xhr.responseJSON?.error || xhr.responseText}`);
        }
    });
});

$('#areas,#empleados').on('click', '.btn-info', function(e) {
    e.preventDefault();

    // Obtener Id_Area desde el href del botón (ejemplo: "#28")
    var idArea = $(this).attr('href').replace('#', '');

    // Obtener Id_Planta desde la URL actual
    var urlActual = window.location.href;
    var idPlanta = urlActual.split('/').pop();

    console.log("ID de Planta:", idPlanta);
    console.log("ID de Área:", idArea);

    // Cambia al tab de permisos
    $('#linkpermiso').tab('show');
    $('#myTab .nav-link, #myTab .nav-item').removeClass('active');
    $('#linkpermiso').addClass('active').closest('.nav-item').addClass('active');
    $('.tab-pane').removeClass('active');
    $('#permisos').addClass('active');

     // Verifica si la DataTable ya está inicializada y la destruye
     if ($.fn.DataTable.isDataTable('#permisos-articulos-table')) {
        $('#permisos-articulos-table').DataTable().destroy();
        $('#permisos-articulos-table tbody').empty(); // Limpia la tabla
    }

    // Petición AJAX con el filtro de área
    $('#permisos-articulos-table').DataTable({
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        pageLength: 50, // Número predeterminado de filas a mostrar
        processing: true,
        serverSide: true,
        ajax: {
            url: `/admin/plantas/PlantaView/${idPlanta}/permisos/${idArea}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            dataSrc: function(json) {
                // Asegúrate de que el formato de datos es el esperado
                console.log(json);
                return json.data;
            }
        },
        columns: [
            { data: 'Nombre' },
            { data: 'Articulo' },
            { 
                data: 'Frecuencia',
                render: function(data, type, row) {
                    return `
                        <div  class="input-group">
                            <input type="number" max="360" min="0"  class="form-control update-frecuencia" data-id="${row.Clave}" value="${data}">
                            <div class="input-group-append">
                                <span class="input-group-text">Días</span>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'Cantidad',
                render: function(data, type, row) {
                    return `
                        <div class="input-group">
                            <input type="number" max="360" min="0"  class="form-control update-cantidad" data-id="${row.Clave}" value="${data}">
                            <div class="input-group-append">
                                <span class="input-group-text">Cant.</span>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'Estatus',
                render: function(data, type, row) {
                    var btnClass = data === 'Alta' ? 'btn-danger' : 'btn-success';
                    var btnText = data === 'Alta' ? 'Desactivar <i class="fas fa-lock"></i>' : '&nbsp;&nbsp;Activar&nbsp;&nbsp; <i class="fas fa-lock-open"></i>';
                    return `
                        <button class="btn btn-xs ${btnClass} toggle-status" data-id="${row.Clave}" data-status="${data}">
                            ${btnText}
                        </button>
                    `;
                }
            },
            { 
                data: null, 
                orderable: false, 
                searchable: false, 
                render: function(data, type, row) {
                    return `<button class="btn btn-danger btn-xs delete-btn" data-id="${row.Clave}">&nbsp;&nbsp;&nbsp; Eliminar &nbsp;&nbsp;&nbsp;<i class="fas fa-trash"></i></button>`;
                }
            }
        ],
            responsive: true,
            scrollX: true,
        language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            infoPostFix: "",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna de manera ascendente",
                sortDescending: ": activar para ordenar la columna de manera descendente"
            }
        }
    });
});
    
    var table = $('#empleados-table').DataTable({
            processing: true,
            serverSide: true,
                    ajax: { 
                    url: '/planta/empleados/data/{{$planta->Id_Planta}}',
                    type: 'POST',
                    data: function (d) {
                        d._token = '{{ csrf_token() }}';
                    },
                }
            columns: [
                { data: 'No_Empleado', name: 'No_Empleado' },
                { data: 'Nip', name: 'Nip' },
                { data: 'No_Tarjeta', name: 'No_Tarjeta' },
                { data: 'Nombre', name: 'Nombre' },
                { data: 'APaterno', name: 'APaterno' },
                { data: 'AMaterno', name: 'AMaterno', defaultContent: '' },
                { data: 'NArea', name: 'NArea' },
                {
                    data: null,
                    name: 'Permisos',
                    render: function(data, type, row) {
                        return `<a href="#" class="btn btn-xs btn-info">Permisos ... <i class="fas fa-user-tag"></i></a>`;
                    }
                },
                {
                    data: null,
                    name: 'Editar',
                    render: function(data, type, row) {
                        return `<button class="btn btn-xs btn-warning edit-btn" data-id="${row.Id_Empleado}" data-nip="${row.Nip}" data-notarjeta="${row.No_Tarjeta}" data-nombre="${row.Nombre}" data-apaterno="${row.APaterno}" data-amaterno="${row.AMaterno}" data-area="${row.Id_Area}">&nbsp;&nbsp; Editar &nbsp;&nbsp; <i class="fas fa-user-edit"></i></button>`;
                    }
                },
                {
                    data: null,
                    name: 'Eliminar',
                    render: function(data, type, row) {
                        return `<button class="btn btn-xs btn-danger" onclick="confirmDelete(${row.Id_Empleado}, '${row.Nombre} ${row.APaterno} ${row.AMaterno}')">Eliminar <i class="fas fa-trash"></i></button>`;
                    }
                },
                {
                    data: 'Txt_Estatus', 
                    name: 'Txt_Estatus',
                    render: function(data, type, row) {
                        var btnClass = row.Txt_Estatus === 'Alta' ? 'btn-danger' : 'btn-success';
                        var btnText = row.Txt_Estatus === 'Alta' ? 'Desactivar <i class="fas fa-lock"></i>' : '&nbsp;&nbsp;Activar&nbsp;&nbsp; <i class="fas fa-lock-open"></i>';
                        return `<button class="btn btn-xs ${btnClass}" onclick="toggleStatus(${row.Id_Empleado})">${btnText}</button>`;
                    }
                },
                { data: 'MFecha', name: 'MFecha' }
            ],
            responsive: true,
            scrollX: true,
        language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            infoPostFix: "",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna de manera ascendente",
                sortDescending: ": activar para ordenar la columna de manera descendente"
            }
        }
    
    });


        window.toggleStatus = function(employeeId) {
            $.ajax({
                url: `/empleado/toggle-status/${employeeId}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    table.ajax.reload(null, false); // false to keep current paging
                },
                error: function() {
                    alert('Error al cambiar el estado del empleado.');
                }
            });
        };

        window.confirmDelete = function(employeeId, employeeName) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `Se eliminará al empleado : [${employeeName}] `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/empleado/delete/${employeeId}`,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            table.ajax.reload(null, false); // false to keep current paging
                            Swal.fire(
                                'Eliminado!',
                                `El empleado ${employeeName} ha sido eliminado.`,
                                'success'
                            );
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'Hubo un problema al eliminar el empleado.',
                                'error'
                            );
                        }
                    });
                }
            });
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#addEmployeeForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '/planta/empleado/add', // Ajusta la URL a la ruta de tu controlador de Laravel
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#addEmployeeModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Empleado agregado correctamente.',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                    });
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';
                for (var key in errors) {
                    errorMessage += errors[key] + '\n';
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                });
            }
        });
    });

        // Manejo del formulario de edición
        $('#editForm').on('submit', function(e) {
            e.preventDefault();

            var id = $('#editId').val();
            var nip = $('#nip').val();
            var notarjeta = $('#notarjeta').val();
            var nombre = $('#nombre').val();
            var apaterno = $('#apaterno').val();
            var amaterno = $('#amaterno').val();
            var area = $('#area').val();

            $.ajax({
                url: `/empleado/update/${id}`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    id:id,
                    nip: nip,
                    notarjeta: notarjeta,
                    nombre: nombre,
                    apaterno: apaterno,
                    amaterno: amaterno,
                    area: area
                },
                success: function(result) {
                    table.ajax.reload(); // Recarga la tabla después de actualizar el empleado
                    $('#editModal').modal('hide');
                    Swal.fire('Éxito', 'Empleado actualizado con éxito.', 'success');
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Hubo un error al actualizar el empleado: ' + xhr.responseJSON.message, 'error');
                    console.log(xhr.responseJSON.errors); 
                }
            });
        });

        // Función para abrir el modal de edición
        $('#empleados-table').on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var nip = $(this).data('nip');
            var notarjeta = $(this).data('notarjeta');
            var nombre = $(this).data('nombre');
            var apaterno = $(this).data('apaterno');
            var amaterno = $(this).data('amaterno');
            var area = $(this).data('area');

            openEditModal(id, nip, notarjeta, nombre, apaterno, amaterno, area);
        });

       // Detectar el clic en el botón de permisos
       


});



    

    function openEditModal(id, nip, notarjeta, nombre, apaterno, amaterno, idArea) {
        // Llenar el formulario con los datos del empleado
        $('#editId').val(id);
        $('#nip').val(nip);
        $('#notarjeta').val(notarjeta);
        $('#nombre').val(nombre);
        $('#apaterno').val(apaterno);
        $('#amaterno').val(amaterno);

        // Cargar opciones de áreas en el select
        $.ajax({
            url: '{!! route('areas.data') !!}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
            console.log('Datos de áreas recibidos:', data); // Verificar datos recibidos
            var $areaSelect = $('#area');
            $areaSelect.empty(); // Limpiar opciones existentes

            // Convertir idArea a cadena para comparación
            var idAreaStr = idArea.toString();

            // Verificar que el área actual del empleado exista en los datos recibidos
            var currentArea = data.find(area => area.Id_Area === idAreaStr);
            console.log('Área actual del empleado:', currentArea); // Verificar área actual
            if (currentArea) {
                // Agregar el área actual del empleado como la primera opción
                $areaSelect.append(`<option value="${currentArea.Id_Area}" selected>${currentArea.Txt_Nombre}</option>`);
                // Eliminar el área actual de las opciones restantes
                data = data.filter(area => area.Id_Area !== idAreaStr);
            }

            // Agregar las demás áreas
            data.forEach(area => {
                $areaSelect.append(`<option value="${area.Id_Area}">${area.Txt_Nombre}</option>`);
            });
        },
        error: function(xhr) {
            console.error('Error al cargar las áreas:', xhr.responseText);
        }
    });

        $('#editModal').modal('show');
    }
</script>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('status'))
                let status = "{{ session('status') }}";
                let message = "{{ session('message') }}";

                Swal.fire({
                    icon: status,
                    title: status === 'success' ? 'Éxito' : 'Error',
                    html: message,
                    confirmButtonText: 'Aceptar'
                });
            @endif
        });
    </script>

<script>
    function goBack() {
      window.history.back();
    }
</script>
@stop
