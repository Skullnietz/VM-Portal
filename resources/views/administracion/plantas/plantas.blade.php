@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Plantas'))

@section('content_header')
    <div class="container">
        <div class="row">
            <div class="col text-left">
                <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                            class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Plantas') }}</h4>
            </div>
            <div class="col text-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAdminModal">
                Agregar Planta&nbsp;&nbsp;&nbsp;<i class="fas fa-fw fa-industry"></i>
            </button>
        </div>
        </div>
    </div>
@stop



@section('content')
<!-- Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-labelledby="addAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdminModalLabel">Agregar/Editar Planta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createPlantForm" method="POST" enctype="multipart/form-data">
                    @csrf <!-- Token CSRF -->
                    <input type="hidden" id="plantId" name="plantId" value=""> <!-- Campo oculto para ID de la planta -->
                    
                    <div class="form-group">
                        <label for="txtNombrePlanta">Nombre de Planta</label>
                        <input type="text" class="form-control" id="txtNombrePlanta" name="txtNombrePlanta" required>
                    </div>
                    <div class="form-group">
                        <label for="txtCodigoCliente">Código Cliente</label>
                        <input type="text" class="form-control" id="txtCodigoCliente" name="txtCodigoCliente" required>
                    </div>
                    <div class="form-group">
                        <label for="txtSitio">Sitio</label>
                        <input type="text" class="form-control" id="txtSitio" name="txtSitio" required>
                    </div>
                    <div class="form-group">
                    <label for="imageUpload">Cargar Imagen (PNG)</label>
                    <div id="drop-area" class="border border-dashed p-4 text-center">
                        <p>Arrastra y suelta tu imagen aquí o haz clic para seleccionar</p>
                        <!-- Input de tipo file para cargar la imagen -->
                        <input type="file" id="imageUpload" name="image" accept=".png" style="display: none;" onchange="handleFiles(this.files)">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('imageUpload').click();">Seleccionar Imagen</button>
                    </div>
                    <div id="imagePreview" class="mt-2"></div>
                    <div id="imageStatus" class="mt-2 text-success" style="display: none;">Imagen cargada correctamente.</div>
                </div>
                </form>
                <div id="loadingSpinner" style="display: none;">
    <i class="fas fa-spinner fa-spin"></i> Cargando...
</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="saveAdmin" onclick="submitForm()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title">
                            Tabla de Plantas
                        </h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                    </div>
                    <div class="card-body">
                        <table id="plantasTable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre Planta</th>
                                    <th>Codigo Cliente</th>
                                    <th>Sitio</th>
                                    <th>Estatus</th>
                                    <th>Opciones</th>
                                    <th>Usuario Alta</th>
                                    <th>Fecha Alta</th>
                                    <th>Usuario Modificación</th>
                                    <th>Fecha Modificación</th>
                                    <th>Usuario Baja</th>
                                    <th>Fecha Baja</th>
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
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
    <!-- Incluir CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .alta {
            background-color: #d4edda; /* Verde */
            color: #155724;
        }

        .modificacion {
            background-color: #fff3cd; /* Amarillo */
            color: #856404;
        }

        .baja {
            background-color: #f8d7da; /* Rojo */
            color: #721c24;
        }
        #drop-area {
        border: 2px dashed #007bff;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        }
        #drop-area.highlight {
            border-color: #0056b3;
        }
    </style>
    
@stop

