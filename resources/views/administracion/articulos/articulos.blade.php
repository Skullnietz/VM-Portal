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
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel"
    aria-hidden="true">
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
                        <label for="Txt_Nombre_Etiqueta">Nombre Etiqueta</label>
                        <input type="text" class="form-control" id="Txt_Nombre_Etiqueta" name="Nombre_Etiqueta">
                    </div>
                    <div class="form-group">
                        <label for="Txt_Codigo">Código</label>
                        <input type="text" class="form-control" id="Txt_Codigo" name="Txt_Codigo" required>
                    </div>
                    <div class="form-group">
                        <label for="Txt_Tamano_Espiral">Tamaño Espiral</label>
                        <select class="form-control" id="Txt_Tamano_Espiral" name="Tamano_Espiral" required>
                            <option value="">Seleccione</option>
                            <option value="Chico">Chico</option>
                            <option value="Grande">Grande</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Txt_Capacidad_Espiral">Capacidad Espiral</label>
                        <input type="number" class="form-control" id="Txt_Capacidad_Espiral" name="Capacidad_Espiral"
                            required min="5" max="24">
                    </div>
                    <div class="form-group">
                        <label>Imagen del Artículo</label>
                        <div class="drop-zone" id="addDropZone">
                            <span class="drop-zone__prompt">Arrastra el archivo aquí o haz clic para subir</span>
                            <input type="file" name="image" class="drop-zone__input" accept="image/*">
                        </div>
                    </div>
                    <center>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </center>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal para editar artículo -->
<div class="modal fade" id="editArticleModal" tabindex="-1" role="dialog" aria-labelledby="editArticleModalLabel"
    aria-hidden="true">
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
                        <label for="editNombreEtiqueta">Nombre Etiqueta</label>
                        <input type="text" class="form-control" id="editNombreEtiqueta" name="Nombre_Etiqueta">
                    </div>
                    <div class="form-group">
                        <label for="editCodigo">Código</label>
                        <input type="text" class="form-control" id="editCodigo" name="Txt_Codigo" required>
                    </div>
                    <div class="form-group">
                        <label for="editTamanoEspiral">Tamaño Espiral</label>
                        <select class="form-control" id="editTamanoEspiral" name="Tamano_Espiral" required>
                            <option value="">Seleccione</option>
                            <option value="Chico">Chico</option>
                            <option value="Grande">Grande</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editCapacidadEspiral">Capacidad Espiral</label>
                        <input type="number" class="form-control" id="editCapacidadEspiral" name="Capacidad_Espiral"
                            required min="5" max="24">
                    </div>
                    <div class="form-group">
                        <label>Imagen del Artículo</label>
                        <div class="drop-zone" id="editDropZone">
                            <span class="drop-zone__prompt">Arrastra el archivo aquí o haz clic para subir</span>
                            <input type="file" name="image" class="drop-zone__input" accept="image/*">
                        </div>
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
                                <th>Nombre Etiqueta</th>
                                <th>Código</th>
                                <th>Tamaño Espiral</th>
                                <th>Capacidad Espiral</th>
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
                                <th><input type="text" id="filterNombreEtiqueta" class="form-control" placeholder="Buscar Etiqueta"></th>
                                <th><input type="text" id="filterCodigo" class="form-control" placeholder="Buscar Código"></th>
                                <th><input type="text" id="filterTamanoEspiral" class="form-control" placeholder="Buscar Tamaño"></th>
                                <th><input type="text" id="filterCapacidadEspiral" class="form-control" placeholder="Buscar Capacidad"></th>
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
<link rel="stylesheet" type="text/css"
    href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
<style>
    .drop-zone {
        max-width: 100%;
        height: 200px;
        padding: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-family: "Quicksand", sans-serif;
        font-weight: 500;
        font-size: 20px;
        cursor: pointer;
        color: #cccccc;
        border: 4px dashed #009578;
        border-radius: 10px;
    }

    .drop-zone--over {
        border-style: solid;
    }

    .drop-zone__input {
        display: none;
    }

    .drop-zone__thumb {
        width: 100%;
        height: 100%;
        border-radius: 10px;
        overflow: hidden;
        background-color: #cccccc;
        background-size: cover;
        position: relative;
    }

    .drop-zone__thumb::after {
        content: attr(data-label);
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 5px 0;
        color: #ffffff;
        background: rgba(0, 0, 0, 0.75);
        font-size: 14px;
        text-align: center;
    }
