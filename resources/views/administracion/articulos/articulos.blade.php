@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Articulos'))

@section('content_header')
    <div class="container">
        <div class="row">
            <div class=" col-md-9 col-9">
                <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                            class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Articulos Urvina') }}</h4>
            </div>
            <div class="col text-right">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <!-- Botón para abrir el modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPermissionModal">
    Agregar Artículo &nbsp;&nbsp;&nbsp;<i class="fas fa-box"></i>
</button>


                       
                    </div>
                </div>



        </div>
    </div>
    <!-- Modal para agregar artículo -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPermissionModalLabel">Agregar Artículo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addArticleForm">
                    @csrf
                    <div class="form-group">
                        <label for="Txt_Descripcion">Descripción</label>
                        <input type="text" class="form-control" id="Txt_Descripcion" name="Txt_Descripcion" required>
                    </div>
                    <div class="form-group">
                        <label for="Txt_Codigo">Código</label>
                        <input type="text" class="form-control" id="Txt_Codigo" name="Txt_Codigo" required>
                    </div>
                    <div class="form-group">
                        <label for="Txt_Codigo_Cliente">Código Cliente</label>
                        <input type="text" class="form-control" id="Txt_Codigo_Cliente" name="Txt_Codigo_Cliente" required>
                    </div><center>
                    <button type="submit" class="btn btn-success">Guardar</button>
                    </center>
                    
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal para editar artículo -->
<div class="modal fade" id="editArticleModal" tabindex="-1" role="dialog" aria-labelledby="editArticleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editArticleModalLabel">Editar Artículo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editArticleForm">
                <div class="modal-body">
                    <input type="hidden" id="editArticleId" name="Id_Articulo">

                    <div class="form-group">
                        <label for="editDescripcion">Descripción</label>
                        <input type="text" class="form-control" id="editDescripcion" name="Txt_Descripcion" required>
                    </div>

                    <div class="form-group">
                        <label for="editCodigo">Código</label>
                        <input type="text" class="form-control" id="editCodigo" name="Txt_Codigo" required>
                    </div>

                    <div class="form-group">
                        <label for="editCodigoCliente">Código Cliente</label>
                        <input type="text" class="form-control" id="editCodigoCliente" name="Txt_Codigo_Cliente">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
            
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title">
                            Lista de Articulos de Asignación
                        </h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a href="#" class="dropdown-item">Action</a>
                                    <a href="#" class="dropdown-item">Another action</a>
                                    <a href="#" class="dropdown-item">Something else here</a>
                                    <a class="dropdown-divider"></a>
                                    <a href="#" class="dropdown-item">Separated link</a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                    </div>
                    <div class="card-body">
                    <table id="articulosTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Imagen</th>
            <th>Descripción</th>
            <th>Código</th>
            <th>Código Cliente</th>
            <th>Estatus</th>
            <th>Fecha Alta</th>
            <th>Usuario Alta</th>
            <th>Fecha Modificación</th>
            <th>Usuario Modificación</th>
            <th>Fecha Baja</th>
            <th>Usuario Baja</th>
            <th>Acciones</th>
        </tr>
        <tr id="filters">
            <th></th>
            <th><input type="text" id="filterDescripcion" class="form-control" placeholder="Buscar Descripción"></th>
            <th><input type="text" id="filterCodigo" class="form-control" placeholder="Buscar Código"></th>
            <th><input type="text" id="filterCodigoCliente" class="form-control" placeholder="Buscar Código Cliente"></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
</table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('right-sidebar')
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">