@section('js')
<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Incluir JavaScript de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
    $(document).ready(function () {
        $('#plantasTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("getPlantasInfo") }}',
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        // Verificar si hay una imagen en la propiedad Ruta_Imagen del objeto row
                        const imagen = row.Ruta_Imagen ? row.Ruta_Imagen : 'https://cdn-icons-png.flaticon.com/512/72/72734.png';
                        
                        return `
                            <div>
                                <img src="${imagen}" alt="Imagen" style="width: 50px; height: 50px;" />
                            </div>
                            
                        `;
                    }
                },
                { data: 'Txt_Nombre_Planta', name: 'Txt_Nombre_Planta' },
                { data: 'Txt_Codigo_Cliente', name: 'Txt_Codigo_Cliente' },
                { data: 'Txt_Sitio', name: 'Txt_Sitio' },
                {
                    data: 'estatus_icon',
                    name: 'estatus_icon',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        let iconClass = row.Txt_Estatus === 'Alta' ? 'fa-toggle-on fa-2x text-success' : 'fa-toggle-off fa-2x text-danger';
                        let nuevoEstatus = row.Txt_Estatus === 'Alta' ? 'Baja' : 'Alta';

                        return `
                            <i id="estatus-icon-${row.id}" 
                                class="fas ${iconClass}" 
                                style="cursor: pointer;"
                                onclick="toggleEstatus(${row.id}, '${nuevoEstatus}')"></i>
                        `;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <a class="btn btn-secondary btn-sm" href="/admin/plantas/PlantaView/${row.id}" title="Mostrar Planta">
                                    <i class="fas fa-eye fa-2x"></i>
                                </a>
                                <button class="btn btn-info btn-sm" onclick="editAdmin(${row.id}, '${row.Txt_Nombre_Planta}', '${row.Txt_Codigo_Cliente}', '${row.Txt_Sitio}')" title="Editar">
                                    <i class="fas fa-edit fa-2x"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteAdmin(${row.id})" title="Eliminar">
                                    <i class="fas fa-trash fa-2x"></i>
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="releaseRelatedRecords(${row.id})" title="Liberar registros">
                                    <i class="fas fa-folder-open fa-2x"></i>
                                </button>
                            </div>
                        `;
                    }
                },
                { data: 'UsuarioAlta', name: 'UsuarioAlta' },
                { data: 'Fecha_Alta', name: 'Fecha_Alta' },
                { data: 'UsuarioModificacion', name: 'UsuarioModificacion' },
                { data: 'Fecha_Modificacion', name: 'Fecha_Modificacion' },
                { data: 'UsuarioBaja', name: 'UsuarioBaja' },
                { data: 'Fecha_Baja', name: 'Fecha_Baja' }
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).find('td').eq(5).addClass('alta');
                $(row).find('td').eq(7).addClass('modificacion');
                $(row).find('td').eq(9).addClass('baja');
            },
            order: [[0, 'asc']],
            responsive: true,
            scrollX: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            }
        });

        // Inicializa Select2 para el modal de agregar administrador
        $('#addAdminModal').on('shown.bs.modal', function () {
            $('#planta').select2({
                placeholder: 'Seleccione una planta',
                allowClear: true,
                dropdownParent: $('#addAdminModal')
            });
        });

        // Cargar las plantas en el select2
        $.ajax({
            url: '/getPlantas',
            method: 'GET',
            success: function (data) {
                $.each(data, function (index, planta) {
                    $('#planta').append(new Option(planta.Txt_Nombre_Planta, planta.Id_Planta));
                });
                $('#planta').select2('destroy').select2({
                    placeholder: 'Seleccione una planta',
                    allowClear: true
                });
            },
            error: function (xhr) {
                console.error('Error al cargar las plantas:', xhr);
            }
        });

        
    });

    // Función para limpiar la vista previa de la imagen
function resetImagePreview() {
    const imagePreview = document.getElementById('imagePreview');
    const imageStatus = document.getElementById('imageStatus');
    const imageUpload = document.getElementById('imageUpload');

    imagePreview.innerHTML = '';
    imageStatus.style.display = 'none';
    imageUpload.value = '';
}

    function deleteAdmin(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta planta?')) {
            $.ajax({
                url: `/planta/${id}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    alert('Planta eliminada exitosamente');
                    $('#plantasTable').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    alert('Error al eliminar la planta: ' + xhr.responseJSON.message);
                }
            });
        }
    }

    function submitForm() {
        

        let plantId = $('#plantId').val();
        let url = plantId ? '{{ route("updatePlanta") }}' : '{{ route("guardarPlanta") }}';

        let formData = new FormData(document.getElementById('createPlantForm'));
        formData.append('file', imageUpload.files[0]);

        // Mostrar el spinner de carga
        $('#loadingSpinner').show();

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $('#addAdminModal').modal('hide');
                $('#plantasTable').DataTable().ajax.reload(null, false);
                alert(response.message);
            },
            error: function (xhr) {
                alert('Error: ' + xhr.responseText);
            },
            complete: function() {
                // Ocultar el spinner de carga una vez completada la petición
                $('#loadingSpinner').hide();
            }
        });
    }

    $('#addAdminModal').on('hidden.bs.modal', function () {
    // Limpiar los campos del formulario cuando el modal se cierra
    resetImagePreview(); // Limpiar la imagen previa
    $('#plantId').val('');
    $('#txtNombrePlanta').val('');
    $('#txtCodigoCliente').val('');
    $('#txtSitio').val('');
    });

    function editAdmin(id, nombrePlanta, codigoCliente, sitio, rutaImagen) {
    $('#plantId').val(id);
    $('#txtNombrePlanta').val(nombrePlanta);
    $('#txtCodigoCliente').val(codigoCliente);
    $('#txtSitio').val(sitio);
    
    // Si existe imagen, mostrarla, de lo contrario, mantener la imagen por defecto
    if (rutaImagen) {
        $('#imagePreview').html(`<img src="${rutaImagen}" alt="Vista previa" class="img-fluid rounded mt-2" />`);
    } else {
        resetImagePreview(); // Limpiar cualquier imagen previa
    }

    $('#addAdminModal').modal('show');
}

    function releaseRelatedRecords(plantId) {
        if (!plantId) {
            alert('Error: No se pudo obtener el ID de la planta.');
            return;
        }

        if (confirm("¿Estás seguro de que quieres liberar todos los registros relacionados con esta planta?")) {
            $.ajax({
                url: '/planta/release/' + plantId,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message);
                    $('#plantasTable').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    alert("Error: " + xhr.responseJSON.message);
                }
            });
        }
    }

    const dropArea = document.getElementById('drop-area');
    const imagePreview = document.getElementById('imagePreview');
    const imageStatus = document.getElementById('imageStatus');
    const imageUpload = document.getElementById('imageUpload');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    dropArea.addEventListener('drop', handleDrop, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() {
        dropArea.classList.add('highlight');
    }

    function unhighlight() {
        dropArea.classList.remove('highlight');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        handleFiles(files);
    }

    function handleFiles(files) {
    const file = files[0];
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file && file.type === 'image/png') {
        if (file.size > maxSize) {
            alert('El archivo es demasiado grande. El tamaño máximo permitido es 5MB.');
            return;
        }
        const reader = new FileReader();
        reader.onload = function (event) {
            imagePreview.innerHTML = `<img src="${event.target.result}" alt="Vista previa" class="img-fluid rounded mt-2" />`;
            imageStatus.style.display = 'block';
        };
        reader.readAsDataURL(file);
        imageUpload.files = files;
    } else {
        alert('Solo se permite archivos PNG.');
    }
}
</script>
<script>
        function toggleEstatus(id, nuevoEstatus) {
                $.ajax({
                    url: '{{ url("/planta/estatus") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        nuevoEstatus: nuevoEstatus
                    },
                    success: function(response) {
                        if (response.success) {
                            // Recargar el DataTable después de actualizar el estatus
                            $('#plantasTable').DataTable().ajax.reload();
                        } else {
                            console.error('Error al actualizar el estatus: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error al actualizar el estatus: ' + xhr.responseText);
                    }
                });
            }
    </script>
<script>
    function goBack() {
      window.history.back();
    }
</script>
@stop