</style>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8"
    src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        const table = $('#articulosTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/articulos-datatable',
                type: 'POST',
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr('content');
                    d.descripcion = $('#filterDescripcion').val();
                    d.nombreEtiqueta = $('#filterNombreEtiqueta').val();
                    d.codigo = $('#filterCodigo').val();
                    d.tamanoEspiral = $('#filterTamanoEspiral').val();
                    d.capacidadEspiral = $('#filterCapacidadEspiral').val();
                }
            },
            responsive: false,
            scrollX: true,
            scrollY: "400px",
            scrollCollapse: true,
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            columns: [
                {
                    data: 'Imagen',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const timestamp = new Date().getTime();
                        return `<img src="/Images/Catalogo/${row.Txt_Codigo}.jpg?t=${timestamp}" 
                            alt="Imagen" 
                            width="50" 
                            height="50"
                            onerror="this.onerror=null;this.src='/Images/product.png';">`;
                    }
                },
                // AQUI SÍ agregamos name para las columnas que vienen de la DB
                { data: 'Txt_Descripcion', name: 'ca.Txt_Descripcion' },
                { data: 'Nombre_Etiqueta', name: 'ca.Nombre_Etiqueta', defaultContent: '' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<button class="btn btn-success btn-sm">
                            ${row.Txt_Codigo}
                        </button>`;
                    }
                },
                { data: 'Tamano_Espiral', name: 'ca.Tamano_Espiral' },
                { data: 'Capacidad_Espiral', name: 'ca.Capacidad_Espiral' },
                { data: 'Txt_Estatus', name: 'ca.Txt_Estatus', orderable: false },
                { data: 'Fecha_Alta', name: 'ca.Fecha_Alta' },
                { data: 'UsuarioAlta', name: 'UsuarioAlta', searchable: false }, // viene de DB::raw
                { data: 'Fecha_Modificacion', name: 'ca.Fecha_Modificacion' },
                { data: 'UsuarioModificacion', name: 'UsuarioModificacion', searchable: false }, // DB::raw
                { data: 'Fecha_Baja', name: 'ca.Fecha_Baja' },
                { data: 'UsuarioBaja', name: 'UsuarioBaja', searchable: false }, // DB::raw
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
                        <button class="btn btn-danger btn-sm delete-article" data-id="${row.Id_Articulo}">
                            <i class="fa fa-trash"></i> Eliminar
                        </button>
                    </div>
                `;
                    }
                }
            ]
        });

        $('#filterDescripcion, #filterNombreEtiqueta, #filterCodigo, #filterTamanoEspiral, #filterCapacidadEspiral').on('keyup', function () {
            table.ajax.reload();
        });

        $('#articulosTable').on('click', '.edit-article', function () {
            const articleId = $(this).data('id');
            $.ajax({
                url: `/articulos/${articleId}/edit`,
                type: 'GET',
                success: function (response) {
                    $('#editArticleId').val(response.Id_Articulo);
                    $('#editDescripcion').val(response.Txt_Descripcion);
                    $('#editNombreEtiqueta').val(response.Nombre_Etiqueta);
                    $('#editCodigo').val(response.Txt_Codigo);
                    $('#editTamanoEspiral').val(response.Tamano_Espiral);
                    $('#editCapacidadEspiral').val(response.Capacidad_Espiral);
                    $('#editArticleModal').modal('show');
                },
                error: function () {
                    alert('Error al cargar los datos del artículo.');
                }
            });
        });

        $('#editArticleForm').on('submit', function (e) {
            e.preventDefault();
            const articleId = $('#editArticleId').val();
            // Usamos FormData para enviar archivos
            const formData = new FormData(this);
            $.ajax({
                url: `/articulos/${articleId}/update`,
                type: 'POST',
                data: formData,
                processData: false, // Importante para enviar archivos
                contentType: false, // Importante para enviar archivos
                success: function (response) {
                    alert(response.message);
                    $('#editArticleModal').modal('hide');
                    table.ajax.reload();
                },
                error: function () {
                    alert('Error al actualizar el artículo.');
                }
            });
        });

        $('#articulosTable').on('click', '.delete-article', function () {
            const articleId = $(this).data('id');
            if (confirm('¿Estás seguro de que deseas eliminar este artículo?')) {
                $.ajax({
                    url: `/articulos/${articleId}/delete`,
                    type: 'DELETE',
                    success: function (response) {
                        alert(response.message);
                        table.ajax.reload();
                    },
                    error: function () {
                        alert('Error al eliminar el artículo.');
                    }
                });
            }
        });

        $('#articulosTable').on('click', '.toggle-status', function () {
            var button = $(this);
            var articuloId = button.data('id');
            var currentStatus = button.data('status');
            var newStatus = (currentStatus === 'Alta') ? 'Baja' : 'Alta';
            if (confirm(`¿Deseas cambiar el estado de este artículo a ${newStatus}?`)) {
                $.ajax({
                    url: '/cambiar-estatus-articulo',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: articuloId,
                        status: newStatus
                    },
                    success: function (response) {
                        if (response.success) {
                            button.removeClass(currentStatus === 'Alta' ? 'btn-success' : 'btn-danger')
                                .addClass(newStatus === 'Alta' ? 'btn-success' : 'btn-danger')
                                .text(newStatus === 'Alta' ? 'Habilitado' : 'Deshabilitado')
                                .data('status', newStatus);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('Hubo un error en la comunicación con el servidor.');
                    }
                });
            }
        });

        $('#addArticleForm').on('submit', function (e) {
            e.preventDefault();
            // Usamos FormData para enviar archivos
            var formData = new FormData(this);
            $.ajax({
                url: '/articulos/store',
                method: 'POST',
                data: formData,
                processData: false, // Importante para enviar archivos
                contentType: false, // Importante para enviar archivos
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        $('#addPermissionModal').modal('hide');
                        table.ajax.reload();
                    } else {
                        alert(response.message || 'Hubo un error al agregar el artículo.');
                    }
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert('Ocurrió un error inesperado.');
                    }
                }
            });
        });
    });

    function goBack() {
        window.history.back();
    }

    // Drag and Drop Logic
    document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
        const dropZoneElement = inputElement.closest(".drop-zone");

        dropZoneElement.addEventListener("click", (e) => {
            inputElement.click();
        });

        inputElement.addEventListener("change", (e) => {
            if (inputElement.files.length) {
                updateThumbnail(dropZoneElement, inputElement.files[0]);
            }
        });

        dropZoneElement.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropZoneElement.classList.add("drop-zone--over");
        });

        ["dragleave", "dragend"].forEach((type) => {
            dropZoneElement.addEventListener(type, (e) => {
                dropZoneElement.classList.remove("drop-zone--over");
            });
        });

        dropZoneElement.addEventListener("drop", (e) => {
            e.preventDefault();

            if (e.dataTransfer.files.length) {
                inputElement.files = e.dataTransfer.files;
                updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
            }

            dropZoneElement.classList.remove("drop-zone--over");
        });
    });

    function updateThumbnail(dropZoneElement, file) {
        let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");

        // First time - remove the prompt
        if (dropZoneElement.querySelector(".drop-zone__prompt")) {
            dropZoneElement.querySelector(".drop-zone__prompt").remove();
        }

        // First time - there is no thumbnail element, so lets create it
        if (!thumbnailElement) {
            thumbnailElement = document.createElement("div");
            thumbnailElement.classList.add("drop-zone__thumb");
            dropZoneElement.appendChild(thumbnailElement);
        }

        thumbnailElement.dataset.label = file.name;

        // Show thumbnail for image files
        if (file.type.startsWith("image/")) {
            const reader = new FileReader();

            reader.readAsDataURL(file);
            reader.onload = () => {
                thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
            };
        } else {
            thumbnailElement.style.backgroundImage = null;
        }
    }
</script>
@stop