@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script>
    $(document).ready(function () {
        $.ajaxSetup({
    headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const table = $('#articulosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/articulos-datatable',
            data: function (d) {
                // Pasar los valores de los inputs como parámetros
                d.descripcion = $('#filterDescripcion').val();
                d.codigo = $('#filterCodigo').val();
                d.codigo_cliente = $('#filterCodigoCliente').val();
            }
        },
        responsive: false, // Se desactiva "responsive" para usar scroll
        scrollX: true, // Habilita desplazamiento horizontal
        scrollY: "400px", // Altura del área visible
        scrollCollapse: true, // Permite que la tabla colapse si hay pocas filas
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-MX.json'
        },
        columns: [
            {
                data: 'Imagen',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `<img src="https://172.31.1.1/imagenes/Catalogo/${row.Txt_Codigo}.jpg" 
                                alt="Imagen" 
                                width="50" 
                                height="50" 
                                onerror="this.onerror=null;this.src='/Images/product.png';">`;
                }
            },
            { data: 'Txt_Descripcion' },
            { data: 'Txt_Codigo' },
            { data: 'Txt_Codigo_Cliente' },
            { data: 'Txt_Estatus', orderable: false },
            { data: 'Fecha_Alta' },
            { data: 'UsuarioAlta' },
            { data: 'Fecha_Modificacion' },
            { data: 'UsuarioModificacion' },
            { data: 'Fecha_Baja' },
            { data: 'UsuarioBaja' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-warning btn-sm edit-article" data-id="${row.Id_Articulo}" data-toggle="modal" data-target="#editArticleModal">
                                <i class="fa fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-danger  btn-sm delete-article" data-id="${row.Id_Articulo}">
                                <i class="fa fa-trash"></i> Eliminar
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // Eventos para los filtros
    $('#filterDescripcion, #filterCodigo, #filterCodigoCliente').on('keyup', function () {
        table.ajax.reload();
    });


     // Manejo del botón Editar
    $('#articulosTable').on('click', '.edit-article', function () {
        const articleId = $(this).data('id');

        // Realizar solicitud AJAX para obtener los datos del artículo
        $.ajax({
            url: `/articulos/${articleId}/edit`,
            type: 'GET',
            success: function (response) {
                // Rellenar el formulario del modal con los datos recibidos
                $('#editArticleId').val(response.Id_Articulo);
                $('#editDescripcion').val(response.Txt_Descripcion);
                $('#editCodigo').val(response.Txt_Codigo);
                $('#editCodigoCliente').val(response.Txt_Codigo_Cliente);

                // Mostrar el modal
                $('#editArticleModal').modal('show');
            },
            error: function () {
                alert('Error al cargar los datos del artículo.');
            }
        });
    });

    // Guardar los cambios al enviar el formulario
    $('#editArticleForm').on('submit', function (e) {
        e.preventDefault();

        const articleId = $('#editArticleId').val();
        const formData = $(this).serialize();

        // Realizar solicitud AJAX para actualizar los datos
        $.ajax({
            url: `/articulos/${articleId}/update`,
            type: 'POST',
            data: formData,
            success: function (response) {
                alert(response.message);
                $('#editArticleModal').modal('hide');
                $('#articulosTable').DataTable().ajax.reload(); // Recargar la tabla
            },
            error: function () {
                alert('Error al actualizar el artículo.');
            }
        });
    });

    // Manejo de botones de "Eliminar"
    $('#articulosTable').on('click', '.delete-article', function () {
        const articleId = $(this).data('id');
        if (confirm('¿Estás seguro de que deseas eliminar este artículo?')) {
            // Realizar la solicitud AJAX para eliminar el artículo
            $.ajax({
                url: `/articulos/${articleId}/delete`,
                type: 'DELETE',
                success: function (response) {
                    alert(response.message);
                    $('#articulosTable').DataTable().ajax.reload(); // Recargar la tabla
                },
                error: function (xhr) {
                    alert('Error al eliminar el artículo.');
                }
            });
        }
    });

        // Manejar el clic en los botones de estado
        $('#articulosTable').on('click', '.toggle-status', function () {
            var button = $(this);
            var articuloId = button.data('id');
            var currentStatus = button.data('status');
            var newStatus = (currentStatus === 'Alta') ? 'Baja' : 'Alta'; // Alternar el estado

            // Confirmar con el usuario antes de hacer el cambio
            if (confirm(`¿Deseas cambiar el estado de este artículo a ${newStatus}?`)) {
                // Realizar la llamada AJAX para actualizar el estado
                $.ajax({
                    url: '/cambiar-estatus-articulo', // Ruta del controlador que maneja el cambio
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // Token CSRF para la seguridad
                        id: articuloId,
                        status: newStatus
                    },
                    success: function (response) {
                        // Si el cambio es exitoso, actualizar el botón
                        if (response.success) {
                            button.removeClass(currentStatus === 'Alta' ? 'btn-success' : 'btn-danger')
                                  .addClass(newStatus === 'Alta' ? 'btn-success' : 'btn-danger')
                                  .text(newStatus === 'Alta' ? 'Habilitado' : 'Deshabilitado')
                                  .data('status', newStatus);
                        } else {
                            alert(response.message); // Mostrar el mensaje de error
                        }
                    },
                    error: function () {
                        alert('Hubo un error en la comunicación con el servidor.');
                    }
                });
            }
        });

        $('#addArticleForm').on('submit', function (e) {
        e.preventDefault(); // Prevenir el comportamiento por defecto del formulario

        // Obtener los datos del formulario
        var formData = $(this).serialize();

        $.ajax({
    url: '/articulos/store',  // Ruta del controlador
    method: 'POST',
    data: formData,
    success: function (response) {
        if (response.success) {
            alert(response.message);  // Mostrar mensaje de éxito
            $('#addPermissionModal').modal('hide');  // Cerrar el modal
            // Aquí puedes recargar la tabla o realizar otra acción
        } else {
            alert(response.message || 'Hubo un error al agregar el artículo.');
        }
    },
    error: function (xhr) {
        // Capturar y mostrar el mensaje del servidor
        if (xhr.responseJSON && xhr.responseJSON.message) {
            alert(xhr.responseJSON.message);  // Mostrar mensaje del backend
        } else {
            alert('Ocurrió un error inesperado.');
        }
    }
});
    });
    });
</script>
<script>
    function goBack() {
      window.history.back();
    }
</script>
@stop
